<?php

namespace App\Controllers\master;


use App\Controllers\baseController;

class mdiscount extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }


    public function index()
    {
        $data = new \App\Models\master\mdiscount_m();
        $data = $data->data();
        return view('master/mdiscount_v', $data);
    }

    
    public function buy()
    {
        //buy
        if ($this->request->getVar("discount_id")) {
            $productd["discount_id"] = $this->request->getVar("discount_id");
        } else {
            $productd["discount_id"] = -1;
        }
        $purchase=$this->db->table("purchased")
        ->orderBy("purchased_id ","DESC")
        ->limit(1)
        ->getWhere($productd);
        $data["product_buy"] = 0;
        foreach ($purchase->getResult() as $purchase) {$data["product_buy"] = $purchase->purchased_price/$purchase->purchased_qty;}
        echo  $data["product_buy"];
    }
}
