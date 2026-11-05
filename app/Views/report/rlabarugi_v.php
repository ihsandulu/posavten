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
                        <?php 
                        if(isset($_GET["tanpamodal"])&&$_GET["tanpamodal"]!=""){
                            $checked="checked";
                        }else{$checked="";} ?>
                        <input <?=$checked;?> type="checkbox" id="tanpamodal" name="tanpamodal" class="form-control">&nbsp;Transaksi Murni&nbsp;
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                    <div class="alert alert-danger m-t-5">
                        Transaksi Murni adalah : Transaksi masuk dan keluar, tanpa :1. Modal awal dan 2. Penarikan setoran oleh owner.
                    </div>

                        <?php if ($message != "") { ?>
                            <div class="alert alert-info alert-dismissable">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong><?= $message; ?></strong>
                            </div>
                        <?php } ?>

                        <div class="table-responsive m-t-40">
                        <div class="bold text-primary h3">Pemasukan : <span id="pemasukan" class=""></span></div>
                            <table id="" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                                <thead class="">
                                    <tr>
                                        <th>No.</th>
                                        <th>Date</th>
                                        <th>Toko</th>
                                        <th>Shift</th>
                                        <th>Kas</th>
                                        <th>Tipe</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                    ->table("kas")
                                    ->join("store", "store.store_id=kas.store_id", "left")
                                    ->where("kas.store_id",session()->get("store_id"))
                                    ->where("kas.kas_type",'masuk');
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
                                    if(isset($_GET["tanpamodal"])&&$_GET["tanpamodal"]!=""){
                                        $builder->where("kas.kas_modal !=","1");
                                        $builder->where("kas.kas_tariksetoran !=","1");
                                    }
                                    $usr= $builder
                                        ->orderBy("kas_id", "DESC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    $pemasukan=0;
                                    foreach ($usr->getResult() as $usr) { ?>
                                        <tr>                                            
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->kas_date; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $usr->kas_shift; ?></td>
                                            <td><?= $usr->kas_description; ?></td>
                                            <td><?= $usr->kas_type; ?></td>
                                            <td><?= number_format($usr->kas_nominal,0,".",",");$pemasukan+=$usr->kas_nominal; ?></td>
                                        </tr>
                                    <?php } ?>
                                    
                                    <tr>
                                        <td><?= $no; ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">Total&nbsp;</td>
                                        <td><?= number_format($pemasukan,0,".",","); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive m-t-40">
                        <div class="bold text-danger h3">Pengeluaran : <span id="pengeluaran" class=""></span></div>
                            <table id="example231" class="display nowrap table table-hover table-striped table-bordered " cellspacing="0" width="100%">
                                <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                                <thead class="">
                                    <tr>
                                        <th>No.</th>
                                        <th>Date</th>
                                        <th>Toko</th>
                                        <th>Shift</th>
                                        <th>Kas</th>
                                        <th>Tipe</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                    ->table("kas")
                                    ->join("store", "store.store_id=kas.store_id", "left")
                                    ->where("kas.store_id",session()->get("store_id"))
                                    ->where("kas.kas_type",'keluar');
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
                                    if(isset($_GET["tanpamodal"])&&$_GET["tanpamodal"]!=""){
                                        $builder->where("kas.kas_modal !=","1");
                                        $builder->where("kas.kas_tariksetoran !=","1");
                                    }
                                    $usr= $builder
                                        ->orderBy("kas_id", "DESC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    $pengeluaran=0;
                                    foreach ($usr->getResult() as $usr) { ?>
                                        <tr>                                            
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->kas_date; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $usr->kas_shift; ?></td>
                                            <td><?= $usr->kas_description; ?></td>
                                            <td><?= $usr->kas_type; ?></td>
                                            <td><?= number_format($usr->kas_nominal,0,".",",");$pengeluaran+=$usr->kas_nominal; ?></td>
                                        </tr>
                                    <?php } ?>                                    
                                    <tr>
                                        <td><?= $no; ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">Total&nbsp;</td>
                                        <td><?= number_format($pengeluaran,0,".",","); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <script>
                            $("#pemasukan").html('Rp. <?= number_format($pemasukan,0,".",",");?>');
                            $("#pengeluaran").html('Rp. <?= number_format($pengeluaran,0,".",",");?>');
                        </script>
                        <div class="table-responsive m-t-40">
                        <div class="bold text-success h3">Laba/Rugi : <span class="text-info">Rp. <?=number_format($pemasukan-$pengeluaran,0,".",",");?></span></div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select').select2();
    var title = "Laba Rugi";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>