<?php

namespace App\Controllers\transaction;


use App\Controllers\baseController;

class transaction extends baseController
{

    protected $sesi_user;
    public function __construct()
    {
        $sesi_user = new \App\Models\global_m();
        $sesi_user->ceksesi();
    }


    public function index()
    {
        $data = new \App\Models\transaction\transaction_m();
        $data = $data->data();
        return view('transaction/transaction_v', $data);
    }


    public function print()
    {
        return view('transaction/transactionprint_v');
    }

    public function posisishift(){
        $builder=$this->db->table("store")
        ->where("store_id",session()->get("store_id"))
        ->orderBy("store_id","DESC")
        ->limit(1)
        ->get();
        $kas_type='keluar';//masuk,keluar
        foreach($builder->getResult() as $kas){
            $kas_type= $kas->store_posisishift;
            if($kas_type==""){$kas_type='keluar';}
        }
        echo $kas_type;
    }

    public function shift(){
        $builder=$this->db->table("store")
        ->where("store_id",session()->get("store_id"))
        ->orderBy("store_id","DESC")
        ->limit(1)
        ->get();
        $store_shift=1;//masuk,keluar
        $store_date=date("Y-m-d");
        foreach($builder->getResult() as $shift){
            $store_shift= $shift->store_shift;
            $store_date= $shift->store_date;
            $store_date= $shift->store_date;
        }
        
        //perhitungan shift
        if($store_shift==''||$store_date!=date("Y-m-d")){
            $store_shift=1;
        }else{
            $store_shift=$store_shift;
        }
        echo $store_shift;
    }

