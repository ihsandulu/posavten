<?php

namespace App\Models\transaction;

use App\Models\core_m;

class lembur_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek lembur
        if ($this->request->getVar("lembur_id")) {
            $lembur["lembur_id"] = $this->request->getVar("lembur_id");
        } else {
            $lembur["lembur_id"] = -1;
        }
            $lembur["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("lembur")
            ->getWhere($lembur);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "action", "data", "lembur_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $lembur) {
                foreach ($this->db->getFieldNames('lembur') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $lembur->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('lembur') as $field) {
                $data[$field] = "";
            }
            $data["lembur_date"] = date("Y-m-d");
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $lembur_id=   $this->request->getPost("lembur_id");                      
                $this->db
                ->table("lembur")
                ->delete(array("lembur_id" => $this->request->getPost("lembur_id"),"store_id" =>session()->get("store_id")));
                $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'lembur_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");  
            $builder = $this->db->table('lembur');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $lembur_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";

            
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'lembur_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('lembur')->update($input, array("lembur_id" => $this->request->getPost("lembur_id")));
            $data["message"] = "Update Success";
            // echo $this->db->getLastQuery();die;   
            
        }
        return $data;
    }
}
