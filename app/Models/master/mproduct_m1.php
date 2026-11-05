<?php

namespace App\Models\master;

use App\Models\core_m;

class mproduct_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek product
        if ($this->request->getVar("product_id")) {
            $productd["product_id"] = $this->request->getVar("product_id");
        } else {
            $productd["product_id"] = -1;
        }
            $productd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("product")
            ->getWhere($productd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "product_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $product) {
                foreach ($this->db->getFieldNames('product') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $product->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('product') as $field) {
                $data[$field] = "";
            }            
            $ubeno="UBE".date("ymdHis");
            $data["product_ube"] = $ubeno;
            $data["product_id"] = 0;
            
            //buy
            $purchase=$this->db->table("purchased")
            ->orderBy("purchased_id ","DESC")
            ->limit(1)
            ->getWhere($productd);
            $data["product_buy"] = 0;
           foreach ($purchase->getResult() as $purchase) {$data["product_buy"] = $purchase->purchased_price/$purchase->purchased_qty;}
        }

        //upload image
        $data['uploadproduct_picture'] = "";
        if (isset($_FILES['product_picture']) && $_FILES['product_picture']['name'] != "") {
            // $request = \Config\Services::request();
            $file = $this->request->getFile('product_picture');
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
                $direktori = ROOTPATH . 'public\images\product_picture'; //definisikan direktori upload            
                $product_picture = str_replace(' ', '_', $name);
                $product_picture = date("H_i_s_") . $product_picture; //definisikan nama fiel yang baru
                $map = directory_map($direktori, FALSE, TRUE); // List direktori

                //Cek File apakah ada 
                foreach ($map as $key) {
                    if ($key == $product_picture) {
                        delete_files($direktori, $product_picture); //Hapus terlebih dahulu jika file ada
                    }
                }
                //Metode Upload Pilih salah satu
                //$path = $this->request->getFile('uploadedFile')->store($direktori, $namabaru);
                //$file->move($direktori, $namabaru)
                if ($file->move($direktori, $product_picture)) {
                    $data['uploadproduct_picture'] = "Upload Success !";
                    $input['product_picture'] = $product_picture;
                } else {
                    $data['uploadproduct_picture'] = "Upload Gagal !";
                }
            } else {
                // File Tipe Tidak Sesuai
                $data['uploadproduct_picture'] = "Format File Salah !";
            }
        } 

        //delete
        if ($this->request->getPost("delete") == "OK") {     
            $product_id = $this->request->getPost("product_id");
            $cek=$this->db->table("transactiond")
            ->where("product_id", $product_id) 
            ->get()
            ->getNumRows();
            if($cek>0){
                $data["message"] = "Product masih dipakai di data transaksi!";
            } else{         
                $this->db
                ->table("product")
                ->delete(array("product_id" => $product_id,"store_id" =>session()->get("store_id")));


                $this->db
                ->table("sell")
                ->delete(array("product_id" => $product_id,"store_id" =>session()->get("store_id")));
                // echo $this->db->getLastQuery(); die;

                $data["message"] = "Delete Success";
            }
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'product_id' && substr($e,0,10)!="sell_price") {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            
            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('product');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $product_id = $this->db->insertID();

            foreach ($this->request->getPost() as $e => $f) {
                if(substr($e,0,10)=="sell_price"){
                    $sell=explode("|",$e);
                    $input1["positionm_id"]=$sell[1];
                    $input1["product_id"]=$product_id;
                    $input1["sell_price"]=$f;                    
                    $input1["sell_percent"]=(($input1["sell_price"] - $input["product_buy"]) / $input["product_buy"]) * 100;

                    //pembulatan 500an
                    $asal = $input1["sell_price"];
                    $tiga = substr($asal,-3);
                    $sisa = $asal - $tiga;
                    $hasil = 0;
                    // echo $sisa;
                    if($tiga>500){$hasil=$sisa+1000;}else{$hasil=$sisa+500;}
                    $input1["sell_price"]=$hasil;

                    $input1["store_id"]=session()->get("store_id");
                    $builder = $this->db->table('sell');
                    $builder->insert($input1);
                }
            }

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;
        
        //update
        if ($this->request->getPost("change") == "OK") {
            $product_id=$this->request->getPost("product_id");
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'product_picture' && substr($e,0,10)!="sell_price") {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('product')->update($input, array("product_id" => $product_id));

            foreach ($this->request->getPost() as $e => $f) {
                if(substr($e,0,10)=="sell_price"){          
                    $sell=explode("|",$e);
                    $positionm_id=$sell[1];
                    $selld["store_id"] = session()->get("store_id");
                    $selld["product_id"] = $product_id;
                    $selld["positionm_id"] = $positionm_id;
                    $sell = $this->db
                        ->table("sell")
                        ->getWhere($selld);
                    if ($sell->getNumRows() > 0) {
                        $where1["sell_id"]=$sell->getRow()->sell_id;
                        
                        
                        $input1["sell_price"]=$f;                    
                        $input1["sell_percent"]=(($input1["sell_price"] - $input["product_buy"]) / $input["product_buy"]) * 100;

                        //pembulatan 500an
                        $asal = $input1["sell_price"];
                        $tiga = substr($asal,-3);
                        $sisa = $asal - $tiga;
                        $hasil = 0;
                        // echo $asal."=>";
                        // echo $sisa."=>";
                        // echo $tiga."<br/>";
                        if($tiga>500){$hasil=$sisa+1000;}else{$hasil=$sisa+500;}
                        $input1["sell_price"]=$hasil;

                        $builder = $this->db->table('sell');
                        $builder->update($input1,$where1);
                    }else{                  
                        $input1["positionm_id"]=$positionm_id;
                        $input1["product_id"]=$product_id;

                        
                        $input1["sell_price"]=$f;                    
                        $input1["sell_percent"]=(($input1["sell_price"] - $input["product_buy"]) / $input["product_buy"]) * 100;

                        //pembulatan 500an
                        $asal = $input1["sell_price"];
                        $tiga = substr($asal,-3);
                        $sisa = $asal - $tiga;
                        $hasil = 0;
                        // echo $asal."=>";
                        // echo $sisa."=>";
                        // echo $tiga."<br/>";
                        if($tiga>500){$hasil=$sisa+1000;}else{$hasil=$sisa+500;}
                        $input1["sell_price"]=$hasil;

                        $input1["store_id"]=session()->get("store_id");
                        $builder = $this->db->table('sell');
                        $builder->insert($input1);
                    }
                }
            }
                    // die;

            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }

        //update buy
        set_time_limit(300);
        if ($this->request->getPost("updatebuy") == "OK") {
            $product=$this->db->table("product")
            ->where("store_id",session()->get("store_id"))
            ->get();
            foreach ($product->getResult() as $product) {
                $purchased=$this->db->table("purchased")
                // ->select("*,COUNT(purchased_id)")
                ->where("product_id",$product->product_id)
                ->where("store_id",session()->get("store_id"))
                ->orderBy("purchased_id ","DESC")
                ->limit(1)
                ->get();
                
                foreach ($purchased->getResult() as $purchased) {
                    if($purchased->purchased_price>0 && $purchased->purchased_qty>0){
                        $input["product_buy"] = $purchased->purchased_price/$purchased->purchased_qty;  
                        $where["product_id"] = $product->product_id;
                        $this->db->table('product')->update($input, $where);

                         $positionm=$this->db->table("positionm")
                        ->where("positionm.store_id",session()->get("store_id"))
                        ->get();
                            // echo $this->db->getLastQuery();die;
                        foreach ($positionm->getResult() as $positionm) {
                            $sell=$this->db->table("sell")
                            ->join("positionm","positionm.positionm_id=sell.positionm_id","left")
                            ->where("sell.store_id",session()->get("store_id"))
                            ->where("sell.product_id",$product->product_id)
                            ->where("sell.positionm_id",$positionm->positionm_id)
                            ->get();
                            // echo $this->db->getLastQuery();
                            if ($sell->getNumRows() > 0) {
                                foreach ($sell->getResult() as $sell) {
                                    $input1["sell_price"] = ($sell->sell_percent/100*$input["product_buy"])+$input["product_buy"];  

                                    //pembulatan 500an
                                    $asal = $input1["sell_price"];
                                    $tiga = substr($asal,-3);
                                    $sisa = $asal - $tiga;
                                    $hasil = 0;
                                    // echo $sisa;
                                    if($tiga>500){$hasil=$sisa+1000;}else{$hasil=$sisa+500;}
                                    $input1["sell_price"]=$hasil;

                                    $where1["product_id"] = $product->product_id;
                                    $where1["positionm_id"] = $positionm->positionm_id;
                                    $where1["store_id"] = session()->get("store_id");
                                    $this->db->table('sell')->update($input1, $where1);
                                }
                            }
                            // echo $this->db->getLastQuery();

                        }
                        // die;
                    }
                }                
            }
            $data["message"] = "Update Success";
            //echo $this->db->last_query();die;
        }

        return $data;
    }
}