    public function kas(){
        $data["message"]="";
        $kas_nominal=$this->request->getGet("kas_nominal");
        $kas_typeinput=$this->request->getGet("kas_type");//inputan
        $store_id=$this->request->getGet("store_id");
        $kas_shiftinput=$this->request->getGet("kas_shift");

        //cek kas realtime
       $store=$this->db->table("store")
       ->where("store_id",$store_id)
        ->orderBy("store_id","DESC")
        ->limit(1)
        ->get()
        ->getRow();
       $modalawal=$store->store_modal;//modal awal
       $kasrealtime=$store->store_kas;//kas aktual
       $store_shift=$store->store_shift;//shift ke berapa
       $store_date=$store->store_date;//tgl shift yg sedang berlaku
       $posisishift=$store->store_posisishift;//posisi shift apakah sendang berjalan (masuk) ataukah sedang off(toko tutup/pergantian shift)

        
       //cek shift
        if($posisishift==''||$store_date!=date("Y-m-d")){
            $store_shift=1;
        }elseif($posisishift=='keluar'&&$kas_typeinput=='masuk'){
            $store_shift+=1;
        }else{
            $store_shift=$store_shift;
        }

        //cek kas terakhir
       $kas=$this->db->table("kas")
       ->where("store_id",$store_id)
        ->orderBy("kas_id","DESC")
        ->limit(1);
        $cekkas=$kas->get();
        $data["message"] = $this->db->getLastQuery();

        $kas_lasttype='keluar';//masuk,keluar

        if($kas->countAll()>0){
            
            foreach($cekkas->getResult() as $kas){
                $kas_lasttype= $kas->kas_type;//data terakhir di database
                $kas_lastnominal= $kas->kas_nominal;//nominal terakhir di table kas
                $kas_shift=$kas->kas_shift;//shift terakhir di table kas
                
                // echo $kas_lasttype."-".$kas_typeinput."-".$store_date."-".date("Y-m-d");

                if(
                    ($kas_lasttype=='keluar'&&$kas_typeinput=='masuk')
                    ||(
                        $kas_lasttype=='masuk'&&$kas_typeinput=='masuk'
                        &&$store_date!=date("Y-m-d")
                        )
                    ){
                    //Keluar(Terakhir)-Masuk(Input) atau Masuk(Terakhir)-Masuk(Input) tapi Di Hari yang Sama

                    //insert kas
                    $input["kas_shift"]= $store_shift;
                    $input["kas_nominal"]= $kas_nominal;
                    $input["kas_type"]= $kas_typeinput;
                    $input["kas_description"]= "Modal Awal";
                    $input["kas_date"]= date("Y-m-d");
                    $builder=$this->db->table("kas")
                    ->insert($input);
                    $kas_id = $this->db->insertID();
                    $data["message"]="Store modal awal berhasil, ID=".$kas_id;

                    //insert modal
                    $input2["modal_shift"]= $store_shift;
                    $input2["modal_nominal"]= $kasrealtime+$kas_nominal;
                    $input2["modal_date"]= date("Y-m-d");
                    $this->db->table("modal")
                    ->insert($input2);

                    //update store
                    $input1["store_kas"]= $kasrealtime+$kas_nominal;
                    $input1["store_modal"]= $kasrealtime+$kas_nominal;//nambah dari kas bukan dari modal
                    $input1["store_shift"]= $store_shift;
                    $input1["store_posisishift"]= $kas_typeinput;
                    $input1["store_date"]= date("Y-m-d");
                    $where1["store_id"]= $store_id;
                    $this->db->table("store")
                    ->update($input1,$where1);

                }elseif($kas_lasttype=='masuk'&&$kas_typeinput=='masuk'){
                    //Masuk(Terakhir)-Masuk(Input) di Hari yang Sama

                    

                    //update kas
                    $input["kas_nominal"]= $kas_nominal;
                    $input["kas_shift"]= $store_shift;
                    $where["kas_id"]= $kas->kas_id;
                    $where["store_id"]= $store_id;
                    $builder=$this->db->table("kas")
                    ->update($input,$where);
                    $kas_id = $kas->kas_id;
                    $data["message"]="Modal awal diupdate, ID=".$kas_id;

                    //update modal
                    $where2["modal_shift"]= $store_shift;
                    $input2["modal_nominal"]= ($modalawal-$kas_lastnominal)+$kas_nominal;
                    $where2["modal_date"]= date("Y-m-d");
                    $this->db->table("modal")
                    ->update($input2);

                    //update store
                    $input1["store_kas"]= ($kasrealtime-$kas_lastnominal)+$kas_nominal;
                    $input1["store_modal"]= ($modalawal-$kas_lastnominal)+$kas_nominal;
                    $input1["store_posisishift"]= $kas_typeinput;
                    $where1["store_id"]= $store_id;
                    $this->db->table("store")
                    ->update($input1,$where1);

                }elseif($kas_lasttype=='masuk'&&$kas_typeinput=='keluar'){
                    //Masuk(Terakhir)-Keluar(Input)

                    //input kas
                   $input["kas_nominal"]= $kas_nominal;
                    $input["kas_shift"]= $store_shift;
                    $input["kas_type"]= $kas_typeinput;
                    $input["kas_description"]= "Penarikan Kas";
                    $input["kas_date"]= date("Y-m-d");
                    $builder=$this->db->table("kas")
                    ->insert($input);
                    $kas_id = $this->db->insertID();
                    $data["message"]="Penarikan kas berhasil, ID=".$kas_id;                   

                     //update store
                    $kastoko=$kasrealtime-$kas_nominal;
                    $input1["store_kas"]= $kastoko;
                    $input1["store_posisishift"]= $kas_typeinput;
                    $where1["store_id"]= $store_id;
                    $this->db->table("store")
                    ->update($input1,$where1);
                }elseif($kas_lasttype=='keluar'&&$kas_typeinput=='keluar'){
                    //Masuk(Terakhir)-Keluar(Input) di Hari yang Sama
                    
                    

                    //input kas
                    $input["kas_nominal"]= $kas_nominal;
                    $input["kas_shift"]= $store_shift;
                    $where["kas_id"]= $kas->kas_id;
                    $where["store_id"]= $store_id;
                    $builder=$this->db->table("kas")
                    ->update($input,$where);
                    $kas_id = $kas->kas_id;
                    $data["message"]="Modal Akhir diupdate, ID=".$kas_id;

                    // echo $kasrealtime."+".$kas_lastnominal."-".$kas_nominal;
                    //update store
                    $kastoko=($kasrealtime+$kas_lastnominal)-$kas_nominal;
                    $input1["store_kas"]= $kastoko;
                    $modal=($modalawal+$kas_lastnominal)-$kas_nominal;
                    $input1["store_modal"]= $modal;
                    $input1["store_posisishift"]= $kas_typeinput;
                    $where1["store_id"]= $store_id;
                    $this->db->table("store")
                    ->update($input1,$where1);
                }
            }
        }else{
            //insert kas
            $input["kas_shift"]= $store_shift;
            $input["kas_nominal"]= $kas_nominal;
            $input["kas_type"]= $kas_typeinput;
            $input["kas_description"]= "Modal Awal";
            $input["kas_date"]= date("Y-m-d");
            $builder=$this->db->table("kas")
            ->insert($input);
            $kas_id = $this->db->insertID();
            $data["message"]="Store modal awal berhasil, ID=".$kas_id;

            
            //insert modal
            $input2["modal_shift"]= $store_shift;
            $input2["modal_nominal"]= $kas_nominal;
            $input2["modal_date"]= date("Y-m-d");
            $this->db->table("modal")
            ->insert($input2);

            //update store
            $input1["store_kas"]= $kasrealtime+$kas_nominal;
            $input1["store_modal"]= $kasrealtime+$kas_nominal;//nambah dari kas bukan dari modal
            $input1["store_shift"]= $store_shift;
            $input1["store_posisishift"]= 'masuk';
            $input1["store_date"]= date("Y-m-d");
            $where1["store_id"]= $store_id;
            $this->db->table("store")
            ->update($input1,$where1);
            // $data["message"] = $this->db->getLastQuery();
        }
        echo $data["message"];
        
    }

