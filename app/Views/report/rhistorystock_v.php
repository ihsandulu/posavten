<?php echo $this->include("template/header_v"); ?>

<div class='container-fluid'>
    <div class='row'>
        <div class='col-12'>
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <?php if (!isset($_GET['user_id']) && !isset($_POST['new']) && !isset($_POST['edit'])) {
                            $coltitle = "col-md-10";
                        } else {
                            $coltitle = "col-md-8";
                        } ?>
                        <div class="<?= $coltitle; ?>">
                            <h4 class="card-title"></h4>
                            <!-- <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6> -->
                        </div>
                    </div>

                    
                    <?php 
                    if(isset($_GET["from"])&&$_GET["from"]!=""){
                        $from=$_GET["from"];
                    }else{
                        $from=date("Y-m-d");
                    }

                    if(isset($_GET["to"])&&$_GET["to"]!=""){
                        $to=$_GET["to"];
                    }else{
                        $to=date("Y-m-d");
                    }

                    if(isset($_GET["productid"])&&$_GET["productid"]>0){
                        $productid=$_GET["productid"];
                    }else{
                        $productid=0;
                    }

                    if(isset($_GET["productidn"])){
                        $productidn=$_GET["productidn"];
                    }else{
                        $productidn="";
                    }

                    ?>

                    <form class="form-inline" >
                        <label for="from">Dari:</label>&nbsp;
                        <input type="date" id="from" name="from" class="form-control" value="<?=$from;?>">&nbsp;
                        <label for="to">Ke:</label>&nbsp;
                        <input type="date" id="to" name="to" class="form-control" value="<?=$to;?>">&nbsp;
                        <label for="to">Produk:</label>&nbsp;
                        <select onchange="ob();" id="productid" name="productid" class="form-control select">
                            <option value="0" <?=($productid=="0")?"selected":"";?>>Pilih Produk</option>
                            <?php $product=$this->db->table("product")->orderBy("product_name","ASC")->get();
                            foreach($product->getResult() as $product){?>
                                <option value="<?=$product->product_id;?>" productidn="<?=$product->product_name;?>" <?=($productid==$product->product_id)?"selected":"";?>><?=$product->product_name;?></option>
                            <?php }?>
                        </select>
                        <input type="hidden" id="productidn" name="productidn" value="<?=$productidn;?>">&nbsp;
                        <script>
                        function ob(){
                            let ob=$("#productid option:selected");
                            let productidn = ob.attr("productidn");
                            $("#productidn").val(productidn);
                        }
                        </script>
                        &nbsp;
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>

                        <?php if ($message != "") { ?>
                            <div class="alert alert-info alert-dismissable">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong><?= $message; ?></strong>
                            </div>
                        <?php } ?>

                        <div class="table-responsive m-t-40">
                            <div class="row">
                                <div class="bold text-primary  col-md-12">Nama Product : <span id="namaproductid" class=""><?=$productidn;?></span></div>
                                <!-- <div class="bold col-md-6 text-right">Stok Terakhir Periode Ini : <span id="stok" class=""></span></div> -->
                            </div>
                            <table id="example231" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                                <thead class="">
                                    <tr>
                                        <th>No.</th>
                                        <th>Tgl</th>
                                        <th>Product</th>
                                        <th>Nomor Nota</th>
                                        <th>Awal</th>
                                        <th>Masuk</th>
                                        <th>Keluar</th>
                                        <th>Akhir</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    //masuk
                                    $builder = $this->db
                                    ->table("transactiond")
                                    ->join("transaction", "transaction.transaction_id=transactiond.transaction_id", "left")
                                    ->join("user", "user.user_id=transaction.cashier_id", "left")
                                    ->join("store", "store.store_id=transactiond.store_id", "left")
                                    ->where("transactiond.store_id",session()->get("store_id"))
                                    ->where("transactiond.product_id",$productid);
                                    if(isset($_GET["from"])&&$_GET["from"]!=""){
                                        $builder->where("transaction.transaction_date >=",$this->request->getGet("from"));
                                    }else{
                                        $builder->where("transaction.transaction_date",date("Y-m-d"));
                                    }
                                    if(isset($_GET["to"])&&$_GET["to"]!=""){
                                        $builder->where("transaction.transaction_date <=",$this->request->getGet("to"));
                                    }else{
                                        $builder->where("transaction.transaction_date",date("Y-m-d"));
                                    }
                                    $masuk= $builder
                                        ->orderBy("transaction_date", "ASC")
                                        ->get();
                                    // echo $this->db->getLastquery();
                                    $arraya=array();
                                    $arrayb=array();
                                    foreach ($masuk->getResult() as $masuk) { 
                                        $arraya["tgl"]=$masuk->transaction_date;
                                        $arraya["produk"]=$productidn;
                                        $arraya["nota"]=$masuk->transaction_no;
                                        $arraya["awal"]=$masuk->transactiond_stokawal;
                                        $arraya["masuk"]=0;
                                        $arraya["keluar"]=$masuk->transactiond_qty;
                                        $arraya["akhir"]=$masuk->transactiond_stokakhir;
                                        $arraya["user"]=$masuk->user_name;
                                        $arrayb[]=$arraya;
                                    } 

                                    //keluar
                                    $builder = $this->db
                                    ->table("purchased")
                                    ->join("purchase", "purchase.purchase_id=purchased.purchase_id", "left")
                                    ->join("user", "user.user_id=purchase.cashier_id", "left")
                                    ->join("store", "store.store_id=purchased.store_id", "left")
                                    ->where("purchased.store_id",session()->get("store_id"))
                                    ->where("purchased.product_id",$productid);
                                    if(isset($_GET["from"])&&$_GET["from"]!=""){
                                        $builder->where("purchase.purchase_date >=",$this->request->getGet("from"));
                                    }else{
                                        $builder->where("purchase.purchase_date",date("Y-m-d"));
                                    }
                                    if(isset($_GET["to"])&&$_GET["to"]!=""){
                                        $builder->where("purchase.purchase_date <=",$this->request->getGet("to"));
                                    }else{
                                        $builder->where("purchase.purchase_date",date("Y-m-d"));
                                    }
                                    $masuk= $builder
                                        ->orderBy("purchase_date", "ASC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    foreach ($masuk->getResult() as $masuk) { 
                                        $arraya["tgl"]=$masuk->purchase_date;
                                        $arraya["produk"]=$productidn;
                                        $arraya["nota"]=$masuk->purchase_no;
                                        $arraya["awal"]=$masuk->purchased_stokawal;
                                        $arraya["masuk"]=$masuk->purchased_qty;
                                        $arraya["keluar"]=0;
                                        $arraya["akhir"]=$masuk->purchased_stokakhir;
                                        $arraya["user"]=$masuk->user_name;
                                        $arrayb[]=$arraya;
                                    } 
                                    
                                    
                                    // print_r($arrayb);                                    
                                    usort($arrayb, function($a, $b) {
                                        return $a['tgl'] <=> $b['tgl'];
                                    });
                                    // print("<br/><br/>Sorted object array:<br/><br/>");
                                    // print_r($arrayb);


                                    $no = 1;
                                    $productid=0;
                                    $tmasuk=0;
                                    $tkeluar=0;
                                    foreach($arrayb as $a5){
                                        $tmasuk=+$a5["masuk"];
                                        $tkeluar=+$a5["keluar"];
                                    ?>
                                        <tr>                                            
                                            <td><?= $no++; ?></td>
                                            <td><?= $a5["tgl"]; ?></td>
                                            <td><?= $a5["produk"]; ?></td>
                                            <td><?= $a5["nota"]; ?></td>
                                            <td><?= $a5["awal"]; ?></td>
                                            <td><?= $a5["masuk"]; ?></td>
                                            <td><?= $a5["keluar"]; ?></td>
                                            <td><?= $a5["akhir"]; ?></td>
                                            <td><?= $a5["user"]; ?></td>
                                        </tr>
                                    <?php } ?>
                                    
                                    <tr>
                                        <td><?= $no; ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= number_format($tmasuk,0,".",","); ?></td>
                                        <td><?= number_format($tkeluar,0,".",","); ?></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select').select2();
    var title = "History Stok";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>