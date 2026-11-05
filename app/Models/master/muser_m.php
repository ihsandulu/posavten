<?php

namespace App\Models\master;

use App\Models\core_m;

class muser_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek user
        if ($this->request->getVar("user_id")) {
            $userd["user_id"] = $this->request->getVar("user_id");
        } else {
            $userd["user_id"] = -1;
        }
        $userd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("user")
            ->getWhere($userd);
        //echo $this->db->getLastquery();
        //die;
        $larang = array("log_id", "id",  "action", "data", "user_id_dep", "trx_id", "trx_code", "contact_id_dep");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $user) {
                foreach ($this->db->getFieldNames('user') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $user->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('user') as $field) {
                $data[$field] = "";
            }
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {
            $user_id=   $this->request->getPost("user_id");
            $cek=$this->db->table("transaction")
            ->where("cashier_id", $user_id) 
            ->orWhere("pic_id", $user_id) 
            ->get()
            ->getNumRows();
            if($cek>0){
                $data["message"] = "User masih dipakai di data transaksi!";
            } else{   
                $this->db
                ->table("user")
                ->delete(array("user_id" => $this->request->getPost("user_id"),"store_id" => session()->get("store_id")));
                $data["message"] = "Delete Success";
                // $data["message"] = "Delete Success" . $this->request->getPost("contact_id") . "=" . $this->request->getPost("user_id");
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' ) {
                    $inputu[$e] = $this->request->getPost($e);
                }
            }

            //user
            $inputu["store_id"] = session()->get("store_id");
            $inputu["user_password"] = password_hash($inputu["user_password"], PASSWORD_DEFAULT);
            $this->db->table('user')->insert($inputu);
            /* echo $this->db->getLastQuery();
            die; */
            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if($e!='change'&&$e!='user_password'){
                    $inputu[$e] = $this->request->getPost($e);
                }
            }
            if($this->request->getPost("user_password")!=""){
                $pass = $this->request->getPost("user_password");
                $inputu["user_password"] = password_hash($pass, PASSWORD_DEFAULT);
            }
            $this->db->table('user')
                ->where("user_id", $inputu["user_id"])
                ->where("store_id", session()->get("store_id"))
                ->update($inputu);
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