    public function nominalkas(){
        $builder=$this->db->table("store")
        ->where("store_id",session()->get("store_id"))
        ->get();
        $nominal=0;
        foreach($builder->getResult() as $store){
            $nominal=$store->store_kas;
        }
        echo $nominal;
    }

    public function modalawalkas(){
        $builder=$this->db->table("store")
        ->where("store_id",session()->get("store_id"))
        ->get();
        $modalawal=0;
        foreach($builder->getResult() as $store){
            $modalawal=$store->store_modal;
        }
        echo $modalawal;
    }

    public function listnota(){
        $listnota=$this->db->table("transaction")
        ->where("transaction_status",$this->request->getGet("transaction_status"))
        ->get();
        foreach ($listnota->getResult() as $listnota) {
        ?>
            <button onclick="nota(<?=$listnota->transaction_id;?>);" class="btn btn-outline-secondary mb-2  btn-child" type="button"><small><?=$listnota->transaction_no;?></small></button>
        <?php
        }
    }

    public function createnota(){
        $input["transaction_date"] = date("Y-m-d");
        $input["transaction_no"] = "POS".date("YmdHis").session()->get("store_id");
        $input["cashier_id"] = session()->get("user_id");
        $input["store_id"] = session()->get("store_id");
        $input["transaction_status"] = 2;
        $input["transaction_shift"] = $this->request->getGet("transaction_shift");

        $builder = $this->db->table('transaction');
        $builder->insert($input);
        // $data["message"] = $this->db->getLastQuery();
        $transaction_id = $this->db->insertID();

        $data["message"] = $transaction_id;
        echo $data["message"];
    }

    public function insertnota(){
        $transaction_id=$this->request->getGet("transaction_id");
        $builder=$this->db->table("product");
        if(isset($_GET["product_id"])){
            $product_id=$this->request->getGet("product_id");
            $product=$builder->where("product_id",$product_id);
        }
        if(isset($_GET["product_batch"])){
            $product_batch=$this->request->getGet("product_batch");
            $product=$builder->where("product_batch",$product_batch);
        }
        $pro=$product->get();
        $sell=$pro->getRow()->product_sell;
        if($pro->getNumRows()>0){
            $where["transaction_id"] = $transaction_id;
            $where["product_id"] = $pro->getRow()->product_id;
            $where["store_id"] = session()->get("store_id");

            $cari = $this->db->table('transactiond')
            ->where($where)
            ->get();
            
            $transactiond = $this->db->table('transactiond');
            if($cari->getNumRows()>0){
                foreach ($cari->getResult() as $cari) {
                    $qty=$cari->transactiond_qty;
                    $price=$cari->transactiond_price;
                    $input["transactiond_qty"] = $qty+1;
                    $input["transactiond_price"] = $price+$sell;
                    $transactiond->update($input,$where);

                    
                    $where1["product_id"] = $pro->getRow()->product_id;
                    $product = $this->db->table('product');
                    $product_stock=$product->getWhere($where1)->getRow()->product_stock;
                    $product_stock=$product_stock-1;
                    $input1["product_stock"] = $product_stock;
                    $product->update($input1,$where1);
                }
            }else{
                $where["transactiond_qty"] = 1;
                $where["transactiond_price"] = $sell;
                $transactiond->insert($where);
                $transactiond_id = $this->db->insertID();

                $where1["product_id"] = $pro->getRow()->product_id;
                $product = $this->db->table('product');
                $product_stock=$product->getWhere($where1)->getRow()->product_stock;
                $product_stock=$product_stock-1;
                $input1["product_stock"] = $product_stock;
                $product->update($input1,$where1);
            }

            // $data["message"] = $this->db->getLastQuery();
            $data["message"] = 1;
        }else{
            $data["message"] = 0;    
        }
        echo $data["message"];
    }

