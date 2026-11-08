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
            $pesanan_id=   $this->request->getPost("pesanan_id");                      
                $this->db
                ->table("pesanan")
                ->delete(array("pesanan_id" => $this->request->getPost("pesanan_id"),"store_id" =>session()->get("store_id")));
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
            
        }
        return $data;
    }
}
