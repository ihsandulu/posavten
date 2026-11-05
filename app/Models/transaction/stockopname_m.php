<?php

namespace App\Models\transaction;

use App\Models\core_m;

class stockopname_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek stockopname
        if ($this->request->getVar("stockopname_id")) {
            $stockopnamed["stockopname_id"] = $this->request->getVar("stockopname_id");
        } else {
            $stockopnamed["stockopname_id"] = -1;
        }
            $stockopnamed["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("stockopname")
            ->getWhere($stockopnamed);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "stockopname_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $stockopname) {
                foreach ($this->db->getFieldNames('stockopname') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $stockopname->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('stockopname') as $field) {
                $data[$field] = "";
            } 
            $data["stockopname_datetime"] = date("Y-m-d H:i:s");
            $data["product_expiredate"] = "0000-00-00";
        }

        //upload image
        $data['uploadstockopname_picture'] = "";
        if (isset($_FILES['stockopname_picture']) && $_FILES['stockopname_picture']['name'] != "") {
            // $request = \Config\Services::request();
            $file = $this->request->getFile('stockopname_picture');
            $name = $file->getName(); // Mengetahui Nama File
            $originalName = $file->getClientName(); // Mengetahui Nama Asli
            $tempfile = $file->getTempName(); // Mengetahui Nama TMP File name
            $ext = $file->getClientExtension(); // Mengetahui extensi File
            $type = $file->getClientMimeType(); // Mengetahui Mime File
            $size_kb = $file->getSize('kb'); // Mengetahui Ukuran File dalam kb
            $size_mb = $file->getSize('mb'); // Mengetahui Ukuran File dalam mb


            //$namabaru = $file->getRandomName();//define nama fiel yang baru secara acak

            if ($type == 'image/jpg'||$type == 'image/jpeg'||$type == 'image/png') //cek mime file
            {    // File Tipe Sesuai   
                helper('filesystem'); // Load Helper File System
                $direktori = ROOTPATH . 'public\images\stockopname_picture'; //definisikan direktori upload            
                $stockopname_picture = str_replace(' ', '_', $name);
                $stockopname_picture = date("H_i_s_") . $stockopname_picture; //definisikan nama fiel yang baru
                $map = directory_map($direktori, FALSE, TRUE); // List direktori

                //Cek File apakah ada 
                foreach ($map as $key) {
                    if ($key == $stockopname_picture) {
                        delete_files($direktori, $stockopname_picture); //Hapus terlebih dahulu jika file ada
                    }
                }
                //Metode Upload Pilih salah satu
                //$path = $this->request->getFile('uploadedFile')->store($direktori, $namabaru);
                //$file->move($direktori, $namabaru)
                if ($file->move($direktori, $stockopname_picture)) {
                    $data['uploadstockopname_picture'] = "Upload Success !";
                    $input['stockopname_picture'] = $stockopname_picture;
                } else {
                    $data['uploadstockopname_picture'] = "Upload Gagal !";
                }
            } else {
                // File Tipe Tidak Sesuai
                $data['uploadstockopname_picture'] = "Format File Salah !";
            }
        } 

        //delete
        if ($this->request->getPost("delete") == "OK") { 
            $stockopname_id = $this->request->getPost("stockopname_id"); 
            //cek tgl update
            $stockopname_datetime = $this->request->getPost("stockopname_datetime"); 
            $product_id = $this->request->getPost("product_id"); 
            $stockopname_awal = $this->request->getPost("stockopname_awal"); 
            $cektgl=$this->db->table("stockopname")
            ->where("stockopname_datetime >=",$stockopname_datetime)
            ->where("product_id",$product_id)
            ->where("stockopname_id !=",$stockopname_id)
            ->countAllResults();
            if($cektgl==0){                
                $input1["product_stock"] = $stockopname_awal;
                $where1["product_id"] = $product_id;
                $this->db->table('product')->update($input1, $where1);
            }
      
            $this->db
            ->table("stockopname")
            ->delete(array("stockopname_id" => $stockopname_id,"store_id" =>session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'stockopname_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('stockopname');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $stockopname_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";

            //cek tgl update
            $cektgl=$this->db->table("stockopname")
            ->where("stockopname_datetime >=",$input["stockopname_datetime"])
            ->where("product_id",$input["product_id"])
            ->where("stockopname_id !=",$stockopname_id)
            ->countAllResults();
            // echo $this->db->getLastQuery();
            // die;
            if($cektgl==0){                
                $input1["product_stock"] = $input["stockopname_hitung"];
                $where1["product_id"] = $input["product_id"];
                $this->db->table('product')->update($input1, $where1);
            }
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'stockopname_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $stockopname_id=$this->request->getPost("stockopname_id");
            $input["store_id"] = session()->get("store_id");
            $this->db->table('stockopname')->update($input, array("stockopname_id" => $stockopname_id));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;

            //cek tgl update
            $cektgl=$this->db->table("stockopname")
            ->where("stockopname_datetime >=",$input["stockopname_datetime"])
            ->where("product_id",$input["product_id"])
            ->where("stockopname_id !=",$stockopname_id)
            ->countAllResults();
            if($cektgl==0){                
                $input1["product_stock"] = $input["stockopname_hitung"];
                $where1["product_id"] = $input["product_id"];
                $this->db->table('product')->update($input1, $where1);
            }
        }

        return $data;
    }
}
