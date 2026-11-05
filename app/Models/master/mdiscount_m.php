<?php

namespace App\Models\master;

use App\Models\core_m;

class mdiscount_m extends core_m
{
    public function data()
    {
        $data = array();
        $data["message"] = "";
        //cek discount
        if ($this->request->getVar("discount_id")) {
            $discountd["discount_id"] = $this->request->getVar("discount_id");
        } else {
            $discountd["discount_id"] = -1;
        }
        $discountd["store_id"] = session()->get("store_id");
        $us = $this->db
            ->table("discount")
            ->getWhere($discountd);
        /* echo $this->db->getLastquery();
        die; */
        $larang = array("log_id", "id", "user_id", "action", "data", "discount_id_dep", "trx_id", "trx_code");
        if ($us->getNumRows() > 0) {
            foreach ($us->getResult() as $discount) {
                foreach ($this->db->getFieldNames('discount') as $field) {
                    if (!in_array($field, $larang)) {
                        $data[$field] = $discount->$field;
                    }
                }
            }
        } else {
            foreach ($this->db->getFieldNames('discount') as $field) {
                $data[$field] = "";
            }
            $data["discount_id"] = 0;
        }

        //upload image
        $data['uploaddiscount_picture'] = "";
        if (isset($_FILES['discount_picture']) && $_FILES['discount_picture']['name'] != "") {
            // $request = \Config\Services::request();
            $file = $this->request->getFile('discount_picture');
            $name = $file->getName(); // Mengetahui Nama File
            $originalName = $file->getClientName(); // Mengetahui Nama Asli
            $tempfile = $file->getTempName(); // Mengetahui Nama TMP File name
            $ext = $file->getClientExtension(); // Mengetahui extensi File
            $type = $file->getClientMimeType(); // Mengetahui Mime File
            $size_kb = $file->getSize('kb'); // Mengetahui Ukuran File dalam kb
            $size_mb = $file->getSize('mb'); // Mengetahui Ukuran File dalam mb


            //$namabaru = $file->getRandomName();//define nama fiel yang baru secara acak

            if ($type == 'image/jpg' || $type == 'image/jpeg' || $type == 'image/png') //cek mime file
            {    // File Tipe Sesuai   
                helper('filesystem'); // Load Helper File System
                $direktori = ROOTPATH . 'public\images\discount_picture'; //definisikan direktori upload            
                $discount_picture = str_replace(' ', '_', $name);
                $discount_picture = date("H_i_s_") . $discount_picture; //definisikan nama fiel yang baru
                $map = directory_map($direktori, FALSE, TRUE); // List direktori

                //Cek File apakah ada 
                foreach ($map as $key) {
                    if ($key == $discount_picture) {
                        delete_files($direktori, $discount_picture); //Hapus terlebih dahulu jika file ada
                    }
                }
                //Metode Upload Pilih salah satu
                //$path = $this->request->getFile('uploadedFile')->store($direktori, $namabaru);
                //$file->move($direktori, $namabaru)
                if ($file->move($direktori, $discount_picture)) {
                    $data['uploaddiscount_picture'] = "Upload Success !";
                    $input['discount_picture'] = $discount_picture;
                } else {
                    $data['uploaddiscount_picture'] = "Upload Gagal !";
                }
            } else {
                // File Tipe Tidak Sesuai
                $data['uploaddiscount_picture'] = "Format File Salah !";
            }
        }

        //delete
        if ($this->request->getPost("delete") == "OK") {
            $discount_id = $this->request->getPost("discount_id");
            $this->db
                ->table("discount")
                ->delete(array("discount_id" => $discount_id, "store_id" => session()->get("store_id")));
            $data["message"] = "Delete Success";
        }

        //insert
        if ($this->request->getPost("create") == "OK") {
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'create' && $e != 'discount_id' && substr($e, 0, 10) != "sell_price") {
                    $input[$e] = $this->request->getPost($e);
                }
            }

            $input["store_id"] = session()->get("store_id");

            $builder = $this->db->table('discount');
            $builder->insert($input);
            /* echo $this->db->getLastQuery();
            die; */
            $discount_id = $this->db->insertID();

