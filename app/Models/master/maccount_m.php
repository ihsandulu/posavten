<?php

namespace App\Models\master;

use App\Models\core_m;

class maccount_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek account
        if ($this->request->getVar("account_id")) {
            $accountd["account_id"] = $this->request->getVar("account_id");
        } else {
            $accountd["account_id"] = -1;
        }
            $accountd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("account")
            ->getWhere($accountd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "account_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $account) {
                foreach ($this->db->getFieldNames('account') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $account->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('account') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $account_id=   $this->request->getPost("account_id");
            // dd($_POST);
            $cek=$this->db->table("transaction")
            ->where("account_id", $account_id) 
            ->get()
            ->getNumRows();
            if($cek>0){
                $data["message"] = "Account masih dipakai di Transaksi!";
            } else{            
                $this->db
                ->table("account")
                ->delete(array("account_id" => $this->request->getPost("account_id"),"store_id" =>session()->get("store_id")));
                $data["message"] = "Delete Success";
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'account_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('account');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $account_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'account_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('account')->update($input, array("account_id" => $this->request->getPost("account_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
