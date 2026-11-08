<?php

namespace App\Models\transaction;

use App\Models\core_m;

class pesanan_m extends core_m
{
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

        //update
        if ($this->request->getPost("change") == "OK") {
            $input["pesanan_accept"] = $this->request->getPost("pesanan_accept");
            $input["store_id"] = session()->get("store_id");
            $this->db->table('pesanan')->update($input, array("pesanan_id" => $this->request->getPost("pesanan_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;   

            //kirim notif ke member
            $tipe  = "member";
            $code  = 0;
            $pesan = "Pesanan anda akan diantar. Harap siapkan uang pas!";
            $member_id = $this->request->getPost("member_id");
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

                $url = "https://qithy.my.id:8000/broadcast/TRP-20241010-01?kirim=&message=" . urlencode($message);

                $data["message"] = @file_get_contents($url);

                // echo $response;

                if ($data["message"] === false) {
                    $data["message"] = "Gagal kirim notif";

                }
            }
        }
        return $data;
    }
}
