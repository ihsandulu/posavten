<?php

namespace App\Models\transaction;

use App\Models\core_m;

class payment_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";




        //delete
        if ($this->request->getPost("delete") == "OK") {
            $this->db
                ->table("payment")
                ->delete(array("payment_id" => $this->request->getPost("payment_id"), "store_id" => session()->get("store_id")));
            $data["message"] = "Delete Success";

            //update bill supplier
            $where1["supplier_id"] = $this->request->getPost("supplier_id");
            $builder = $this->db->table('supplier');
            $supplier_bill = $builder->getWhere($where1)->getRow()->supplier_bill;

            $input1["supplier_bill"] = $supplier_bill + $this->request->getPost("payment_nominal");
            $builder->update($input1, $where1);

            //delete kas
            $this->db
                ->table("kas")
                ->delete(array("payment_id" => $this->request->getPost("payment_id"), "store_id" => session()->get("store_id")));

            //update store
            $where4["store_id"] = session()->get("store_id");
            $builder = $this->db->table('store');
            $store_kas = $builder->getWhere($where4)->getRow()->store_kas;
            $input4["store_kas"] = $store_kas + $this->request->getPost("payment_nominal");
            $builder->update($input4, $where4);
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'payment_id' && $e != 'payment_nominal_before') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $input["payment_date"] = date("Y-m-d");
            $input["cashier_id"] = session()->get("user_id");
            $input["account_id"] = '16';
            $input["payment_no"] = "PAY" . date("YmdHis") . session()->get("store_id");

            if (isset($_GET["purchase_id"])) {
                $input["purchase_id"] = $this->request->getGet("purchase_id");
            }

            $builder = $this->db->table('payment');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $payment_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";

            //update bill supplier
            $where1["supplier_id"] = $this->request->getPost("supplier_id");
            $builder = $this->db->table('supplier');
            $supplier = $builder->getWhere($where1)->getRow();
            $supplier_bill = $supplier->supplier_bill;

            $input1["supplier_bill"] = $supplier_bill - $this->request->getPost("payment_nominal");
            $builder->update($input1, $where1);

            //input kas
            $store = $this->db->table("store")->where("store_id", session()->get("store_id"))->get()->getRow();
            $input2["store_id"] = session()->get("store_id");
            $input2["kas_date"] = date("Y-m-d");
            $input2["kas_nominal"] = $input["payment_nominal"];
            $input2["kas_type"] = 'keluar';
            $input2["kas_shift"] = $store->store_shift;
            $input2["payment_id"] = $payment_id;
            $input2["payment_no"] = $input["payment_no"];
            $input2["account_id"] = '16';
            if (isset($_GET["purchase_id"])) {
                $input2["kas_description"] = 'Pembayaran Tagihan ' . $this->request->getGet("purchase_no");
                $input2["purchase_id"] = $this->request->getGet("purchase_id");
            } else {
                $input2["kas_description"] = 'Pembayaran kpd ' . $supplier->supplier_name;
            }
            $builder = $this->db->table('kas');
            $builder->insert($input2);

            //update store
            $where4["store_id"] = session()->get("store_id");
            $builder = $this->db->table('store');
            $store_kas = $builder->getWhere($where4)->getRow()->store_kas;
            $input4["store_kas"] = $store_kas - $input["payment_nominal"];
            $builder->update($input4, $where4);
        }
        //echo $_POST["create"];die;

        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'payment_picture' && $e != 'payment_nominal_before') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('payment')->update($input, array("payment_id" => $this->request->getPost("payment_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;

            //update bill supplier
            $where1["supplier_id"] = $this->request->getPost("supplier_id");
            $builder = $this->db->table('supplier');
            $supplier_bill = $builder->getWhere($where1)->getRow()->supplier_bill;

            $input1["supplier_bill"] = $supplier_bill + $this->request->getPost("payment_nominal_before") - $this->request->getPost("payment_nominal");
            $builder->update($input1, $where1);


            //update kas
            $where3["payment_id"] = $this->request->getPost("payment_id");
            $builder = $this->db->table('kas');
            $kas_nominal = $builder->getWhere($where3)->getRow()->kas_nominal;
            $input3["kas_nominal"] = $kas_nominal - $this->request->getPost("payment_nominal_before") + $input["payment_nominal"];
            $builder->update($input3, $where3);

            //update store
            $where4["store_id"] = session()->get("store_id");
            $builder = $this->db->table('store');
            $store_kas = $builder->getWhere($where4)->getRow()->store_kas;
            $input4["store_kas"] = $store_kas + $this->request->getPost("payment_nominal_before") - $input["payment_nominal"];
            $builder->update($input4, $where4);
        }


        //cek payment
        if ($this->request->getVar("payment_id")) {
            $payment["payment_id"] = $this->request->getVar("payment_id");
        } else {
            $payment["payment_id"] = -1;
        }
        $payment["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("payment")
            ->getWhere($payment);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "payment_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $payment) {
                foreach ($this->db->getFieldNames('payment') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $payment->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('payment') as $field) {
                $data[$field] = "";
            }
            $data["supplier_id"]=$_GET["supplier_id"];
        }

        //total payment
        $builder = $this->db
            ->table("payment")
            ->where("payment.store_id", session()->get("store_id"));
        if (isset($_GET["from"]) && $_GET["from"] != "") {
            $builder->where("payment.payment_date >=", $this->request->getGet("from"));
        } else {
            $builder->where("payment.payment_date", date("Y-m-d"));
        }
        if (isset($_GET["to"]) && $_GET["to"] != "") {
            $builder->where("payment.payment_date <=", $this->request->getGet("to"));
        } else {
            $builder->where("payment.payment_date", date("Y-m-d"));
        }
        if (isset($_GET["purchase_id"]) && $_GET["purchase_id"] > 0) {
            $builder->where("payment.purchase_id", $this->request->getGet("purchase_id"));
        }
        $usr = $builder
            ->orderBy("payment_id", "DESC")
            ->get();
        // echo $this->db->getLastquery();die;
        $no = 1;
        $tnominal = 0;
        foreach ($usr->getResult() as $usr) {
            $tnominal+=$usr->payment_nominal;
        }
        $data["tpayment"]=$tnominal;

        return $data;
    }
}
