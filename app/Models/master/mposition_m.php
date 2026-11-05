<?php

namespace App\Models\master;

use App\Models\core_m;

class mposition_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek position
        if ($this->request->getVar("position_id")) {
            $positiond["position_id"] = $this->request->getVar("position_id");
        } else {
            $positiond["position_id"] = -1;
        }
            $positiond["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("position")
            ->getWhere($positiond);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "position_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $position) {
                foreach ($this->db->getFieldNames('position') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $position->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('position') as $field) {
                $data[$field] = "";
            }
        }

        
        //delete
        if ($this->request->getPost("delete") == "OK") {           
            $this->db
                ->table("position")
                ->delete(array("position_id" => $this->request->getPost("position_id"),"store_id" =>session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'position_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('position');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $position_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'position_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('position')->update($input, array("position_id" => $this->request->getPost("position_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