    public function deletenota(){
        $transaction_id=$this->request->getGet("transaction_id");
        $input["transaction_id"] = $transaction_id;

        $builder = $this->db->table('transactiond');
        $transactiond=$builder
        ->select("product_id,SUM(transactiond_qty)AS qty")
        ->where("transaction_id",$transaction_id)
        ->groupBy("product_id")
        ->get();
        foreach ($transactiond->getResult() as $transactiond) {
            $where2["product_id"] = $transactiond->product_id;
            $product = $this->db->table('product');
            $product_stock=$product->getWhere($where2)->getRow()->product_stock;
            $product_stock=$product_stock+$transactiond->qty;
            $input2["product_stock"] = $product_stock;
            $product->update($input2,$where2);
            // echo $this->db->getLastQuery()."<br/>";
        }
        $builder->delete($input);

        $builder = $this->db->table('transaction');
        $builder->delete($input);
        // $data["message"] = $this->db->getLastQuery();

        $data["message"] = $transaction_id;
        echo $data["message"];

        //delete kas
        $input1["transaction_id"]= $transaction_id;
        $builder=$this->db->table("kas")
        ->delete($input1);

        // echo $this->db->getLastQuery();
    }


    public function deletetransactiond(){
        $transaction_id=$this->request->getGet("transaction_id");
        $product_id=$this->request->getGet("product_id");
        $product_qty=$this->request->getGet("product_qty");
        $input["transaction_id"] = $transaction_id;
        $input["product_id"] = $product_id;

        $builder = $this->db->table('transactiond');
        $builder->delete($input);
        $data["message"] = $this->db->getLastQuery();

        echo $data["message"];

        $where1["product_id"] = $product_id;
        $product = $this->db->table('product');
        $product_stock=$product->getWhere($where1)->getRow()->product_stock;
        $product_stock=$product_stock+$product_qty;
        $input1["product_stock"] = $product_stock;
        $product->update($input1,$where1);

    }

    public function updateqty(){
        $transactiond_id=$this->request->getGet("transactiond_id");
        $type=$this->request->getGet("type");

        $input["transactiond_id"] = $transactiond_id;
        //cek qty
        $transactiond = $this->db->table('transactiond')
        ->join("product","product.product_id=transactiond.product_id","left")
        ->where($input)
        ->get();
        foreach ($transactiond->getResult() as $transactiond) {
            $sell=$transactiond->product_sell;
            $product_id=$transactiond->product_id;
        
            $qty=$transactiond->transactiond_qty;
            $price=$transactiond->transactiond_price;
            // $data["message"] = $qty;
            if($type=="tambah"){
                $qty++;
                $price+=$sell;
            }
            if($type=="kurang"){
                $qty--;
                $price-=$sell;
            }

            $input2["transactiond_qty"] = $qty;
            $input2["transactiond_price"] = $price;
            $where2["transactiond_id"] = $transactiond_id;
            $builder = $this->db->table('transactiond');
            $builder->update($input2,$where2);
            // $data["message"] = $this->db->getLastQuery();

            $data["message"] = $transactiond_id;

            $wherep["product_id"] = $product_id;
            $product = $this->db->table('product');
            $product_stock=$product->getWhere($wherep)->getRow()->product_stock;
             if($type=="tambah"){                
                $product_stock=$product_stock-1;
            }
            if($type=="kurang"){        
                $product_stock=$product_stock+1;
            }
            $inputp["product_stock"] = $product_stock;
            $product->update($inputp,$wherep);
        }
        echo $data["message"];
    }

