<?php

namespace App\Models\transaction;

use App\Models\core_m;

class transactionpb_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek cicilan
        if ($this->request->getVar("cicilan_id")) {
            $ciciland["cicilan_id"] = $this->request->getVar("cicilan_id");
        } else {
            $ciciland["cicilan_id"] = -1;
        }
        $us = $this->db
            ->table("cicilan")
            ->getWhere($ciciland);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "cicilan_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $cicilan) {
                foreach ($this->db->getFieldNames('cicilan') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $cicilan->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('cicilan') as $field) {
                $data[$field] = "";
            }
        }
        $data["transaction_id"] = $this->request->getGet("transaction_id");

        $positionm = $this->db->table("positionm")
            ->where("positionm_default", "1")
            ->get();
        $positionm_id = 0;
        foreach ($positionm->getResult() as $row) {
            $positionm_id = $row->positionm_id;
        }

        $member = $this->db->table("member")->where("member_id", $this->request->getGet("member_id"))->get();
        foreach ($member->getResult() as $row) {
            $positionm_id = $row->positionm_id;
        }
        $data["positionm_id"] = $positionm_id;



        //delete
        if ($this->request->getPost("delete") == "OK") {
            $this->db
                ->table("cicilan")
                ->delete(array("cicilan_id" => $this->request->getPost("cicilan_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'cicilan_id' && $e != 'cicilanqty') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $builder = $this->db->table('cicilan');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $cicilan_id = $this->db->insertID();
            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;

        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'cicilanqty') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $this->db->table('cicilan')->update($input, array("cicilan_id" => $this->request->getPost("cicilan_id")));
            $data["message"] = "Update Success";
            // echo $this->db->getLastQuery();die;
        }

        //******update cicilan transaction******\\
        if (($this->request->getPost("create") == "OK") || ($this->request->getPost("change") == "OK")) {
            //cari cicilan
            $cicilan = $this->db->table("cicilan")->where("transaction_id", $this->request->getPost("transaction_id"))->get();
            $cicilann = 0;
            foreach ($cicilan->getResult() as $row) {
                $cicilann += $row->cicilan_nominal;
            }

            $inputtransaksi["transaction_pay"] = $cicilann;
            $this->db->table("transaction")->where("transaction_id", $this->request->getPost("transaction_id"))->update($inputtransaksi);
            // echo $this->db->getLastQuery();die;
        }
        return $data;
    }
}
