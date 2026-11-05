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

                        <form action="<?= base_url("transactionnon"); ?>" method="get" class="col-md-2">
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
                                    isset(session()->get("halaman")['29']['act_create'])
                                    && session()->get("halaman")['29']['act_create'] == "1"
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
                                $judul = "Update Detail Produk Keluar";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Detail Produk Keluar";
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
                                            ->where("store_id", session()->get("store_id"))
                                            ->orderBy("product_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select required onchange="unitsec()" class="form-control select" id="product_id" name="product_id">
                                            <option data-product-unitsec="" data-unit-id="" value="" <?= ($product_id == "") ? "selected" : ""; ?>>Pilih Produk</option>
                                            <?php foreach ($product->getResult() as $p) { ?>
                                                <option
                                                    data-product-unitsec="<?= $p->product_unitsec; ?>"
                                                    data-unit-id="<?= $p->unit_id; ?>"
                                                    value="<?= $p->product_id; ?>"
                                                    <?= ($product_id == $p->product_id) ? "selected" : ""; ?>>
                                                    <?= $p->product_name; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="transactiond_qty">Qty:</label>
                                    <div class="col-sm-10">
                                        <input required type="number" autofocus class="form-control" id="transactiond_qty" name="transactiond_qty" placeholder="" value="<?= $transactiond_qty; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="transactiond_unit">Unit:</label>
                                    <div class="col-sm-10">
                                        <select class="form-group" id="transactiond_unit" name="transactiond_unit">

                                        </select>
                                        <script>
                                            function unitsec() {
                                                let transactiond_unit = <?= json_encode($transactiond_unit ?? ''); ?>;
                                                let selected = $("#product_id option:selected");
                                                let unit_id = selected.data("unit-id");
                                                let product_unitsec = selected.data("product-unitsec");

                                                if (!unit_id || !product_unitsec) {
                                                    alert("Data unit tidak lengkap untuk produk ini.");
                                                    return;
                                                }

                                                $.get("<?= base_url('api/unitsecond'); ?>", {
                                                        transactiond_unit: transactiond_unit,
                                                        unit_id: unit_id,
                                                        product_unitsec: product_unitsec
                                                    })
                                                    .done(function(data) {
                                                        $("#transactiond_unit").html(data);
                                                    })
                                                    .fail(function() {
                                                        alert("Gagal memuat data unit kedua.");
                                                    });
                                            }
                                        </script>
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
                        <?php
                        if (isset($_GET["from"]) && $_GET["from"] != "") {
                            $from = $_GET["from"];
                        } else {
                            $from = date("Y-m-d");
                        }

                        if (isset($_GET["to"]) && $_GET["to"] != "") {
                            $to = $_GET["to"];
                        } else {
                            $to = date("Y-m-d");
                        }

                        ?>
                        <form class="form-inline">
                            <label for="from">Dari:</label>&nbsp;
                            <input type="date" id="from" name="from" class="form-control" value="<?= $from; ?>">&nbsp;
                            <label for="to">Ke:</label>&nbsp;
                            <input type="date" id="to" name="to" class="form-control" value="<?= $to; ?>">&nbsp;
                            <?php if (isset($_GET["report"])) { ?>
                                <input type="hidden" id="report" name="report" class="form-control" value="<?= $this->request->getGet("report"); ?>">&nbsp;
                            <?php } ?>
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
                                        <th>Toko</th>
                                        <th>No. Transaksi</th>
                                        <th>Produk</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                        ->table("transactiond")
                                        ->join("unit", "unit.unit_id=transactiond.transactiond_unit", "left")
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
                                                            isset(session()->get("halaman")['29']['act_update'])
                                                            && session()->get("halaman")['29']['act_update'] == "1"
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
                                                            isset(session()->get("halaman")['29']['act_delete'])
                                                            && session()->get("halaman")['29']['act_delete'] == "1"
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
                                            <td><?= $usr->unit_name; ?></td>
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
    var title = "<?= (isset($_GET["report"])) ? "Laporan" : ""; ?> Detail Barang Keluar";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>