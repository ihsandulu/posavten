<?php

namespace App\Models\transaction;

use App\Models\core_m;

class payroll_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek payroll
        if ($this->request->getVar("payroll_id")) {
            $payroll["payroll_id"] = $this->request->getVar("payroll_id");
        } else {
            $payroll["payroll_id"] = -1;
        }
            $payroll["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("payroll")
            ->getWhere($payroll);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "action", "data", "payroll_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $payroll) {
                foreach ($this->db->getFieldNames('payroll') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $payroll->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('payroll') as $field) {
                $data[$field] = "";
            }
            $data["payroll_date"] = date("Y-m-d");
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $payroll_id=   $this->request->getPost("payroll_id");                      
                $this->db
                ->table("payroll")
                ->delete(array("payroll_id" => $this->request->getPost("payroll_id"),"store_id" =>session()->get("store_id")));
                $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'payroll_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");  
            $builder = $this->db->table('payroll');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $payroll_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";

            
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'payroll_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('payroll')->update($input, array("payroll_id" => $this->request->getPost("payroll_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;   
            
        }
        return $data;
    }
}
