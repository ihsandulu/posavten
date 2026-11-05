<?php

namespace App\Models\report;

use App\Models\core_m;

class rprodukkeluar_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek transactiond
        if ($this->request->getVar("transactiond_id")) {
            $transactiondd["transactiond_id"] = $this->request->getVar("transactiond_id");
        } else {
            $transactiondd["transactiond_id"] = -1;
        }
            $transactiondd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("transactiond")
            ->getWhere($transactiondd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "transactiond_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $transactiond) {
                foreach ($this->db->getFieldNames('transactiond') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $transactiond->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('transactiond') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $transactiond_id=   $this->request->getPost("transactiond_id");
            $cek=$this->db->table("product")
            ->where("transactiond_id", $transactiond_id) 
            ->get()
            ->getNumRows();
            if($cek>0){
                $data["message"] = "transactiond masih dipakai di data product!";
            } else{            
                $this->db
                ->table("transactiond")
                ->delete(array("transactiond_id" => $this->request->getPost("transactiond_id"),"store_id" =>session()->get("store_id")));
                $data["message"] = "Delete Success";
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'transactiond_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('transactiond');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $transactiond_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'transactiond_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('transactiond')->update($input, array("transactiond_id" => $this->request->getPost("transactiond_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