    public function pelunasan(){
        $transaction_id = $this->request->getGet("transaction_id");
        $transaction_no = $this->request->getGet("transaction_no");
        $transaction_bill = $this->request->getGet("transaction_bill");
        $transaction_pay = $this->request->getGet("transaction_pay");
        $transaction_change = $this->request->getGet("transaction_change");
        $kas_shift = $this->request->getGet("shift");
        $transaction_status=0;

        $input["transaction_bill"]=$transaction_bill;
        $input["transaction_pay"]=$transaction_pay;
        $input["transaction_change"]=$transaction_change;
        $input["transaction_status"]=$transaction_status;

        $this->db->table("transaction")
        ->where("transaction_id",$transaction_id)
        ->update($input);
        echo 0;

        //insert kas
        $input1["kas_shift"]= $kas_shift;
        $input1["transaction_id"]= $transaction_id;
        $input1["kas_nominal"]= $transaction_bill;
        $input1["kas_type"]= 'masuk';
        $input1["kas_description"]= "Pembayaran ".$transaction_no;
        $input1["kas_date"]= date("Y-m-d");
        $builder=$this->db->table("kas")
        ->insert($input1);

        // echo $this->db->getLastQuery();
    }

    public function updatestatus(){
        $input["transaction_status"]=$this->request->getGet("transaction_status");
        $this->db->table("transaction")
        ->where("transaction_id",$this->request->getGet("transaction_id"))
        ->update($input);
        echo $data["message"]=$input["transaction_status"];
    }

    public function cekstatus(){
        $status=$this->db->table("transaction")
        ->where("transaction_id",$this->request->getGet("transaction_id"))
        ->get();
        $data["message"]=2;
        foreach ($status->getResult() as $status) {
            $data["message"]=$status->transaction_status;
        }
        echo $data["message"];
    }


