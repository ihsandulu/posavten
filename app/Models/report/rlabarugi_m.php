<?php

namespace App\Models\report;

use App\Models\core_m;

class rlabarugi_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek kas
        if ($this->request->getVar("kas_id")) {
            $kasd["kas_id"] = $this->request->getVar("kas_id");
        } else {
            $kasd["kas_id"] = -1;
        }
            $kasd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("kas")
            ->getWhere($kasd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "kas_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $kas) {
                foreach ($this->db->getFieldNames('kas') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $kas->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('kas') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $kas_id=   $this->request->getPost("kas_id");
            $cek=$this->db->table("product")
            ->where("kas_id", $kas_id) 
            ->get()
            ->getNumRows();
            if($cek>0){
                $data["message"] = "kas masih dipakai di data product!";
            } else{            
                $this->db
                ->table("kas")
                ->delete(array("kas_id" => $this->request->getPost("kas_id"),"store_id" =>session()->get("store_id")));
                $data["message"] = "Delete Success";
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'kas_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('kas');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $kas_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'kas_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('kas')->update($input, array("kas_id" => $this->request->getPost("kas_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
