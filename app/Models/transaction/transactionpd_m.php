<?php

namespace App\Models\transaction;

use App\Models\core_m;

class transactionpd_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek transactiond
        if ($this->request->getVar("transactiond_id")) {
            $transactiondd["transactiond_id"] = $this->request->getVar("transactiond_id");
        } else {
            $transactiondd["transactiond_id"] = -1;
        }
        $transactiondd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("transactiond")
            ->getWhere($transactiondd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "transactiond_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $transactiond) {
                foreach ($this->db->getFieldNames('transactiond') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $transactiond->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('transactiond') as $field) {
                $data[$field] = "";
            }
        }
        $data["transaction_id"] = $this->request->getGet("transaction_id");

        $positionm = $this->db->table("positionm")
            ->where("store_id", session()->get("store_id"))
            ->where("positionm_default", "1")
            ->get();
            // echo $this->db->getLastQuery();die;
        $positionm_id = 0;
        foreach ($positionm->getResult() as $row) {
            $positionm_id = $row->positionm_id;
        }

        $member = $this->db->table("member")->where("member_id", $this->request->getGet("member_id"))->get();
        // echo $this->db->getLastQuery();die;
        foreach ($member->getResult() as $row) {
            $positionm_id = $row->positionm_id;
        }
        $data["positionm_id"] = $positionm_id;



        //delete
        if ($this->request->getPost("delete") == "OK") {
            //stok
            $product = $this->db->table("product")->where("product_id", $this->request->getPost("product_id"))->get();
            $stok = $this->request->getPost("transactiondqty");
            foreach ($product->getResult() as $row) {
                $stok = $row->product_stock + $this->request->getPost("transactiondqty");
            }
            $inputproduct["product_stock"] = $stok;
            $this->db->table('product')->update($inputproduct, array("product_id" => $this->request->getPost("product_id")));

            $this->db
                ->table("transactiond")
                ->delete(array("transactiond_id" => $this->request->getPost("transactiond_id"), "store_id" => session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'transactiond_id' && $e != 'transactiondqty') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            //stok
            $product = $this->db->table("product")->where("product_id", $input["product_id"])->get();
            $stokawal = 0;
            foreach ($product->getResult() as $row) {
                $stokawal = $row->product_stock;
            }
            $stokakhir = $stokawal - $input["transactiond_qty"];
            $input["transactiond_stokawal"] = $stokawal;
            $input["transactiond_stokakhir"] = $stokakhir;
            $builder = $this->db->table('transactiond');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $transactiond_id = $this->db->insertID();

            //update stok
            $inputproduct["product_stock"] = $stokakhir;
            $this->db->table('product')->update($inputproduct, array("product_id" => $this->request->getPost("product_id")));
            // echo $this->db->getLastQuery();die;            

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;

        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'transactiondqty') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            //stok
            $product = $this->db->table("product")->where("product_id", $input["product_id"])->get();
            $stokawal = $this->request->getPost("transactiondqty");
            foreach ($product->getResult() as $row) {
                $stokawal = $row->product_stock + $this->request->getPost("transactiondqty");
            }
            $stokakhir = $stokawal - $input["transactiond_qty"];
            $input["transactiond_stokawal"] = $stokawal;
            $input["transactiond_stokakhir"] = $stokakhir;

            $this->db->table('transactiond')->update($input, array("transactiond_id" => $this->request->getPost("transactiond_id")));

            //update stok
            $inputproduct["product_stock"] = $stokakhir;
            $this->db->table('product')->update($inputproduct, array("product_id" => $this->request->getPost("product_id")));

            $data["message"] = "Update Success";
            // echo $this->db->getLastQuery();die;
        }


        //******update tagihan transaction******\\
        if (($this->request->getPost("create") == "OK") || ($this->request->getPost("change") == "OK")) {
            //cari tagihan
            $transactiond = $this->db->table("transactiond")->where("transaction_id", $this->request->getPost("transaction_id"))->get();
            $tagihan = 0;
            foreach ($transactiond->getResult() as $row) {
                $tagihan += $row->transactiond_price;
            }
            //cek diskon
            $discount = $this->db->table("discount")
                ->where("store_id", session()->get("store_id"))
                ->where("positionm_id", $positionm_id)
                ->get();
            // echo $this->db->getLastQuery(); die;
            $diskon = 0;
            foreach ($discount->getResult() as $row) {
                if ($tagihan >= $row->discount_buymin && $tagihan <= $row->discount_buymax) {
                    $diskon += $row->discount_nominal;
                }
            }
            $inputtransaksi["transaction_dbill"] = $tagihan;
            $inputtransaksi["transaction_discount"] = $diskon;
            $inputtransaksi["transaction_bill"] = $tagihan - $diskon;
            $this->db->table("transaction")->where("transaction_id", $this->request->getPost("transaction_id"))->update($inputtransaksi);
            // echo $this->db->getLastQuery();die;
        }
        return $data;
    }
}
