<?php

namespace App\Models\master;

use App\Models\core_m;

class mcategory_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek category
        if ($this->request->getVar("category_id")) {
            $categoryd["category_id"] = $this->request->getVar("category_id");
        } else {
            $categoryd["category_id"] = -1;
        }
            $categoryd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("category")
            ->getWhere($categoryd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "category_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $category) {
                foreach ($this->db->getFieldNames('category') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $category->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('category') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") { 
            $category_id=   $this->request->getPost("category_id");
            $cek=$this->db->table("product")
            ->where("category_id", $category_id) 
            ->get()
            ->getNumRows();
            if($cek>0){
                $data["message"] = "Category masih dipakai di data product!";
            } else{    
                $this->db
                ->table("category")
                ->delete(array("category_id" =>  $category_id,"store_id" =>session()->get("store_id")));
                $data["message"] = "Delete Success";
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'category_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('category');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $category_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'category_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('category')->update($input, array("category_id" => $this->request->getPost("category_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
