<?php

namespace App\Controllers\report;


use App\Controllers\baseController;

class rtransactiond extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }


    public function index()
    {
        $data = new \App\Models\report\rtransactiond_m();
        $data = $data->data();
        return view('report/rtransactiond_v', $data);
    }
}
