<?php

namespace App\Controllers\master;


use App\Controllers\baseController;

class mmember extends baseController
{

    protected $sesi_member;
    public function __construct()
    {
        $sesi_member = new \App\Models\global_m();
        $sesi_member->ceksesi();
    }


    public function index()
    {
        $data = new \App\Models\master\mmember_m();
        $data = $data->data();
        return view('master/mmember_v', $data);
    }
}
