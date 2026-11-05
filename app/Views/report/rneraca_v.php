<?php echo $this->include("template/header_v"); ?>
<style>
td{padding: 0px  10px 0px 10px  !important;}
</style>
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
                            <!-- <h4 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h4> -->
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

                    <form class="form-inline mb-5" >
                        <label for="from">Dari:</label>&nbsp;
                        <input oninput="shift()" type="date" id="from" name="from" class="form-control tgl" value="<?=$from;?>">&nbsp;
                        <label for="to">Ke:</label>&nbsp;
                        <input oninput="shift()" type="date" id="to" name="to" class="form-control tgl" value="<?=$to;?>">&nbsp;
                        <label for="to">Shift:</label>&nbsp;
                        <select id="shift" name="shift" class="form-control">
                            <option value="0"  <?=(isset($_GET["shift"])&&$_GET["shift"]=='0')?"selected":"";?>>Semua Shift</option>
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
                        </select>&nbsp;
                        <script>
                        $(document).on('input', '.tgl', function(){
                            shift();
                        });
                        function shift(){
                            let from = $("#from").val();
                            let to = $("#to").val();
                            // alert('<?=base_url("rneracashift");?>?from='+from+'&to='+to);
                            $.get("<?=base_url("rneracashift");?>",{from:from,to:to})
                            .done(function(data){
                                $("#shift").html(data);
                            });
                        }
                        </script>
                        <?php 
                        if(isset($_GET["tanpamodal"])&&$_GET["tanpamodal"]!=""){
                            $checked="checked";
                        }else{$checked="";} ?>
                        <button type="submit" class="btn btn-primary">Submit</button>&nbsp;
                        <button type="button" onclick="print();" class="btn btn-warning"><span class="fa fa-print"></span> Print</button>
                    </form>

                       
                        
                    

                    <div class="bold  mt-5 pb-3 h4 col-12 text-center" style="border-bottom:black solid 1px;">
                        Neraca :
                        <?=(isset($_GET["from"]))?date("d M Y",strtotime($_GET["from"])):date("d M Y");?> s/d
                        <?=(isset($_GET["to"]))?date("d M Y",strtotime($_GET["to"])):date("d M Y");?>
                    </div>

                    <div class="bold text-primary pl-0 mt-5 mb-3 h4 col-12">Pemasukan <?=(isset($_GET["shift"])&&$_GET["shift"]>0)?"Shift ".$_GET["shift"]:"";?> : <span id="pemasukan" class=""></span></div>
                    <table id="" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                        <thead class="">
                            <tr>
                                <th>Akun</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $usr = $this->db
                            ->table("account")
                            ->where("store_id",session()->get("store_id"))
                            ->where("account_type","Debet")
                            ->orderBy("account_sort", "ASC")
                            ->get();
                            // echo $this->db->getLastquery();
                            $pemasukan=0;
                            foreach ($usr->getResult() as $usr) {
                                $builder = $this->db
                                ->table("kas")                                            
                                ->select("SUM(kas_nominal)AS tnom")
                                ->join("store", "store.store_id=kas.store_id", "left")
                                ->where("kas.account_id",$usr->account_id)
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
                                if(isset($_GET["shift"])&&$_GET["shift"]>0){
                                    $builder->where("kas.kas_shift",$this->request->getGet("shift"));
                                }
                                $kas= $builder
                                    ->groupBy("kas.account_id")
                                    ->get();
                            // echo $this->db->getLastquery();
                                    $tnom=0;
                                    foreach($kas->getResult() as $kas){$tnom=$kas->tnom;}
                                    if($tnom==null){$tnom=0;}
                                ?>
                                <tr>                        
                                    <td class="text-left"><?= $usr->account_name; ?></td>
                                    <td class="text-right"><?= number_format($tnom,0,".",",");$pemasukan+=$tnom; ?></td>
                                </tr>
                            <?php } ?>
                            
                            <tr>
                                <td class="text-left">Total&nbsp;</td>
                                <td class="text-right"><?= number_format($pemasukan,0,".",","); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="bold text-primary pl-0 mt-5 mb-3 h4 col-12">Pengeluaran <?=(isset($_GET["shift"])&&$_GET["shift"]>0)?"Shift ".$_GET["shift"]:"";?> : <span id="pengeluaran" class=""></span></div>
                    <table id="" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                        <thead class="">
                            <tr>
                                <th>Akun</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $usr = $this->db
                            ->table("account")
                            ->where("account_type","Kredit")
                            ->orderBy("account_sort", "ASC")
                            ->get();
                            // echo $this->db->getLastquery();
                            $pengeluaran=0;
                            foreach ($usr->getResult() as $usr) {
                                $builder = $this->db
                                ->table("kas")                                            
                                ->select("SUM(kas_nominal)AS tnom")
                                ->join("store", "store.store_id=kas.store_id", "left")
                                ->where("kas.account_id",$usr->account_id)
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
                                if(isset($_GET["shift"])&&$_GET["shift"]>0){
                                    $builder->where("kas.kas_shift",$this->request->getGet("shift"));
                                }
                                $kas= $builder
                                    ->groupBy("kas.account_id")
                                    ->get();
                                    $tnom=0;
                                    foreach($kas->getResult() as $kas){$tnom=$kas->tnom;}
                                    if($tnom==null){$tnom=0;}
                                ?>
                                <tr>                        
                                    <td class="text-left"><?= $usr->account_name; ?></td>
                                    <td class="text-right"><?= number_format($tnom,0,".",",");$pengeluaran+=$tnom; ?></td>
                                </tr>
                            <?php } ?>
                            
                            <tr>
                                <td class="text-left">Total&nbsp;</td>
                                <td class="text-right"><?= number_format($pengeluaran,0,".",","); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="bold text-primary pl-0 mt-5 mb-3 h4 col-12">Produk Terjual <?=(isset($_GET["shift"])&&$_GET["shift"]>0)?"Shift ".$_GET["shift"]:"";?> : <span id="produk_terjual" class=""></span></div>
                    <table id="" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">                        
                        <tbody>                           
                            <tr>                        
                                <td class="text-left">Total&nbsp;</td>
                                <td class="text-right"><?php 
                                
                                $builder = $this->db
                                ->table("transactiond")                                            
                                ->select("SUM(transactiond_qty)AS tnom")
                                ->join("transaction", "transaction.transaction_id=transactiond.transaction_id", "left")
                                ->where("transactiond.store_id",session()->get("store_id"));
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
                                if(isset($_GET["shift"])&&$_GET["shift"]>0){
                                    $builder->where("transaction.transaction_shift",$this->request->getGet("shift"));
                                }
                                $transactiond= $builder
                                    ->get();
                                    $tnom=0;
                                    $produk_terjual=0;
                                    foreach($transactiond->getResult() as $transactiond){
                                        $tnom=$transactiond->tnom;
                                        $produk_terjual=$tnom;
                                    }
                                    if($tnom==null){$tnom=0;}
                                    if($produk_terjual==null){$produk_terjual=0;}
                                    echo number_format($tnom,0,".",",");
                                ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="bold text-success pl-0 mt-5 mb-3 h4 col-12">
                        Laba/Rugi : <span class="text-info">Rp. <?=number_format($pemasukan-$pengeluaran,0,".",",");?></span>
                    </div>
                    <script>
                        $("#pemasukan").html('Rp. <?= number_format($pemasukan,0,".",",");?>');
                        $("#pengeluaran").html('Rp. <?= number_format($pengeluaran,0,".",",");?>');
                        $("#produk_terjual").html('<?= number_format($produk_terjual,0,".",",");?> pcs');
                        function print(){
                            <?php 
                                if(isset($_GET["from"])&&$_GET["from"]!=""){
                                    $from="&from=".$_GET["from"];
                                }else{
                                    $from="";
                                }
                                if(isset($_GET["to"])&&$_GET["to"]!=""){
                                    $to="&to=".$_GET["to"];
                                }else{
                                    $to="";
                                }
                                if(isset($_GET["shift"])&&$_GET["shift"]>0){
                                    $shift="&shift=".$_GET["shift"];
                                }else{
                                    $shift="";
                                }
                                $url=base_url("rneracaprint?")."?print=OK".$from.$to.$shift;
                            ?>
                            window.open('<?=$url;?>','_blank');
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select').select2();
    var title = "Neraca";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>