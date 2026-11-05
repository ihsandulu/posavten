<?php

namespace App\Models\master;

use App\Models\core_m;

class mstore_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek store
        if ($this->request->getVar("store_id")) {
            $stored["store_id"] = $this->request->getVar("store_id");
        } else {
            $stored["store_id"] = -1;
        }
            $stored["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("store")
            ->getWhere($stored);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "store_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $store) {
                foreach ($this->db->getFieldNames('store') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $store->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('store') as $field) {
                $data[$field] = "";
            }
        }

        //upload image
        $data['uploadstore_picture'] = "";
        if (isset($_FILES['store_picture']) && $_FILES['store_picture']['name'] != "") {
            // $request = \Config\Services::request();
            $file = $this->request->getFile('store_picture');
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
                $direktori = ROOTPATH . 'public/images/store_picture'; //definisikan direktori upload            
                $store_picture = str_replace(' ', '_', $name);
                $store_picture = date("H_i_s_") . $store_picture; //definisikan nama fiel yang baru
                $map = directory_map($direktori, FALSE, TRUE); // List direktori

                //Cek File apakah ada 
                foreach ($map as $key) {
                    if ($key == $store_picture) {
                        delete_files($direktori, $store_picture); //Hapus terlebih dahulu jika file ada
                    }
                }
                //Metode Upload Pilih salah satu
                //$path = $this->request->getFile('uploadedFile')->store($direktori, $namabaru);
                //$file->move($direktori, $namabaru)
                if ($file->move($direktori, $store_picture)) {
                    $data['uploadstore_picture'] = "Upload Success !";
                    $input['store_picture'] = $store_picture;
                } else {
                    $data['uploadstore_picture'] = "Upload Gagal !";
                }
            } else {
                // File Tipe Tidak Sesuai
                $data['uploadstore_picture'] = "Format File Salah !";
            }
        } 

        //delete
        if ($this->request->getPost("delete") == "OK") {  
            $store_id=$this->request->getPost("store_id");  
            $cek=$this->db->table("user") 
            ->where("store_id",$store_id)  
            ->get()
            ->getNumRows();
            if($cek>0){
                $data["message"] = "Data masih terpakai di menu lain!";
            }else{    
                $this->db
                ->table("store")
                ->delete(array("store_id" => $this->request->getPost("store_id"),"store_id" =>session()->get("store_id")));
                $data["message"] = "Delete Success";
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'store_id') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('store');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $store_id = $this->db->insertID();

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        //update
        if ($this->request->getPost("change") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'store_picture') {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('store')->update($input, array("store_id" => $this->request->getPost("store_id")));
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }
        return $data;
    }
}