    public function nota(){
        $transaction=$this->db->table("transaction")
        ->where("transaction_id",$this->request->getGet("transaction_id"))
        ->get();
        foreach ($transaction->getResult() as $transaction) {
            if($transaction->transaction_status==0){$iconprint="";}else{$iconprint="hide";}
        ?>
        <div class="row">            
            <div class="col-9">NOTA : <i id="transactionno"><?=$transaction->transaction_no;?></i></div>
            <div class="col-3 text-right">
                <button onclick="print(<?=$transaction->transaction_id;?>);" id="printicon" class="btn btn-warning btn-xs btn-right fa fa-print mb-2" type="button"></button>
                <?php 
                if (
                    (
                        isset(session()->get("position_administrator")[0][0]) 
                        && (
                            session()->get("position_administrator") == "1" 
                            || session()->get("position_administrator") == "2"
                        )
                    ) ||
                    (
                        isset(session()->get("halaman")['pos']['act_delete']) 
                        && session()->get("halaman")['pos']['act_delete'] == "1"
                    )
                ) { ?>
                <button onclick="deletenota(<?=$transaction->transaction_id;?>);" class="btn btn-danger btn-xs btn-right fa fa-close mb-2" type="button"></button>
                <?php }?>
            </div>
        </div>
        <div>
            <input type="hidden" id="transaction_status" value="<?=$transaction->transaction_status;?>"/>
            <input type="hidden" id="transaction_id" value="<?=$transaction->transaction_id;?>"/>
            <input type="hidden" id="transaction_no" value="<?=$transaction->transaction_no;?>"/>
            <table id="" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                <thead class="">
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Batch</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usr = $this->db
                        ->table("transactiond")
                        ->select("*,SUM(transactiond_qty)AS qty, SUM(transactiond_price)AS price,")
                        ->join("product", "product.product_id=transactiond.product_id", "left")
                        ->join("unit", "unit.unit_id=product.unit_id", "left")
                        ->where("product.store_id",session()->get("store_id"))
                        ->where("transactiond.transaction_id",$this->request->getGet("transaction_id"))
                        ->groupBy("transactiond.product_id")
                        ->orderBy("product_name", "ASC")
                        ->get();
                    //echo $this->db->getLastquery();
                    $no = 1;
                    $tprice=0;
                    foreach ($usr->getResult() as $usr) { 
                        ?>
                        <tr class="">
                            <?php if (!isset($_GET["report"])) { ?>
                                <td style="padding-left:0px; padding-right:0px;"> 
                                    <?php 
                                    if (
                                        (
                                            isset(session()->get("position_administrator")[0][0]) 
                                            && (
                                                session()->get("position_administrator") == "1" 
                                                || session()->get("position_administrator") == "2"
                                            )
                                        ) ||
                                        (
                                            isset(session()->get("halaman")['pos']['act_delete']) 
                                            && session()->get("halaman")['pos']['act_delete'] == "1"
                                        )
                                    ) { ?>
                                        <button class="btn btn-xs btn-danger delete m-2" onclick="deletetransactiond(<?= $usr->transaction_id; ?>,<?= $usr->product_id; ?>,<?= $usr->qty; ?>);" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                    <?php }?>
                                </td>
                            <?php } ?>
                            <td class="text-small0"><?= $no++; ?></td>
                            <td class="text-small0"><?= $usr->product_batch; ?></td>
                            <td class="text-small0"><?= $usr->product_name; ?></td>
                            <?php 
                            $qty=$usr->qty; 
                            $price=$usr->price; 
                            $tprice+=$price; 
                            ?>
                            <td class="text-small0">
                                <?php 
                                if (
                                    (
                                        isset(session()->get("position_administrator")[0][0]) 
                                        && (
                                            session()->get("position_administrator") == "1" 
                                            || session()->get("position_administrator") == "2"
                                        )
                                    ) ||
                                    (
                                        isset(session()->get("halaman")['pos']['act_update']) 
                                        && session()->get("halaman")['pos']['act_update'] == "1"
                                    )
                                ) { ?>
                                <?php if($qty>0){?>
                                <i onclick="updateqty(<?= $usr->transactiond_id; ?>,'kurang')" class="fa fa-minus text-small text-danger pointer"></i> 
                                <?php }?>
                                <?= number_format($qty,0,",",".") ?> <?= $usr->unit_name; ?> 
                                <?php if($usr->product_stock>0){?>
                                <i onclick="updateqty(<?= $usr->transactiond_id; ?>,'tambah')" class="fa fa-plus text-small text-success pointer"></i>
                                <?php }?>
                                <?php }else{?>
                                    <?= number_format($qty,0,",",".") ?> <?= $usr->unit_name; ?>     
                                <?php }?>   
                            </td>
                            <td class="text-small0"><?= number_format($price,0,",",".") ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th colspan="5">Total</th>
                        <th>
                            <?= number_format($tprice,0,",","."); ?>
                            <input type="hidden" id="tagihan" value="<?=$tprice;?>"/>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="5">
                            <?php 
                            if (
                                (
                                    isset(session()->get("position_administrator")[0][0]) 
                                    && (
                                        session()->get("position_administrator") == "1" 
                                        || session()->get("position_administrator") == "2"
                                    )
                                ) ||
                                (
                                    isset(session()->get("halaman")['pos']['act_create']) 
                                    && session()->get("halaman")['pos']['act_create'] == "1"
                                )
                            ) { ?>
                            <button class="btn btn-md btn-success" onclick="bayar();"><span class="fa fa-money" style="color:white;"></span>  Bayar</button>        
                            <?php }else{echo "Bayar";}?>                    
                            <input type="hidden" id="bayarannya" value="<?=$transaction->transaction_pay;?>"/>
                        </th>
                        <th class="dibayar"><?=$transaction->transaction_pay;?></th>
                    </tr>
                    <tr>
                        <th colspan="5">
                            Kembalian          
                            <input type="hidden" id="kembaliannya" value="<?=$transaction->transaction_change;?>"/>
                            <input type="hidden" id="status" value="<?=$transaction->transaction_status;?>"/>
                        </th>
                        <th class="kembalian"><?=$transaction->transaction_change;?></th>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
        }
    }

    public function listproductgambar(){
        $builder = $this->db->table("product");
         if($this->request->getGet("product_name")!=""){
            $builder->like("product.product_name",$this->request->getGet("product_name"),"BOTH");
        }
        $product=$builder->orderBy("product_name")->get();
        foreach ($product->getResult() as $product) {?>
        <?php 
        if (
            (
                isset(session()->get("position_administrator")[0][0]) 
                && (
                    session()->get("position_administrator") == "1" 
                    || session()->get("position_administrator") == "2"
                )
            ) ||
            (
                isset(session()->get("halaman")['pos']['act_create']) 
                && session()->get("halaman")['pos']['act_create'] == "1"
            )
        ) {
            if($product->product_stock>0){
                $insertnota = "insertnota(".$product->product_id.")";
                $disabled="";
            }else{
                $insertnota = "toast('Info Stock', 'Stock Kosong!')";
                $disabled="disabled";
            }
        }else{$insertnota =""; $disabled="disabled";}?>
        <div class="col-3 divimg_product <?=$disabled;?>" onclick="<?=$insertnota;?>" >
            <figure class="caption-1 pointer">
                <img src="<?=base_url("images/product_picture/".$product->product_picture);?>" alt="" class="w-100 card-img-top  img_product">
                <figcaption class="px-5 py-4 text-center text-light">
                    <h2 class="h5 font-weight-bold mb-0 text-small1 text-light figcaption"><?=$product->product_name;?></h2>
                    <p class="text-small2 figcaption"><?=number_format($product->product_sell,0,",",".");?></p>
                </figcaption>
            </figure>
        </div>
        <?php }
    }

    public function listproductlist(){
       ?>
            <!-- <table id="" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%"> -->
                <table id="dataTable" class="table table-condensed table-hover ">
                <thead class="">
                    <tr>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Ube</th>
                        <th>Batch</th>
                        <th>Exp Date</th>
                        <th>Stock</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $builder = $this->db->table("product");
                    $usr1 = $builder->join("category", "category.category_id=product.category_id", "left")
                        ->join("unit", "unit.unit_id=product.unit_id", "left")
                        ->join("store", "store.store_id=product.store_id", "left")
                        ->where("product.store_id",session()->get("store_id"));
                    if($this->request->getGet("product_name")!=""){
                        $usr1->like("product.product_name",$this->request->getGet("product_name"),"BOTH");
                    }
                    $usr=$usr1->orderBy("product_name", "ASC")
                        ->get();
                    // echo $this->db->getLastquery();
                    $no = 1;
                    foreach ($usr->getResult() as $usr) { ?>
                    <?php 
                        if (
                            (
                                isset(session()->get("position_administrator")[0][0]) 
                                && (
                                    session()->get("position_administrator") == "1" 
                                    || session()->get("position_administrator") == "2"
                                )
                            ) ||
                            (
                                isset(session()->get("halaman")['pos']['act_create']) 
                                && session()->get("halaman")['pos']['act_create'] == "1"
                            )
                        ) {
                            if($usr->product_stock>0){
                                $insertnota = "insertnota(".$usr->product_id.")";
                                $disabled="";
                            }else{
                                $insertnota = "toast('Info Stock', 'Stock Kosong!')";
                                $disabled="disabled";
                            }
                        }else{$insertnota ="";}?>
                        <tr class="pointer <?=$disabled;?>" onclick="<?=$insertnota;?>">                            
                            <td><?= $usr->category_name; ?></td>
                            <td><?= $usr->product_name; ?></td>
                            <td><?= $usr->product_ube; ?></td>
                            <td><?= $usr->product_batch; ?></td>
                            <td><?= $usr->product_expiredate; ?></td>
                            <?php 
                            $limit=$usr->product_countlimit; 
                            $stock=$usr->product_stock;
                            if($limit>=$stock){$alstock="danger";}else{$alstock="default";}
                            ?>
                            <td><span class="text-<?=$alstock;?>"><?= number_format($stock,0,",","."); ?></span> <?= $usr->unit_name; ?></td>
                            <?php 
                            $buy=$usr->product_buy; 
                            $sell=$usr->product_sell;
                            $margin=$sell-$buy;
                            ?>
                            <td><?= number_format($sell,0,",","."); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
       <?php
    }
}
