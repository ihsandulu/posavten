<?php

namespace App\Controllers\report;


use App\Controllers\baseController;

class rneraca extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }


    public function index()
    {
        return view('report/rneraca_v');
    } 
    
    public function print()
    {
        return view('report/rneracaprint_v');
    }
    
    public function shift()
    {?>
        <option value="0" <?=(isset($_GET["shift"])&&$_GET["shift"]=='0')?"selected":"";?>>Semua Shift</option>
        <?php $builder=$this->db->table("kas")
        ->select("kas_shift");
            if(isset($_GET["from"])&&$_GET["from"]!=""){
                $builder->where("kas.kas_date >=",$this->request->getGet("from"));
            }else{
                $builder->where("kas.kas_date",date("Y-m-d"));
            }
            if(isset($_GET["to"])&&$_GET["to"]!=""){
                $builder->where("kas.kas_date <=",$this->request->getGet("to"));
            }else{
                $builder->where("kas.kas_date",date("Y-m-d"));
            }
        $kas=$builder->groupBy("kas_shift")
        ->orderBy("kas_shift","ASC")
        ->get();
        foreach ($kas->getResult() as $kas) {?>                                
            <option value="<?=$kas->kas_shift;?>" <?=(isset($_GET["shift"])&&$_GET["shift"]==$kas->kas_shift)?"selected":"";?>>Shift <?=$kas->kas_shift;?></option>
        <?php }?>
    <?php }
}
