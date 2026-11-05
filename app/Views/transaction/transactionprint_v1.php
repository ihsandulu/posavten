<?php echo $this->include("template/headersaja_v"); ?>
<style>
.separator {
  border-bottom: 1px dashed #aaa;
}
.text-small{font-size: 8px;}
.img_product{
    width:100%; 
    height:150px !important;    
  border:rgba(155, 155, 155, 0.5) solid 1px;
  border-radius:4px;
}
.pointer{cursor: pointer;}
.centerpage{
    position: fixed;
    left:50%;
    top:50%;
    transform:translate(-50%,-50%);
}
.hide{display: none;}
.absolute-top-right{
    position: absolute;
    right:5px;
    top:5px;
}
@media print {
    html, body, div{
        font-family:Arial, Helvetica, sans-serif;
        font-size:50px;
        margin: 0px !important;
        line-height:100%;
    }
    #storename_title{margin: bottom 30px, im !important;}
    p{margin-bottom:0px; font-size:50px;}
    @page {
        
    }	
    .tebal10{font-size:50px; font-weight:bold;}		
    .tebal12{font-size:52px; font-weight:bold;}	
    .tebal14{font-size:54px; font-weight:bold;}	
    .tebal16{font-size:56px; font-weight:bold;}		
    th, td{padding:0px 1px 0px 1px; font-size:50px; line-height: 100% !important;}
    .pagebreak{page-break-after: always;}
} 
.border{border:black solid 1px;}
</style>
<?php 
$store=$this->db->table("store")->where("store_id",session()->get("store_id"))->get()->getRow();
$builder=$this->db->table("transaction")
->where("transaction_id",$this->request->getGet("transaction_id"));
$transaction=$builder->get();
if($builder->countAll()>0){
    foreach ($transaction->getResult() as $transaction) {
    ?>
    <div class='container-fluid'>
        <div class='row'>
            <div class="col-12 row" style=" border-top:black solid  1px; border-bottom:black solid 1px; padding-top:25px; padding-bottom:25px;">	 
                <div class="col-12 text-center" id="storename_title" style="font-weight:bold; padding:0px; font-size:60px;"><?=$store->store_name;?></div>
                <div style="padding:5px;"></div>
                <div class="col-12 text-center" style="padding:0px; font-size:40px;"><?=$store->store_address;?></div>
                <div style="padding:15px;"></div>
                <div class="col-12 text-center" style="padding:0px; font-size:45px;">Mobile : <?=$store->store_phone;?>,  <?=session()->get("store_web");?></div> 
            </div>
            <div class="col-4 mt-3 p-0 tebal10" style="font-size:40px;">Invoice No.</div>
            <div class="col-8 mt-3 p-0 text-right" style="font-size:40px;"><?=$transaction->transaction_no;?></div>
            <div class="col-4 mb-3 p-0 tebal10" style="font-size:40px;">Date</div>
            <div class="col-8 mb-3 p-0 text-right" style="font-size:40px;"><?=date("d M Y",strtotime($transaction->transaction_date));?></div>
            <div class="col-12" style="padding:0px;"> 
                <table id="" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                    <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                    <thead class="">
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
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
                            <tr>            
                                <td class="text-left">
                                    <?= $no++; ?>. <?= $usr->product_name; ?><br/>
                                    <?= $usr->product_batch; ?>
                                </td>
                                <?php 
                                $qty=$usr->qty; 
                                $price=$usr->price; 
                                $tprice+=$price; 
                                ?>
                                <td>
                                    <?= number_format($qty,0,".",",") ?> <?= $usr->unit_name; ?> 
                                </td>
                                <td><?= number_format($price,0,".",",") ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <th>
                                <?= number_format($tprice,0,".",","); ?>
                                <input type="hidden" id="tagihan" value="<?=$tprice;?>"/>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="2" class="text-right">Bayar</th>
                            <th class="dibayar"><?=number_format($transaction->transaction_pay,0,".",",");?></th>
                        </tr>
                        <tr>
                            <th colspan="2" class="text-right">Kembalian</th>
                            <th class="kembalian"><?=number_format($transaction->transaction_change,0,".",",");?></th>
                        </tr>
                    </tbody>
                </table>                        
            </div>
            <div class="col-12 mt-3 pt-0 text-center" style="  ">   
                <?=$store->store_noteinvoice;?>
            </div>
           <!--  <div class="col-4 row mt-5 p-0" style=""  align="center">
                <div class="col-12"><strong class="tebal10">Hormat Kami,</strong></div>
                <div class="col-12" style="height:50px;">&nbsp;</div>
                <div class="col-12" style=""><strong><?=session()->get("user_name");?></strong></div>
            </div> -->
        </div>
    </div>

<?php }
}else{?>
    <h1 class="centerpage">Data tidak ditemukan!</h1>
<?php }?>
<script>
window.print();
setTimeout(function(){ this.close(); }, 500);
</script>

<?php echo  $this->include("template/footersaja_v"); ?>