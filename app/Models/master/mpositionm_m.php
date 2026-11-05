<?php

namespace App\Models\master;

use App\Models\core_m;

class mpositionm_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek positionm
        if ($this->request->getVar("positionm_id")) {
            $positionmd["positionm_id"] = $this->request->getVar("positionm_id");
        } else {
            $positionmd["positionm_id"] = -1;
        }
            $positionmd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("positionm")
            ->getWhere($positionmd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "positionm_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $positionm) {
                foreach ($this->db->getFieldNames('positionm') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $positionm->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('positionm') as $field) {
                $data[$field] = "";
            }
        }

        
        //delete
        if ($this->request->getPost("delete") == "OK") {           
            $this->db
                ->table("positionm")
                ->delete(array("positionm_id" => $this->request->getPost("positionm_id"),"store_id" =>session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            //kosongkan fiekd default
             $inputq["positionm_default"] = 0;
            $this->db->table('positionm')->update($inputq, array("store_id" => session()->get("store_id")));
            
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'positionm_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('positionm');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $positionm_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            //kosongkan fiekd default
             $inputq["positionm_default"] = 0;
            $this->db->table('positionm')->update($inputq, array("store_id" => session()->get("store_id")));

            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'positionm_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('positionm')->update($input, array("positionm_id" => $this->request->getPost("positionm_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
