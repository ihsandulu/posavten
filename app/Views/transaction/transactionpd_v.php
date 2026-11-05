<?php echo $this->include("template/header_v"); ?>
<style>
    th,
    td,
    tr {
        border: rgba(0, 0, 0, 0.2) solid 1px;
    }
</style>
<div class='container-fluid'>
    <div class='row'>
        <div class='col-12'>
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <?php if (!isset($_GET['user_id']) && !isset($_POST['new']) && !isset($_POST['edit']) && !isset($_GET['report'])) {
                            $coltitle = "col-md-8";
                            $report = "";
                        } else {
                            $coltitle = "col-md-8";
                            $report = "?report=OK";
                        } ?>
                        <div class="<?= $coltitle; ?>">
                            <h4 class="card-title"></h4>
                            <!-- <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6> -->
                        </div>

                        <form action="<?= base_url("transactionp"); ?>" method="get" class="col-md-2">
                            <h1 class="page-header col-md-12">
                                <button class="btn btn-warning btn-block btn-lg" value="OK" style="">Back</button>
                                <?php if (isset($_GET["report"])) { ?>
                                    <input name="report" value="OK" type="hidden" />
                                <?php } ?>
                            </h1>
                        </form>
                        <?php if (!isset($_GET["report"])) { ?>
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
                                    isset(session()->get("halaman")['30']['act_create'])
                                    && session()->get("halaman")['30']['act_create'] == "1"
                                )
                            ) { ?>
                                <form method="post" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                        <input type="hidden" name="transactiond_id" />
                                    </h1>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Detail Penjualan";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Detail Penjualan";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="member_id">Product:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $product = $this->db->table("product")
                                            ->join("sell", "sell.product_id=product.product_id", "left")
                                            ->where("product.store_id", session()->get("store_id"))
                                            ->where("product.product_customer", "1")
                                            ->where("sell.store_id", "1")
                                            ->where("sell.positionm_id", $positionm_id)
                                            ->orderBy("TRIM(`product`.`product_name`)", "ASC")
                                            ->get();
                                        // echo $this->db->getLastQuery();
                                        ?>
                                        <select onchange="harga()" required class="form-control select" id="product_id" name="product_id">
                                            <option data-transactiond_price="0" transactiond_price="0" value="" <?= ($product_id == "") ? "selected" : ""; ?>>Pilih Product</option>
                                            <?php
                                            foreach ($product->getResult() as $product) { ?>
                                                <option data-transactiond_price="<?= $product->sell_price; ?>" value="<?= $product->product_id; ?>" <?= ($product_id == $product->product_id) ? "selected" : ""; ?>><?= $product->product_name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <script>
                                            function harga() {                                                
                                                let transactiond_price = $("#product_id option:selected").data("transactiond_price");
                                                $("#transactiond_price").val(transactiond_price);
                                            }
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="transactiond_qty">Qty:</label>
                                    <div class="col-sm-10">
                                        <input required type="number"  class="form-control" id="transactiond_qty" name="transactiond_qty" placeholder="" value="<?= $transactiond_qty?$transactiond_qty:1; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="transactiond_price">Price:</label>
                                    <div class="col-sm-10">
                                        <input required type="text"  class="form-control" id="transactiond_price" name="transactiond_price" placeholder="" value="<?= $transactiond_price; ?>">
                                    </div>
                                </div>



                                <input type="hidden" name="transactiondqty" value="<?= $transactiond_qty; ?>" />
                                <input type="hidden" name="transactiond_id" value="<?= $transactiond_id; ?>" />
                                <input type="hidden" name="transaction_id" value="<?= $transaction_id; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button type="button" class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href='<?= base_url("transaction"); ?>'">Back</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php } else { ?>
                        
                        

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
                                        <th>Toko</th>
                                        <th>No. Transaksi</th>
                                        <th>Produk</th>
                                        <th>Qty</th>
                                        <th>Harga Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                        ->table("transactiond")
                                        ->join("transaction", "transaction.transaction_id=transactiond.transaction_id", "left")
                                        ->join("store", "store.store_id=transactiond.store_id", "left")
                                        ->join("product", "product.product_id=transactiond.product_id", "left")
                                        ->where("transactiond.store_id", session()->get("store_id"))
                                        ->where("transactiond.transaction_id", $this->request->getGet("transaction_id"));
                                    if (isset($_GET["from"]) && $_GET["from"] != "") {
                                        $builder->where("transactiond.transactiond_date >=", $this->request->getGet("from"));
                                    }
                                    if (isset($_GET["to"]) && $_GET["to"] != "") {
                                        $builder->where("transactiond.transactiond_date <=", $this->request->getGet("to"));
                                    }
                                    $usr = $builder
                                        ->orderBy("transactiond_id", "DESC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    foreach ($usr->getResult() as $usr) { ?>
                                        <tr>
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
                                                            isset(session()->get("halaman")['30']['act_update'])
                                                            && session()->get("halaman")['30']['act_update'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                            <input type="hidden" name="transactiond_id" value="<?= $usr->transactiond_id; ?>" />
                                                        </form>
                                                    <?php } ?>

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
                                                            isset(session()->get("halaman")['30']['act_delete'])
                                                            && session()->get("halaman")['30']['act_delete'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                            <input type="hidden" name="transactiond_id" value="<?= $usr->transactiond_id; ?>" />
                                                            <input type="hidden" name="transactiondqty" value="<?= $usr->transactiond_qty; ?>" />
                                                            <input type="hidden" name="product_id" value="<?= $usr->product_id; ?>" />
                                                        </form>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $usr->transaction_no; ?></td>
                                            <td><?= $usr->product_name; ?></td>
                                            <td><?= number_format($usr->transactiond_qty, 0, ".", ","); ?></td>
                                            <td><?= number_format($usr->transactiond_price, 0, ".", ","); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select').select2();
    var title = "<?= (isset($_GET["report"])) ? "Laporan" : ""; ?> Detail Penjualan";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>