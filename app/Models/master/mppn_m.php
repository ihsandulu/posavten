<?php

namespace App\Models\master;

use App\Models\core_m;

class mppn_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek ppn
        if ($this->request->getVar("ppn_id")) {
            $ppnd["ppn_id"] = $this->request->getVar("ppn_id");
        } else {
            $ppnd["ppn_id"] = -1;
        }
            $ppnd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("ppn")
            ->getWhere($ppnd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "ppn_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $ppn) {
                foreach ($this->db->getFieldNames('ppn') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $ppn->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('ppn') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {           
            $this->db
                ->table("ppn")
                ->delete(array("ppn_id" => $this->request->getPost("ppn_id"),"store_id" =>session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'ppn_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('ppn');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $ppn_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'ppn_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('ppn')->update($input, array("ppn_id" => $this->request->getPost("ppn_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
