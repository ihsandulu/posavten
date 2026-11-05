<?php

namespace App\Models\master;

use App\Models\core_m;

class mmember_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek member
        if ($this->request->getVar("member_id")) {
            $memberd["member_id"] = $this->request->getVar("member_id");
        } else {
            $memberd["member_id"] = -1;
        }
        $memberd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("member")
            ->getWhere($memberd);
        //echo $this->db->getLastquery();
        //die;
        $larang = array("log_id", "id",  "action", "data", "member_id_dep", "trx_id", "trx_code", "contact_id_dep");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $member) {
                foreach ($this->db->getFieldNames('member') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $member->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('member') as $field) {
                $data[$field] = "";
            }
                $data["member_password"] = "123456";
                $data["member_no"] = "M".date("ymdHis");
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {
            $member_id=   $this->request->getPost("member_id");
            $cek=$this->db->table("transaction")
            ->where("cashier_id", $member_id) 
            ->orWhere("pic_id", $member_id) 
            ->get()
            ->getNumRows();
            if($cek>0){
                $data["message"] = "member masih dipakai di data transaksi!";
            } else{   
                $this->db
                ->table("member")
                ->delete(array("member_id" => $this->request->getPost("member_id"),"store_id" => session()->get("store_id")));
                $data["message"] = "Delete Success";
                // $data["message"] = "Delete Success" . $this->request->getPost("contact_id") . "=" . $this->request->getPost("member_id");
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' ) {
                    $inputu[$e] = $this->request->getPost($e);
                }
            }

            //member
            $inputu["store_id"] = session()->get("store_id");
            $inputu["member_password"] = password_hash($inputu["member_password"], PASSWORD_DEFAULT);
            $this->db->table('member')->insert($inputu);
            /* echo $this->db->getLastQuery();
            die; */
            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if($e!='change'&&$e!='member_password'){
                    $inputu[$e] = $this->request->getPost($e);
                }
            }
            if($this->request->getPost("member_password")!=""){
                $pass = $this->request->getPost("member_password");
                $inputu["member_password"] = password_hash($pass, PASSWORD_DEFAULT);
            }
            $this->db->table('member')
                ->where("member_id", $inputu["member_id"])
                ->where("store_id", session()->get("store_id"))
                ->update($inputu);
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
