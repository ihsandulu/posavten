<?php

namespace App\Models\transaction;

use App\Models\core_m;

class purchase_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek purchase
        if ($this->request->getVar("purchase_id")) {
            $purchased["purchase_id"] = $this->request->getVar("purchase_id");
        } else {
            $purchased["purchase_id"] = -1;
        }
        $purchased["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("purchase")
            ->getWhere($purchased);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "purchase_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $purchase) {
                foreach ($this->db->getFieldNames('purchase') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $purchase->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('purchase') as $field) {
                $data[$field] = "";
            }
            $data["purchase_ppn"] = "0";
            $data["purchase_date"] = date("Y-m-d");
        }



        //delete
        if ($this->request->getPost("delete") == "OK") {
            $purchase_id =   $this->request->getPost("purchase_id");
            $cekpurchase = $this->db->table("purchased")
                ->where("purchase_id", $purchase_id)
                ->get()
                ->getNumRows();
            // echo $this->db->getLastQuery();die;
            if ($cekpurchase > 0) {
                $data["message"] = "Masih terdapat data barang yang belum dihapus!";
            } else {
                $cekpayment = $this->db->table("payment")
                    ->where("purchase_id", $purchase_id)
                    ->get()
                    ->getNumRows();
                // echo $this->db->getLastQuery();die;
                if ($cekpayment > 0) {
                    $data["message"] = "Masih terdapat pembayaran yang belum dihapus!";
                } else {
                    $this->db
                        ->table("purchase")
                        ->delete(array("purchase_id" => $this->request->getPost("purchase_id"), "store_id" => session()->get("store_id")));
                    $data["message"] = "Delete Success";
                }
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'purchase_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $input["cashier_id"] = session()->get("user_id");
            if (isset($_POST["purchase_no"]) && $_POST["purchase_no"] != "") {
                $input["purchase_no"] = $_POST["purchase_no"];
            } else {
                $input["purchase_no"] = "PUR" . date("YmdHis") . session()->get("store_id");
            }

            $builder = $this->db->table('purchase');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $purchase_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";

            if ($input["purchase_ppn"] > 0) {

                $spurchased = $this->db->table("purchased")
                    ->where("purchase_id", $purchase_id);
                $purchased = $spurchased->get();
                foreach ($purchased->getResult() as $purchased) {
                    $purchased_price = $purchased->purchased_price;
                    $purchased_ppn = intval($input["purchase_ppn"]);
                    $purchased_id = intval($purchased->purchased_id);
                    if ($purchased_ppn > 0) {
                        $ppn = $purchased_ppn / 100 * $purchased_price;
                    } else {
                        $ppn = 0;
                    }
                    $purchased_bill = $purchased_price + $ppn;

                    $input2["purchased_ppn"] = $purchased_ppn;
                    $input2["purchased_bill"] = $purchased_bill;
                    $where2["purchased_id"] = $purchased_id;
                    $spurchased->update($input2, $where2);
                }
            }
        }
        //echo $_POST["create"];die;

        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'purchase_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('purchase')->update($input, array("purchase_id" => $this->request->getPost("purchase_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;


            $purchase_id = $this->request->getPost("purchase_id");
            $spurchased = $this->db->table("purchased")
                ->where("purchase_id", $purchase_id);
            $purchased = $spurchased->get();
            foreach ($purchased->getResult() as $purchased) {
                $purchased_price = $purchased->purchased_price;
                $purchased_ppn = intval($input["purchase_ppn"]);
                $purchased_id = intval($purchased->purchased_id);
                if ($purchased_ppn > 0) {
                    $ppn = $purchased_ppn / 100 * $purchased_price;
                } else {
                    $ppn = 0;
                }
                $purchased_bill = $purchased_price + $ppn;

                $input2["purchased_ppn"] = $purchased_ppn;
                $input2["purchased_bill"] = $purchased_bill;
                $where2["purchased_id"] = $purchased_id;
                $spurchased->update($input2, $where2);
            }
        }
        return $data;
    }
}
