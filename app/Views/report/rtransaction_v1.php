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

                        ?>
                        <form class="form-inline" >
                            <label for="from">Dari:</label>&nbsp;
                            <input type="date" id="from" name="from" class="form-control" value="<?=$from;?>">&nbsp;
                            <label for="to">Ke:</label>&nbsp;
                            <input type="date" id="to" name="to" class="form-control" value="<?=$to;?>">&nbsp;
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>

                        <?php if ($message != "") { ?>
                            <div class="alert alert-info alert-dismissable">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong><?= $message; ?></strong>
                            </div>
                        <?php } ?>

                        <div class="table-responsive m-t-40">
                            <table id="example231" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                                <thead class="">
                                    <tr>
                                        <th>No.</th>
                                        <th>Tanggal</th>
                                        <th>Toko</th>
                                        <th>No. Transaksi</th>
                                        <th>Shift</th>
                                        <th>Kasir</th>
                                        <th>Produk</th>
                                        <th>Tagihan</th>
                                        <th>Bayar</th>
                                        <th>Kembalian</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                        ->table("transaction")
                                        ->join("store", "store.store_id=transaction.store_id", "left")
                                        ->join("user", "user.user_id=transaction.cashier_id", "left")
                                        ->where("transaction.store_id",session()->get("store_id"));
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
                                    $usr= $builder
                                        ->orderBy("transaction_id", "ASC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    $tbill=0;
                                    $tpay=0;
                                    $tchange=0;
                                    foreach ($usr->getResult() as $usr) { 
                                        if($usr->transaction_bill==null){$usr->transaction_bill=0;}
                                        if($usr->transaction_pay==null){$usr->transaction_pay=0;}
                                        if($usr->transaction_change==null){$usr->transaction_change=0;}
                                        ?>
                                        <tr>                                            
                                            <td><a href="<?=base_url("rtransactiond?transaction_id=".$usr->transaction_id);?>" class="btn btn-xs btn-info"><span class="fa fa-cubes"></span> <?= $no++; ?></a></td>
                                            <td><?= $usr->transaction_date; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $usr->transaction_no; ?></td>
                                            <td><?= $usr->transaction_shift; ?></td>
                                            <td><?= $usr->user_name; ?></td>
                                            <td>
                                                <?php $product=$this->db->table("transactiond")
                                                ->join("product","product.product_id=transactiond.product_id","left")
                                                ->where("transaction_id",$usr->transaction_id)
                                                ->get();
                                                foreach ($product->getResult() as $product) {
                                                    echo $product->product_name." (".$product->transactiond_qty."), ";
                                                }
                                                ?>
                                            </td>
                                            <td><?= number_format($usr->transaction_bill,0,".",","); $tbill+=$usr->transaction_bill;?></td>
                                            <td><?= number_format($usr->transaction_pay,0,".",","); $tpay+=$usr->transaction_pay;?></td>
                                            <td><?= number_format($usr->transaction_change,0,".",","); $tchange+=$usr->transaction_change; ?></td>
                                            <td>
                                                <?php
                                                $status=array("sukses", "batal","pending");
                                                echo $status[$usr->transaction_status]; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    
                                        <tr>
                                            <td><?= $no; ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right">Total&nbsp;</td>
                                            <td><?= number_format($tbill,0,".",","); ?></td>
                                            <td><?= number_format($tpay,0,".",","); ?></td>
                                            <td><?= number_format($tchange,0,".",","); ?></td>
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
    var title = "Laporan Penjualan";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>