<?php

namespace App\Models\master;

use App\Models\core_m;

class msupplier_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek supplier
        if ($this->request->getVar("supplier_id")) {
            $supplierd["supplier_id"] = $this->request->getVar("supplier_id");
        } else {
            $supplierd["supplier_id"] = -1;
        }
            $supplierd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("supplier")
            ->getWhere($supplierd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "supplier_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $supplier) {
                foreach ($this->db->getFieldNames('supplier') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $supplier->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('supplier') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {           
            $this->db
                ->table("supplier")
                ->delete(array("supplier_id" => $this->request->getPost("supplier_id"),"store_id" =>session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'supplier_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('supplier');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $supplier_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'supplier_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('supplier')->update($input, array("supplier_id" => $this->request->getPost("supplier_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
