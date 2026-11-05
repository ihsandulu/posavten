<?php

namespace App\Models\master;

use App\Models\core_m;

class mbank_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek bank
        if ($this->request->getVar("bank_id")) {
            $bankd["bank_id"] = $this->request->getVar("bank_id");
        } else {
            $bankd["bank_id"] = -1;
        }
            $bankd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("bank")
            ->getWhere($bankd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "bank_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $bank) {
                foreach ($this->db->getFieldNames('bank') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $bank->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('bank') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $bank_id=   $this->request->getPost("bank_id");
            $cek=$this->db->table("product")
            ->where("bank_id", $bank_id) 
            ->get()
            ->getNumRows();
            if($cek>0){
                $data["message"] = "bank masih dipakai di data product!";
            } else{            
                $this->db
                ->table("bank")
                ->delete(array("bank_id" => $this->request->getPost("bank_id"),"store_id" =>session()->get("store_id")));
                $data["message"] = "Delete Success";
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'bank_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('bank');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $bank_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'bank_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('bank')->update($input, array("bank_id" => $this->request->getPost("bank_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
