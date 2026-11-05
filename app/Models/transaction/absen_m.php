<?php

namespace App\Models\transaction;

use App\Models\core_m;

class absen_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek absen
        if ($this->request->getVar("absen_id")) {
            $absen["absen_id"] = $this->request->getVar("absen_id");
        } else {
            $absen["absen_id"] = -1;
        }
            $absen["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("absen")
            ->getWhere($absen);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "action", "data", "absen_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $absen) {
                foreach ($this->db->getFieldNames('absen') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $absen->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('absen') as $field) {
                $data[$field] = "";
            }
            $data["absen_date"] = date("Y-m-d");
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $absen_id=   $this->request->getPost("absen_id");                      
                $this->db
                ->table("absen")
                ->delete(array("absen_id" => $this->request->getPost("absen_id"),"store_id" =>session()->get("store_id")));
                $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'absen_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");  
            $builder = $this->db->table('absen');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $absen_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";

            
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'absen_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('absen')->update($input, array("absen_id" => $this->request->getPost("absen_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;   
            
        }
        return $data;
    }
}
