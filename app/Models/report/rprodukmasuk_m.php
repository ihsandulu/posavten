<?php

namespace App\Models\report;

use App\Models\core_m;

class rprodukmasuk_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek purchased
        if ($this->request->getVar("purchased_id")) {
            $purchasedd["purchased_id"] = $this->request->getVar("purchased_id");
        } else {
            $purchasedd["purchased_id"] = -1;
        }
            $purchasedd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("purchased")
            ->getWhere($purchasedd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "purchased_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $purchased) {
                foreach ($this->db->getFieldNames('purchased') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $purchased->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('purchased') as $field) {
                $data[$field] = "0";
            }
            $data["purchased_outdate"] = "";
        }

        

        //delete
        if ($this->request->getPost("delete") == "OK") {                       
            $this->db
            ->table("purchased")
            ->delete(array("purchased_id" => $this->request->getPost("purchased_id"),"store_id" =>session()->get("store_id")));
            $data["message"] = "Delete Success";

             //update bill supplier
            $where1["supplier_id"] = $this->request->getGet("supplier_id");
            $builder=$this->db->table('supplier');
            $supplier_bill=$builder->getWhere($where1)->getRow()->supplier_bill;
            
            $input1["supplier_bill"] = $supplier_bill-$this->request->getPost("purchased_bill");
            $supplier=$builder->update($input1, $where1);

            //update stok
            $stock=$this->db->table("product")
            ->where("product_id",$this->request->getPost("product_id"))
            ->get()
            ->getRow()
            ->product_stock;

            $inputp["product_stock"]=$stock-$this->request->getPost("purchased_qty");
            $wherep["product_id"]=$this->request->getPost("product_id");
            $this->db->table("product")
            ->update($inputp,$wherep);
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'purchased_id' && $e != 'purchased_bill_before' && $e != 'purchased_qtyb') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            if($this->request->getPost("purchased_ppn")==""){$input["purchased_ppn"]=0;}
            $input["store_id"] = session()->get("store_id");
            $input["purchase_id"] = $this->request->getGet("purchase_id");

            $builder = $this->db->table('purchased');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $purchased_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";

             //update bill supplier
            $where1["supplier_id"] = $this->request->getGet("supplier_id");
            $builder=$this->db->table('supplier');
            $supplier_bill=$builder->getWhere($where1)->getRow()->supplier_bill;
            
            $input1["supplier_bill"] = $supplier_bill+ $input["purchased_bill"];
            $supplier=$builder->update($input1, $where1);

            //update stok
            $stock=$this->db->table("product")
            ->where("product_id",$input["product_id"])
            ->get()
            ->getRow()
            ->product_stock;

            $inputp["product_stock"]=$stock+$input["purchased_qty"];
            $wherep["product_id"]=$input["product_id"];
            $this->db->table("product")
            ->update($inputp,$wherep);
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'purchased_picture' && $e != 'purchased_bill_before' && $e != 'purchased_qtyb') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            
            if($this->request->getPost("purchased_ppn")==""){$input["purchased_ppn"]=0;}
            $input["store_id"] = session()->get("store_id");
            $this->db->table('purchased')->update($input, array("purchased_id" => $this->request->getPost("purchased_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;

             //update bill supplier
            $where1["supplier_id"] = $this->request->getGet("supplier_id");
            $builder=$this->db->table('supplier');
            $supplier_bill=$builder->getWhere($where1)->getRow()->supplier_bill;
            $input1["supplier_bill"] = $supplier_bill-intVal($this->request->getPost("purchased_bill_before"))+intVal($this->request->getPost("purchased_bill"));
            $supplier=$builder->update($input1, $where1);

            

            //update stok
            $stock=$this->db->table("product")
            ->where("product_id",$this->request->getPost("product_id"))
            ->get()
            ->getRow()
            ->product_stock;

            $inputp["product_stock"]=$stock-$this->request->getPost("purchased_qtyb")+$this->request->getPost("purchased_qty");
            $wherep["product_id"]=$this->request->getPost("product_id");
            $this->db->table("product")
            ->update($inputp,$wherep);
        }
        return $data;
    }
}
