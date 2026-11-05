<?php

namespace App\Controllers\report;


use App\Controllers\baseController;

class rprodukmasuk extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }


    public function index()
    {
        $data = new \App\Models\report\rprodukmasuk_m();
        $data = $data->data();
        return view('report/rprodukmasuk_v', $data);
    }
}
