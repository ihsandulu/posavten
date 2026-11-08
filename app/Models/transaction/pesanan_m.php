<?php

namespace App\Models\transaction;

use App\Models\core_m;

class pesanan_m extends core_m
{

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
    private function sendFcm($accessToken, $projectId, $deviceToken, $message)
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
                    'body'  => (string) $message
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

    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek pesanan
        if ($this->request->getVar("pesanan_id")) {
            $pesanan["pesanan_id"] = $this->request->getVar("pesanan_id");
        } else {
            $pesanan["pesanan_id"] = -1;
        }
        $pesanan["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("pesanan")
            ->getWhere($pesanan);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "action", "data", "pesanan_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $pesanan) {
                foreach ($this->db->getFieldNames('pesanan') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $pesanan->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('pesanan') as $field) {
                $data[$field] = "";
            }
            $data["pesanan_date"] = date("Y-m-d");
        }




        //delete
        if ($this->request->getPost("delete") == "OK") {
            $pesanan_id =   $this->request->getPost("pesanan_id");
            $this->db
                ->table("pesanan")
                ->delete(array("pesanan_id" => $this->request->getPost("pesanan_id"), "store_id" => session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'pesanan_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $builder = $this->db->table('pesanan');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $pesanan_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;


        // update
        if ($this->request->getPost("change") == "OK") {

            $pesanan_id = $this->request->getPost("pesanan_id");
            $pesanan_accept = $this->request->getPost("pesanan_accept");

            $input["pesanan_accept"] = $pesanan_accept;
            $input["store_id"] = session()->get("store_id");

            $this->db->table('pesanan')->update(
                $input,
                [
                    "pesanan_id" => $pesanan_id,
                    "store_id"   => session()->get("store_id")
                ]
            );

            $data["message"] = "Update Success";

            // =========================================
            // KIRIM NOTIFIKASI KE MEMBER MELALUI FCM
            // =========================================

            // Notifikasi hanya dikirim ketika pesanan selesai
            if ((int) $pesanan_accept == 1) {

                $tipe  = "member";
                $code  = 0;
                $pesan = "Pesanan anda akan diantar. Harap siapkan uang pas!";

                $member_id = $this->request->getPost("member_id");

                $member = $this->db->table("member")
                    ->where("member_id", $member_id)
                    ->get();

                if ($member->getNumRows() > 0) {

                    try {

                        // Ambil access token Firebase
                        $fcm = $this->getFcmAccessToken();

                        foreach ($member->getResult() as $row) {

                            $email = $row->member_email;
                            $token = $row->member_token;

                            if (empty($token)) {
                                $data["notif"] = "Token member kosong.";
                                continue;
                            }

                            // Simpan pesan ke database
                            $inputpesan = [
                                "pesan_email" => $email,
                                "pesan_code"  => $code,
                                "pesan_tipe"  => $tipe,
                                "pesan_isi"   => $pesan,
                                "pesan_token" => $token
                            ];

                            $this->db->table("pesan")->insert($inputpesan);

                            $pesan_id = $this->db->insertID();

                            // Format pesan enam bagian
                            $message = $code . '|'
                                . $pesan_id . '|'
                                . $email . '|'
                                . $tipe . '|'
                                . $pesan . '|'
                                . $token;

                            // Kirim langsung ke FCM
                            $hasil = $this->sendFcm(
                                $fcm['access_token'],
                                $fcm['project_id'],
                                $token,
                                $message
                            );

                            if ($hasil['success']) {

                                $data["notif"] = "Berhasil mengirim notifikasi ke member.";
                            } else {

                                $data["notif"] = "Gagal mengirim notifikasi ke member.";

                                log_message(
                                    'error',
                                    'FCM member gagal: ' . json_encode($hasil)
                                );
                            }
                        }
                    } catch (\Throwable $e) {

                        $data["notif"] = "Gagal mengirim notifikasi ke member.";

                        log_message(
                            'error',
                            'FCM member: ' . $e->getMessage()
                        );
                    }
                } else {

                    $data["notif"] = "Member tidak ditemukan.";
                }
            }
        }

        return $data;
    }
}
