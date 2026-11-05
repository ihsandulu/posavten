<?php

namespace App\Controllers\report;


use App\Controllers\baseController;

class rhistorystock extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }


    public function index()
    {
        $data["message"]="";
        return view('report/rhistorystock_v', $data);
    } 
    
    public function print()
    {
        return view('report/rhistorystockprint_v');
    }
    
    
}
