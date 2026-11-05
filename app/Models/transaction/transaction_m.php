<?php

namespace App\Models\transaction;

use App\Models\core_m;

class transaction_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek transaction
        if ($this->request->getVar("transaction_id")) {
            $transactiond["transaction_id"] = $this->request->getVar("transaction_id");
        } else {
            $transactiond["transaction_id"] = -1;
        }
            $transactiond["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("transaction")
            ->getWhere($transactiond);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "transaction_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $transaction) {
                foreach ($this->db->getFieldNames('transaction') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $transaction->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('transaction') as $field) {
                $data[$field] = "";
            }
            $data["transaction_bill"] = "0";
            $data["transaction_pay"] = "0";
            $data["transaction_change"] = "0";
            $data["transaction_status"] = "2";
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {           
            $this->db
                ->table("transaction")
                ->delete(array("transaction_id" => $this->request->getPost("transaction_id"),"store_id" =>session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'transaction_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('transaction');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $transaction_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'transaction_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('transaction')->update($input, array("transaction_id" => $this->request->getPost("transaction_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
