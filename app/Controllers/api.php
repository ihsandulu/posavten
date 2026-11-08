<?php

namespace App\Controllers;

use phpDocumentor\Reflection\Types\Null_;

class api extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }

    public function index()
    {
        echo "Page Not Found!";
    }


    // =====================================================
    // 1. MENGAMBIL ACCESS TOKEN DARI FIREBASE
    // =====================================================
    private function getFcmAccessToken()
    {
        $file = WRITEPATH . 'firebase/posavten-80a992b2e622.json';

        if (!file_exists($file)) {
            throw new \Exception('File Firebase tidak ditemukan: ' . $file);
        }

        $serviceAccount = json_decode(file_get_contents($file), true);

        if (
            !$serviceAccount ||
            empty($serviceAccount['client_email']) ||
            empty($serviceAccount['private_key']) ||
            empty($serviceAccount['project_id'])
        ) {
            throw new \Exception('Isi file Service Account tidak valid.');
        }

        $now = time();

        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        $payload = [
            'iss'   => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600
        ];

        $base64Url = function ($data) {
            return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        };

        $jwtHeader  = $base64Url(json_encode($header));
        $jwtPayload = $base64Url(json_encode($payload));

        $signatureInput = $jwtHeader . '.' . $jwtPayload;

        $privateKey = openssl_pkey_get_private(
            $serviceAccount['private_key']
        );

        if (!$privateKey) {
            throw new \Exception('Private key Firebase tidak valid.');
        }

        $signature = '';

        $signed = openssl_sign(
            $signatureInput,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        if (!$signed) {
            throw new \Exception('Gagal membuat tanda tangan JWT.');
        }

        $jwt = $signatureInput . '.' . $base64Url($signature);

        $ch = curl_init(
            'https://oauth2.googleapis.com/token'
        );

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            throw new \Exception('Gagal menghubungi Google: ' . $curlError);
        }

        $result = json_decode($response, true);

        if ($httpCode != 200 || empty($result['access_token'])) {
            throw new \Exception(
                'Gagal mendapatkan access token: ' . $response
            );
        }

        return [
            'access_token' => $result['access_token'],
            'project_id'   => $serviceAccount['project_id']
        ];
    }


    // =====================================================
    // 2. MENGIRIM PESAN KE FIREBASE CLOUD MESSAGING
    // =====================================================
    private function sendFcm($accessToken, $projectId, $deviceToken, $message, $pesanasli)
    {
        if (empty($deviceToken)) {
            return [
                'success' => false,
                'error' => 'Token perangkat kosong.'
            ];
        }

        $url = 'https://fcm.googleapis.com/v1/projects/'
            . $projectId
            . '/messages:send';

        $data = [
            'message' => [
                'token' => $deviceToken,

                'notification' => [
                    'title' => 'Notifikasi POSAVTEN',
                    'body'  => (string) $pesanasli
                ],

                'data' => [
                    'message' => (string) $message
                ]
            ]
        ];

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'error'   => $curlError
            ];
        }

        return [
            'success' => ($httpCode == 200),
            'http_code' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }





    public function simpan_token()
    {
        $tipe  = $this->request->getGet('tipe');
        $token = $this->request->getGet('token');
        $id    = $this->request->getGet('id');

        if ($tipe == "user") {

            $inputuser = [
                'user_token' => $token
            ];

            $this->db->table('user')
                ->where('user_id', $id)
                ->update($inputuser);
        } else {

            $inputuser = [
                'member_token' => $token
            ];

            $this->db->table('member')
                ->where('member_id', $id)
                ->update($inputuser);
        }

        return $this->response->setBody($tipe);
    }

    public function simpanpesanan()
    {
        $input["member_id"] = $this->request->getGet("member_id");
        $input["pesanan_nopesanan"] = $this->request->getGet("pesanan_nopesanan");
        $input["product_id"] = $this->request->getGet("product_id");
        $input["pesanan_alamat"] = $this->request->getGet("pesanan_alamat");
        if ($this->db->table('pesanan')->insert($input)) {
            echo "insert";
        } else {
            echo "gagal";
        }
    }

    public function listpesananandroid()
    {
        $builder = $this->db
            ->table("pesanan")
            ->join("member", "member.member_id=pesanan.member_id", "left")
            ->where("pesanan.store_id", 1)
            ->where("pesanan.pesanan_accept", 0);
        if (isset($_GET["from"]) && $_GET["from"] != "") {
            $builder->where("pesanan.pesanan_date >=", $this->request->getGet("from"));
        } else {
            $builder->where("pesanan.pesanan_date", date("Y-m-d"));
        }
        if (isset($_GET["to"]) && $_GET["to"] != "") {
            $builder->where("pesanan.pesanan_date <=", $this->request->getGet("to"));
        } else {
            $builder->where("pesanan.pesanan_date", date("Y-m-d"));
        }
        $usr = $builder
            ->orderBy("pesanan.pesanan_accept", "ASC")
            ->orderBy("pesanan.pesanan_id", "DESC")
            ->get();
        // echo $this->db->getLastquery();die;
        $no = 1;
        $status = array("Proses", "Selesai");
        foreach ($usr->getResult() as $usr) {
?>
            <div class="card mb-3 border shadow-sm">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <strong>Pesanan <?= $usr->pesanan_nopesanan; ?></strong>
                        <span class="badge bg-warning text-dark">
                            Menunggu
                        </span>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <small class="text-muted">Nama Pemesan</small>
                        <div class="fw-bold"><?= $usr->member_name; ?></div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Alamat Pengantaran</small>
                        <div>
                            <?= $usr->pesanan_alamat; ?>
                        </div>
                    </div>

                    <button type="button"
                        class="btn btn-success w-100 btnSelesaikan"
                        data-id="<?= esc($usr->pesanan_id); ?>">

                        <i class="fas fa-check-circle me-2"></i>
                        Selesaikan
                    </button>
                </div>
            </div>
        <?php }
    }


    public function kirimnotif()
    {
        $tipe  = $this->request->getGet("tipe");
        $code  = $this->request->getGet("code");
        $pesan = $this->request->getGet("pesan");

        try {

            // Ambil access token Firebase satu kali
            $fcm = $this->getFcmAccessToken();

            $accessToken = $fcm['access_token'];
            $projectId   = $fcm['project_id'];

            $berhasil = 0;
            $gagal = 0;

            if ($tipe == "user") {

                // MULAI KIRIM PESAN KE USER
                $login = $this->db->table("login")
                    ->select("user.user_token, user.user_email")
                    ->join("user", "user.user_id = login.user_id", "left")
                    ->join(
                        "(SELECT user_id, MAX(login_datetime) AS last_login
                    FROM login
                    WHERE login_date = '" . date("Y-m-d") . "'
                    GROUP BY user_id) latest",
                        "latest.user_id = login.user_id
                    AND latest.last_login = login.login_datetime"
                    )
                    ->where("login.login_status", 0)
                    ->where("login.login_date", date("Y-m-d"))
                    ->get();

                foreach ($login->getResult() as $row) {

                    $token = $row->user_token;
                    $email = $row->user_email;

                    $inputpesan = [
                        "pesan_email" => $email,
                        "pesan_code"  => $code,
                        "pesan_tipe"  => $tipe,
                        "pesan_isi"   => $pesan,
                        "pesan_token" => $token
                    ];

                    $this->db->table("pesan")->insert($inputpesan);
                    $pesan_id = $this->db->insertID();

                    $message = $code . '|' . $pesan_id . '|' . $email . '|' . $tipe . '|' . $pesan . '|' . $token;

                    // KIRIM LANGSUNG KE FCM
                    $hasil = $this->sendFcm(
                        $accessToken,
                        $projectId,
                        $token,
                        $message,
                        $pesan
                    );

                    if ($hasil['success']) {
                        $berhasil++;
                    } else {
                        $gagal++;
                        log_message('error', 'FCM gagal untuk ' . $email . ': ' . json_encode($hasil));
                    }
                }

                return $this->response->setJSON([
                    'status'       => 'success',
                    'message'      => 'Pesanan diproses.',
                    'notifkeuser'  => $berhasil,
                    'gagal'        => $gagal
                ]);
            }

            if ($tipe == "member") {

                $member_id = $this->request->getGet("id");

                $member = $this->db->table("member")
                    ->where("member_id", $member_id)
                    ->get();

                foreach ($member->getResult() as $row) {

                    $email = $row->member_email;
                    $token = $row->member_token;

                    $inputpesan = [
                        "pesan_email" => $email,
                        "pesan_code"  => $code,
                        "pesan_tipe"  => $tipe,
                        "pesan_isi"   => $pesan,
                        "pesan_token" => $token
                    ];

                    $this->db->table("pesan")->insert($inputpesan);
                    $pesan_id = $this->db->insertID();

                    $message = $code . '|' . $pesan_id . '|' . $email . '|' . $tipe . '|' . $pesan . '|' . $token;

                    // KIRIM LANGSUNG KE FCM
                    $hasil = $this->sendFcm(
                        $accessToken,
                        $projectId,
                        $token,
                        $message,
                        $pesan
                    );

                    if ($hasil['success']) {
                        $berhasil++;
                    } else {
                        $gagal++;
                        log_message('error', 'FCM gagal untuk ' . $email . ': ' . json_encode($hasil));
                    }
                }

                return $this->response->setJSON([
                    'status'   => 'success',
                    'message'  => 'Pesan diproses.',
                    'notifkemember' => $berhasil,
                    'gagal'    => $gagal
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tipe penerima tidak dikenal.'
            ]);
        } catch (\Throwable $e) {

            log_message('error', 'Kirim FCM: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function listproductandroid()
    {

        $builder = $this->db->table("product")
            ->join("(SELECT sell_percent, sell_price, positionm_id, product_id AS prodid FROM sell)sell", "sell.prodid=product.product_id AND sell.positionm_id=9", "left");
        if ($this->request->getGet("product_name") != "") {
            $builder->like("product.product_name", $this->request->getGet("product_name"), "BOTH");
        }
        $builder->where("product.store_id", 1);
        $product = $builder->orderBy("product_name")
            ->get();
        // echo $this->db->getLastQuery();
        foreach ($product->getResult() as $product) {
            if ($product->product_picture == "") {
                $gambar = "no_image.png";
            } else {
                $gambar = $product->product_picture;
            }
            $sell = $product->sell_price;
            if ($sell == "") {
                $sell = 0;
            }
        ?>
            <div class="col-4"
                data-id="<?= esc($product->product_id); ?>"
                data-nama="<?= esc($product->product_name); ?>"
                onclick="pilihproduk(this)">
                <div class="card border h-100 position-relative overflow-hidden">

                    <img src="<?= base_url("images/product_picture/" . $gambar); ?>"
                        class="card-img-top"
                        style="aspect-ratio:1/1;object-fit:cover">

                    <!-- NAMA DAN HARGA DI TENGAH GAMBAR -->
                    <div class="position-absolute top-50 start-50 translate-middle text-center w-100 px-1"
                        style="background:rgba(255,255,255,0.90); padding:8px 4px;">

                        <div class="small fw-bold text-dark">
                            <?= $product->product_name; ?>
                        </div>

                        <div class="small fw-bold text-dark">
                            Rp <?= number_format($sell, 0, ",", "."); ?>
                        </div>

                    </div>

                </div>
            </div>
        <?php }
    }

    public function register()
    {
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setContentType('application/json');

        $username = $this->request->getGet('username');
        $email = $this->request->getGet('email');
        $passwordInput = $this->request->getGet('password');

        $response = [];

        $email = $this->request->getGet('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Format email tidak valid!'
            ]);
        }

        if ($username && $passwordInput && $email) {

            // Cek tabel member berdasarkan username
            $member = $this->db->table('member')
                ->where('member_email', $email)
                ->get()
                ->getRow();

            if ($member) {
                $response = [
                    'status'  => 'fail',
                    'message' => 'Member sudah terdaftar!'
                ];
            } else {

                $inputu = [];

                $inputu["member_name"] = $username;
                $inputu["member_email"] = $email;

                // Member
                $inputu["store_id"] = 1;
                $inputu["member_password"] = password_hash(
                    $passwordInput,
                    PASSWORD_DEFAULT
                );

                $inputu["member_no"] = "M" . date("ymdHis");
                $inputu["positionm_id"] = 8;

                $this->db->table('member')->insert($inputu);

                $memberId = $this->db->insertID();

                // Ambil data member yang baru disimpan
                $member = $this->db->table('member')
                    ->where('member_id', $memberId)
                    ->get()
                    ->getRow();

                $response = [
                    'tipe'     => 'member',
                    'status'   => 'success',
                    'id'       => $member->member_id,
                    'username' => $member->member_name,
                    'email'    => $member->member_email,
                    'token'    => $member->member_token,
                    'message'  => 'Register Berhasil!'
                ];
            }
        } else {

            $response = [
                'status'  => 'fail',
                'message' => 'Register Tidak Valid!'
            ];
        }

        return $this->response->setJSON($response);
    }

    public function login()
    {
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setContentType('application/json');

        $username      = $this->request->getGet('username');
        $passwordInput = $this->request->getGet('password');

        $response = [];

        if ($username && $passwordInput) {

            // Cek tabel member
            $member = $this->db->table('member')
                ->where('member_email', $username)
                ->get()
                ->getRow();

            if ($member) {

                if (password_verify($passwordInput, $member->member_password)) {

                    $response = [
                        'tipe'     => 'member',
                        'status'   => 'success',
                        'id'       => $member->member_id,
                        'username' => $member->member_name,
                        'email'    => $member->member_email,
                        'token'    => $member->member_token,
                        'message'  => 'Login Berhasil!'
                    ];
                } else {

                    $response = [
                        'status'  => 'fail',
                        'message' => 'Password Member Salah!'
                    ];
                }
            } else {

                // Jika bukan member, cek tabel user
                $user = $this->db->table('user')
                    ->where('user_email', $username)
                    ->get()
                    ->getRow();

                if ($user) {

                    if (password_verify($passwordInput, $user->user_password)) {

                        $response = [
                            'tipe'     => 'user',
                            'status'   => 'success',
                            'id'       => $user->user_id,
                            'username' => $user->user_name,
                            'email'    => $user->user_email,
                            'token'    => $user->user_token,
                            'message'  => 'Login Berhasil!'
                        ];
                    } else {

                        $response = [
                            'status'  => 'fail',
                            'message' => 'Password User Salah!'
                        ];
                    }
                } else {

                    $response = [
                        'status'  => 'fail',
                        'message' => 'Email Salah!'
                    ];
                }
            }
        } else {

            $response = [
                'status'  => 'fail',
                'message' => 'Login Tidak Valid!'
            ];
        }

        return $this->response->setJSON($response);
    }
    public function active()
    {
        $input["store_active"] = $this->request->getGET("store_active");
        $this->db->table('store')->update($input, array("store_id" => $this->request->getGET("store_id")));
        echo $this->db->getLastQuery();
    }

    public function isibatch()
    {
        $where["product_batch"] = $this->request->getGet("product_batch"); // lebih aman dari $_GET
        $product = $this->db->table("product")->where($where)->get();

        $data = [
            "product_id" => 0,
            "product_name" => "",
            "product_buy" => 0,
            "product_ube" => "",
            "stockopname_awal" => 0,
            "product_expiredate" => ""
        ];

        foreach ($product->getResult() as $row) {
            $data = [
                "product_id" => $row->product_id,
                "product_name" => $row->product_name,
                "product_buy" => $row->product_buy,
                "product_ube" => $row->product_ube,
                "product_stock" => $row->product_stock,
                "product_expiredate" => $row->product_expiredate
            ];
        }

        return $this->response->setJSON($data);
    }

    public function unitsecond()
    {
        $transactiond_unit = $this->request->getGet("transactiond_unit");
        ?>
        <option value="0" <?= ($transactiond_unit == "0") ? "selected" : ""; ?>>Unit</option>
        <?php
        $unit = $this->db->table("unit")
            ->where("unit_id", $this->request->getGet("unit_id"))
            ->get();
        //echo $this->db->getLastQuery();
        ?>
        <?php
        foreach ($unit->getResult() as $unit) { ?>
            <option value="<?= $unit->unit_id; ?>" <?= ($transactiond_unit == $unit->unit_id) ? "selected" : ""; ?>>
                <?= $unit->unit_name; ?>
            </option>
        <?php }
        $units = $this->db->table("unit")
            ->where("unit_id", $this->request->getGet("product_unitsec"))
            ->get();
        //echo $this->db->getLastQuery();
        ?>
        <?php
        foreach ($units->getResult() as $units) { ?>
            <option value="<?= $units->unit_id; ?>" <?= ($transactiond_unit == $units->unit_id) ? "selected" : ""; ?>>
                <?= $units->unit_name; ?>
            </option>
        <?php }
    }


    public function caribatch()
    {
        $where["product_batch"] = $_GET["product_batch"];
        $product = $this->db->table("product")->where($where)->get();
        $product_id = "";
        foreach ($product->getResult() as $row) {
            $product_id = $row->product_id;
        }
        echo $product_id;
    }

    public function ilembur()
    {
        $lembur_jam = $this->request->getGET("lembur_jam");
        $store = $this->db->table('store')->where("store_id", $this->request->getGET("store_id"))->get();
        $lemburupah = 0;
        foreach ($store->getResult() as $row) {
            $lemburupah = $lembur_jam * $row->store_lemburupah;
        }
        echo $lemburupah;
    }

    public function iabsen()
    {
        $absen_status = $this->request->getGET("absen_status");
        $store = $this->db->table('store')->where("store_id", $this->request->getGET("store_id"))->get();
        $potonganabsen = 0;
        foreach ($store->getResult() as $row) {
            if ($absen_status == 1) {
                $potonganabsen = $row->store_psakit;
            }
            if ($absen_status == 2) {
                $potonganabsen = $row->store_pizin;
            }
            if ($absen_status == 3) {
                $potonganabsen = $row->store_pcuti;
            }
            if ($absen_status == 4) {
                $potonganabsen = $row->store_palpha;
            }
        }
        echo $potonganabsen;
    }

    public function compressFolder()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1); // tampilkan error di layar

        $src = $this->request->getGet("src"); // pastikan param src dikirim via URL
        if (!$src) {
            echo "❌ Parameter src tidak ditemukan.";
            return;
        }

        $folderPath = FCPATH . 'images/' . $src . '/'; // tambahkan slash di akhir

        if (!is_dir($folderPath)) {
            echo "❌ Folder tidak ditemukan: $folderPath";
            return;
        }

        $quality = 70; // Kualitas kompres, 1–100 (semakin kecil, semakin ringan)
        $maxWidth = 300;

        $files = scandir($folderPath);
        if (!$files) {
            echo "❌ Gagal membaca folder: $folderPath";
            return;
        }

        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) continue;

            $filePath = $folderPath . $file;

            if (!file_exists($filePath)) {
                echo "❌ File tidak ditemukan: $filePath<br>";
                continue;
            }

            $imageType = @exif_imagetype($filePath);
            if (!in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
                echo "⏩ Skip non-gambar: $file<br>";
                continue;
            }

            list($originalWidth, $originalHeight) = getimagesize($filePath);

            // Buat resource gambar
            if ($imageType == IMAGETYPE_JPEG) {
                $image = imagecreatefromjpeg($filePath);
            } elseif ($imageType == IMAGETYPE_PNG) {
                $image = imagecreatefrompng($filePath);
            } else {
                continue;
            }

            if ($originalWidth > $maxWidth) {
                $ratio = $maxWidth / $originalWidth;
                $newWidth = $maxWidth;
                $newHeight = $originalHeight * $ratio;

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            } else {
                $resized = $image;
            }

            // Simpan ulang gambar (replace)
            if ($imageType == IMAGETYPE_JPEG) {
                imagejpeg($resized, $filePath, $quality);
            } elseif ($imageType == IMAGETYPE_PNG) {
                $png_quality = (int)((100 - $quality) / 10);
                imagepng($resized, $filePath, $png_quality);
            }

            // Bersihkan memori
            imagedestroy($image);
            if ($resized !== $image) {
                imagedestroy($resized);
            }

            echo "✔ Gambar dikompres: $file<br>";
        }
    }


    public function tampilgambartercompress()
    {
        /* <img src="<?= base_url("api/tampilgambartercompress?src=images/product_picture/15_16_19_download_(18).jpg&q=60"); ?>" alt="Gambar Produk" class="img-fluid"> */
        //parameter q adalah tingkat persen dari compress yang kita mau

        $path = isset($_GET['src']) ? $_GET['src'] : '';
        $quality = isset($_GET['q']) ? intval($_GET['q']) : 70; // default 70%

        // Pastikan path aman
        $gambar = $_GET['src'];
        $filepath = $_SERVER['DOCUMENT_ROOT'] . "/apotekinsani/public/" . $gambar;

        if (!file_exists($filepath)) {
            http_response_code(404);
            exit("File not found.");
        }

        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        // Header dan kompresi sesuai jenis file
        if ($ext === 'jpg' || $ext === 'jpeg') {
            header("Content-Type: image/jpeg");
            $image = imagecreatefromjpeg($filepath);
            imagejpeg($image, null, $quality); // Kompres kualitas
            imagedestroy($image);
        } elseif ($ext === 'png') {
            header("Content-Type: image/png");
            $image = imagecreatefrompng($filepath);
            // Kompres PNG: kualitas 0 (terbaik), 9 (terkompres)
            $png_quality = (int) round(($quality / 100) * 9);
            imagepng($image, null, $png_quality);
            imagedestroy($image);
        } else {
            http_response_code(415);
            exit("Unsupported image type.");
        }
    }

    public function bukagambar()
    { ?>
        <img src="<?= base_url("api/tampilgambartercompress?src=images/product_picture/15_16_19_download_(18).jpg&q=60"); ?>" alt="Gambar Produk" class="img-fluid">
        <?php }

    public function ibatch()
    {
        $where["product_id"] = $this->request->getGET("product_id");
        $input["product_batch"] = $this->request->getGET("product_batch");
        $this->db->table('product')->where($where)->update($input);
        echo $this->db->getLastQuery();
    }

    public function createstore()
    {
        //input store 
        $input["store_name"] = $this->request->getGET("store_name");
        $input["store_address"] = $this->request->getGET("store_address");
        $input["store_phone"] = $this->request->getGET("store_phone");
        $input["store_wa"] = $this->request->getGET("store_wa");
        $input["store_owner"] = $this->request->getGET("store_owner");
        $input["store_active"] = $this->request->getGET("store_active");
        $this->db->table('store')->insert($input);
        // echo $this->db->getLastQuery();
        $userid = $this->db->insertID();

        //input position
        $inputposition1["store_id"] = $userid;
        $inputposition1["position_name"] = "Admin";
        $inputposition2["position_administrator"] = 2;
        $this->db->table('position')->insert($inputposition1);
        $positionid1 = $this->db->insertID();
        //input position
        $inputposition2["store_id"] = $userid;
        $inputposition2["position_administrator"] = 1;
        $inputposition2["position_name"] = "Administrator";
        $this->db->table('position')->insert($inputposition2);
        $positionid2 = $this->db->insertID();

        //input user
        $inputuser1["store_id"] = $userid;
        $inputuser1["user_name"] = $this->request->getGET("user_name");
        $inputuser1["user_email "] = $this->request->getGET("user_email ");
        $inputuser1["user_password"] = password_hash($this->request->getGET("user_password"), PASSWORD_DEFAULT);
        $inputuser1["position_id"] = $positionid1;
        $this->db->table('user')->insert($inputuser1);

        //input user administrator
        $inputuser2["store_id"] = $userid;
        $inputuser2["user_name"] = "Administrator";
        $inputuser2["user_email "] = "ihsan.dulu@gmail.com";
        $inputuser2["user_password"] = "$2y$10$GjtRux7LHXpXN5JotL/J0uE1KyV5LQ.OQrapMZqbhHt84oB7WDoEa";
        $inputuser2["position_id"] = $positionid2;
        $this->db->table('user')->insert($inputuser2);
        echo $this->db->getLastQuery();
    }

    public function iswritable()
    {
        $dir = $_GET["path"];
        if (is_dir($dir)) {
            if (is_writable($dir)) {
                echo "true";
            } else {
                echo "false";
            }
        } else if (file_exists($dir)) {
            return (is_writable($dir));
        }
    }

    public function updateakun()
    {
        $input["akun_id"] = $this->request->getGET("akun_id");
        if ($input["akun_id"] == 0) {
            $input["akun_id"] = null;
        }
        $this->db->table('lpj1')->update($input, array("lpj1_id" => $this->request->getGET("lpj1_id")));
        // echo $this->db->getLastQuery();
        $akun = $this->db->table("akun")
            ->where("akun.akun_id", $input["akun_id"])
            ->get();
        foreach ($akun->getResult() as $akun) {
            if ($input["akun_id"] > 0) {
                $isi = $akun->akun_name . " (" . $akun->akun_no . ")";
            } else {
                $isi = "";
            }
            echo $isi;
        }
    }

    public function akunkosong()
    {
        $akunkosong = $this->db->table("lpj1")
            ->where("lpj0_id", $this->request->getGET("lpj0_id"))
            ->groupStart()
            ->where("akun_id", null)
            ->orWhere("akun_id", "0")
            ->groupEnd()
            ->get()
            ->getNumRows();
        // echo $this->db->getLastQuery();
        if ($akunkosong == 0) {
            echo "OK";
        }
    }

    public function hakakses()
    {
        $crud = $this->request->getGET("crud");
        $val = $this->request->getGET("val");
        $val = json_decode($val);
        $position_id = $this->request->getGET("position_id");
        $pages_id = $this->request->getGET("pages_id");
        $where["position_id"] = $this->request->getGET("position_id");
        $where["pages_id"] = $this->request->getGET("pages_id");
        $cek = $this->db->table('positionpages')->where($where)->get()->getNumRows();
        if ($cek > 0) {
            $input1[$crud] = $val;
            $this->db->table('positionpages')->update($input1, $where);
            echo $this->db->getLastQuery();
        } else {
            $input2["position_id"] = $position_id;
            $input2["pages_id"] = $pages_id;
            $input2[$crud] = $val;
            $this->db->table('positionpages')->insert($input2);
            echo $this->db->getLastQuery();
        }
    }

    public function cekcost_approver()
    {

        $build = $this->db->table("cost_approver");
        if (isset($_GET["branch_id"])) {
            $build->where("branch_id", $this->request->getGet("branch_id"));
        }
        $usr = $build->get();
        echo $usr->getNumRows();
    }

    public function request0buttons()
    {
        $request0 = $this->db->table("request0")
            ->select("request0.request0_id, request0.request0_no, SUM(request1.request1_proposed_nom)AS nominal")
            ->join("request1", "request1.request0_id=request0.request0_id", "left")
            ->join("advance0", "advance0.request0_id=request0.request0_id", "left")
            ->whereIn("request0.branch_id", session()->branchrule, true)
            ->groupStart()
            ->where("advance0.request0_id IS NULL")
            ->orWhere("advance0.request0_id", $this->request->getGet("request0_id"))
            ->groupEnd()
            ->where("request0.request0_status", "Validated")
            ->groupBy("request1.request0_id")
            ->groupBy("request0.request0_id")
            ->get();
        //echo $this->db->getLastQuery();
        foreach ($request0->getResult() as $request0) { ?>
            <div class="col-md-2 p-2">
                <button id="b<?= $request0->request0_id; ?>" type="button" onclick="buka('<?= $request0->request0_id; ?>')" class="btn btn-warning pilihb">
                    <?= $request0->request0_no; ?> (Rp. <?= number_format($request0->nominal, 0, ",", "."); ?>)
                    <button onclick="batal()" id="bc<?= $request0->request0_id; ?>" type="button" class="btn btn-xs btn-danger fa fa-close btnclose" style="position: absolute; top:-3px; right:-5px;"></button>
                </button>
            </div>
        <?php }
    }

    public function request0detail()
    {
        ?>
        <div class="tabale-responsive m-t-40">
            <table id="example231" class="display nowrap table table-hover  table-bordered" cellspacing="0" width="100%">
                <!-- 
                                            <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                <thead class="">
                    <tr style="background-color:peachpuff;">
                        <th>Date of Filing</th>
                        <th>No.Req</th>
                        <th>Branch</th>
                        <th>Applicant</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $builder = $this->db
                        ->table("request0");

                    $builder->where("request0_id", $this->request->getGet("request0_id"));
                    $usr = $builder
                        ->join("user", "user.user_id=request0.user_id_created", "left")
                        ->join("branch", "branch.branch_id=request0.branch_id", "left")
                        ->orderBy("request0_id", "desc")
                        ->get();
                    //echo $this->db->getLastquery();
                    $no = 1;
                    foreach ($usr->getResult() as $usr) {
                    ?>
                        <tr id="d<?= $usr->request0_id; ?>">
                            <td><?= $usr->request0_date; ?></td>
                            <td><?= $usr->request0_no; ?></td>
                            <td><?= $usr->branch_name; ?></td>
                            <td><?= $usr->username; ?></td>
                            <td><?= $usr->request0_desc; ?></td>
                            <td><?= $usr->request0_status; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="table-responsive m-t-40">
            <table id="example231" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <!-- 
                                            <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                <thead class="">
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>Cost</th>
                        <th>Proposed</th>
                        <th>Approved</th>
                        <th>Approved By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usr = $this->db
                        ->table("request1")
                        ->join("cost", "cost.cost_id=request1.cost_id", "left")
                        ->where("request0_id", $this->request->getVar("request0_id"))
                        ->orderBy("request1_id", "desc")
                        ->get();
                    //echo $this->db->getLastquery();
                    $no = 1;
                    foreach ($usr->getResult() as $usr) {
                        $usr1 = $this->db
                            ->table("user")
                            ->join("contact", "contact.contact_id=user.contact_id", "left")
                            ->where("user_id", $usr->user_id_approved)
                            ->get();
                        /* echo $this->db->getLastquery();
                                        die; */
                        $user_name = "";
                        if ($usr1->getNumRows() > 0) {
                            $usr1 = $usr1->getRow();
                            $user_name = $usr1->contact_first_name;
                        }

                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $usr->created; ?></td>
                            <td><?= $usr->cost_name; ?></td>
                            <td><?= number_format($usr->request1_proposed_nom, 0, ",", "."); ?></td>
                            <td><?= number_format($usr->request1_approved_nom, 0, ",", "."); ?></td>
                            <td><?= ($user_name == "") ? "" : $user_name . " (" . $usr->approved . ")"; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php
    }

    public function advance0detail()
    {
    ?>
        <div class="tabale-responsive m-t-40">
            <table id="example231" class="display nowrap table table-hover  table-bordered" cellspacing="0" width="100%">
                <!-- 
                                            <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                <thead class="">
                    <tr style="background-color:peachpuff;">
                        <th>Action</th>
                        <th>Date of Filing</th>
                        <th>No.Advance</th>
                        <th>No.LPJ</th>
                        <th>Branch</th>
                        <th>Applicant</th>
                        <th>Total Nominal</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $builder = $this->db
                        ->table("advance0");
                    $builder->where("advance0.advance0_id", $this->request->getGet("advance0_id"));
                    $usr = $builder
                        ->select("*, advance0.advance0_id AS advance0_id, advance0.user_id_approved AS user_id_approved, advance0.approved AS approved")
                        ->join("(SELECT advance0_id AS advance0d_id, SUM(advance1_nom)AS dnom FROM advance1 GROUP BY advance0_id)advance1", "advance1.advance0d_id=advance0.advance0_id", "left")
                        ->join("user", "user.user_id=advance0.user_id_created", "left")
                        ->join("request0", "request0.request0_id=advance0.request0_id", "left")
                        ->join("(SELECT request0_id AS request0d_id,SUM(request1_proposed_nom)AS proposenom, SUM(request1_approved_nom)AS approvenom FROM request1 GROUP BY request0_id)request1", "request1.request0d_id=request0.request0_id", "left")
                        ->join("branch", "branch.branch_id=request0.branch_id", "left")
                        ->join("lpj0", "lpj0.advance0_id=advance0.advance0_id", "left")
                        ->orderBy("advance0.advance0_id", "desc")
                        ->get();
                    //echo $this->db->getLastquery();
                    $no = 1;
                    foreach ($usr->getResult() as $usr) {
                    ?>
                        <tr id="d<?= $usr->advance0_id; ?>">
                            <td style="padding-left:0px; padding-right:0px;">
                                <form method="post" class="btn-action" style="">
                                    <a href="#" onclick="tampilrow(this,'d<?= $usr->advance0_id; ?>','9', ['Advance Approved Date','Request Number','Request Proposed', 'Request Approved'], ['<?= $usr->approved; ?>', '<?= $usr->request0_no; ?>', '<?= number_format($usr->proposenom, 0, ",", "."); ?>', '<?= number_format($usr->approvenom, 0, ",", "."); ?>'])" class="btn btn-sm btn-success tampilrow">
                                        <span class="fa fa-plus" style="color:white;"></span>
                                    </a>
                                </form>
                            </td>
                            <td><?= $usr->advance0_date; ?></td>
                            <td><?= $usr->advance0_no; ?></td>
                            <td><?= $usr->lpj0_no; ?></td>
                            <td><?= $usr->branch_name; ?></td>
                            <td><?= $usr->username; ?></td>
                            <td><?= number_format($usr->advance0_nom, 0, ",", "."); ?></td>
                            <td><?= $usr->advance0_desc; ?></td>
                            <td><?= $usr->advance0_status; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="table-responsive m-t-40">
            <table id="example231" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <!-- 
                                            <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                <thead class="">
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>Cash</th>
                        <th>Nominal</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usr = $this->db
                        ->table("advance1")
                        ->join("cash", "cash.cash_id=advance1.cash_id", "left")
                        ->where("advance0_id", $this->request->getVar("advance0_id"))
                        ->orderBy("advance1_id", "desc")
                        ->get();
                    //echo $this->db->getLastquery();
                    $no = 1;
                    foreach ($usr->getResult() as $usr) {
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date("Y-m-d", strtotime($usr->created)); ?></td>
                            <td><?= $usr->cash_name; ?></td>
                            <td><?= number_format($usr->advance1_nom, 0, ",", "."); ?></td>
                            <td><?= $usr->advance1_bukti; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php
    }

    public function listprepaid_prepaidpayment()
    {
        $prepaid_id = $this->request->getGET("prepaid_id");
        $prepaid = $this->db->table("prepaid")
            ->where("branch_id", $this->request->getGET("branch_id"))
            ->get();
        // echo $this->db->getLastQuery();
    ?>
        <option value="0" <?= ($prepaid_id == "0") ? "selected" : ""; ?>>Select Prepaid</option>
        <?php
        foreach ($prepaid->getResult() as $prepaid) { ?>
            <option value="<?= $prepaid->prepaid_id; ?>" <?= ($prepaid_id == $prepaid->prepaid_id) ? "selected" : ""; ?>><?= $prepaid->prepaid_name; ?></option>
        <?php } ?>
    <?php }

    public function listcash_prepaidpayment()
    {
        $cash_id = $this->request->getGET("cash_id");
        $cash = $this->db->table("cash")
            ->where("branch_id", $this->request->getGET("branch_id"))
            ->get();
        echo $this->db->getLastQuery();
    ?>
        <option value="0" <?= ($cash_id == "0") ? "selected" : ""; ?>>Select Cash</option>
        <?php
        foreach ($cash->getResult() as $cash) { ?>
            <option value="<?= $cash->cash_id; ?>" <?= ($cash_id == $cash->cash_id) ? "selected" : ""; ?>><?= $cash->cash_name; ?></option>
        <?php } ?>
<?php }
}
