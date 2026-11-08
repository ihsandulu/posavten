<?php

namespace App\Controllers\transaction;


use App\Controllers\baseController;

class pesanan extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }


    public function index()
    {
        $data = new \App\Models\transaction\pesanan_m();
        $data = $data->data();
        return view('transaction/pesanan_v', $data);
    }
}