            foreach ($this->request->getPost() as $e => $f) {
                if (substr($e, 0, 10) == "sell_price") {
                    $sell = explode("|", $e);
                    $input1["positionm_id"] = $sell[1];
                    $input1["discount_id"] = $discount_id;
                    $input1["sell_price"] = $f;
                    $input1["sell_percent"] = (($input1["sell_price"] - $input["discount_buy"]) / $input["discount_buy"]) * 100;

                    //pembulatan 500an
                    $asal = $input1["sell_price"];
                    $tiga = substr($asal, -3);
                    $sisa = $asal - $tiga;
                    $hasil = 0;
                    // echo $sisa;
                    // if($tiga>500){$hasil=$sisa+1000;}else{$hasil=$sisa+500;}
                    if ($tiga > 500) {
                        $hasil = $sisa + 1000;
                    } else if ($tiga > 0) {
                        $hasil = $sisa + 500;
                    } else {
                        $hasil = $sisa;
                    }
                    $input1["sell_price"] = $hasil;

                    $input1["store_id"] = session()->get("store_id");
                    $builder = $this->db->table('sell');
                    $builder->insert($input1);
                }
            }

            $data["message"] = "Insert Data Success";
        }
        //echo $_POST["create"];die;

        //update
        if ($this->request->getPost("change") == "OK") {
            $discount_id = $this->request->getPost("discount_id");
            foreach ($this->request->getPost() as $e => $f) {
                if ($e != 'change' && $e != 'discount_picture' && substr($e, 0, 10) != "sell_price") {
                    $input[$e] = $this->request->getPost($e);
                }
            }
            $input["store_id"] = session()->get("store_id");
            $this->db->table('discount')->update($input, array("discount_id" => $discount_id));

            foreach ($this->request->getPost() as $e => $f) {
                if (substr($e, 0, 10) == "sell_price") {
                    $sell = explode("|", $e);
                    $positionm_id = $sell[1];
                    $selld["store_id"] = session()->get("store_id");
                    $selld["discount_id"] = $discount_id;
                    $selld["positionm_id"] = $positionm_id;
                    $sell = $this->db
                        ->table("sell")
                        ->getWhere($selld);
                    if ($sell->getNumRows() > 0) {
                        $where1["sell_id"] = $sell->getRow()->sell_id;


                        $input1["sell_price"] = $f;
                        $input1["sell_percent"] = (($input1["sell_price"] - $input["discount_buy"]) / $input["discount_buy"]) * 100;

                        $builder = $this->db->table('sell');
                        $builder->update($input1, $where1);
                    } else {
                        $input1["positionm_id"] = $positionm_id;
                        $input1["discount_id"] = $discount_id;


                        $input1["sell_price"] = $f;
                        $input1["sell_percent"] = (($input1["sell_price"] - $input["discount_buy"]) / $input["discount_buy"]) * 100;

                        $input1["store_id"] = session()->get("store_id");
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
            $discount = $this->db->table("discount")
                ->where("store_id", session()->get("store_id"))
                ->get();
            foreach ($discount->getResult() as $discount) {
                $purchased = $this->db->table("purchased")
                    // ->select("*,COUNT(purchased_id)")
                    ->where("discount_id", $discount->discount_id)
                    ->where("store_id", session()->get("store_id"))
                    ->orderBy("purchased_id ", "DESC")
                    ->limit(1)
                    ->get();

                foreach ($purchased->getResult() as $purchased) {
                    if ($purchased->purchased_price > 0 && $purchased->purchased_qty > 0) {
                        $input["discount_buy"] = $purchased->purchased_price / $purchased->purchased_qty;
                        $where["discount_id"] = $discount->discount_id;
                        $this->db->table('discount')->update($input, $where);

                        $positionm = $this->db->table("positionm")
                            ->where("positionm.store_id", session()->get("store_id"))
                            ->get();
                        // echo $this->db->getLastQuery();die;
                        foreach ($positionm->getResult() as $positionm) {
                            $sell = $this->db->table("sell")
                                ->join("positionm", "positionm.positionm_id=sell.positionm_id", "left")
                                ->where("sell.store_id", session()->get("store_id"))
                                ->where("sell.discount_id", $discount->discount_id)
                                ->where("sell.positionm_id", $positionm->positionm_id)
                                ->get();
                            // echo $this->db->getLastQuery();
                            if ($sell->getNumRows() > 0) {
                                foreach ($sell->getResult() as $sell) {
                                    $input1["sell_price"] = ($sell->sell_percent / 100 * $input["discount_buy"]) + $input["discount_buy"];

                                    $where1["discount_id"] = $discount->discount_id;
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
