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
                        <?php if (!isset($_GET['user_id']) && !isset($_POST['new']) && !isset($_POST['edit'])) {
                            $coltitle = "col-md-10";
                        } else {
                            $coltitle = "col-md-8";
                        } ?>
                        <div class="<?= $coltitle; ?>">
                            <h4 class="card-title"></h4>
                            <!-- <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6> -->
                        </div>

                        <?php if (!isset($_POST['new']) && !isset($_POST['edit']) && !isset($_GET['report'])) { ?>
                            <?php if (isset($_GET["user_id"])) { ?>
                                <form action="<?= site_url("user"); ?>" method="get" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button class="btn btn-warning btn-block btn-lg" value="OK" style="">Back</button>
                                    </h1>
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
                                    isset(session()->get("halaman")['29']['act_create'])
                                    && session()->get("halaman")['29']['act_create'] == "1"
                                )
                            ) { ?>
                                <form method="post" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                        <input type="hidden" name="transaction_id" />
                                    </h1>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Pembelian";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Pembelian";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="transaction_date">Tgl Pembelian:</label>
                                    <div class="col-sm-10">
                                        <input required type="date" autofocus class="form-control" id="transaction_date" name="transaction_date" placeholder="" value="<?= $transaction_date; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="member_id">Dikeluarkan Untuk:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $member = $this->db->table("member")
                                            ->where("store_id", session()->get("store_id"))
                                            ->orderBy("member_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select class="form-control select" id="member_id" name="Member_id">
                                            <option value="" <?= ($member_id == "") ? "selected" : ""; ?>>Pilih Member</option>
                                            <?php
                                            foreach ($member->getResult() as $member) { ?>
                                                <option value="<?= $member->member_id; ?>" <?= ($member_id == $member->member_id) ? "selected" : ""; ?>><?= $member->member_name; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>



                                <input type="hidden" name="transaction_id" value="<?= $transaction_id; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button type="button" class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href='<?= base_url("transactionnon"); ?>'">Back</button>
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
                                        <th>Tanggal</th>
                                        <th>Toko</th>
                                        <th>No. Transaksi</th>
                                        <th>Kasir</th>
                                        <th>Produk</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                        ->table("transaction")
                                        ->join("store", "store.store_id=transaction.store_id", "left")
                                        ->join("user", "user.user_id=transaction.cashier_id", "left")
                                        ->where("transaction.store_id", session()->get("store_id"));
                                    if (isset($_GET["from"]) && $_GET["from"] != "") {
                                        $builder->where("transaction.transaction_date >=", $this->request->getGet("from"));
                                    } else {
                                        $builder->where("transaction.transaction_date", date("Y-m-d"));
                                    }
                                    if (isset($_GET["to"]) && $_GET["to"] != "") {
                                        $builder->where("transaction.transaction_date <=", $this->request->getGet("to"));
                                    } else {
                                        $builder->where("transaction.transaction_date", date("Y-m-d"));
                                    }
                                    $usr = $builder
                                        ->orderBy("transaction_id", "ASC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    $tbill = 0;
                                    $tpay = 0;
                                    $tchange = 0;
                                    foreach ($usr->getResult() as $usr) {
                                    ?>
                                        <tr>
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
                                                        isset(session()->get("halaman")['29']['act_read'])
                                                        && session()->get("halaman")['29']['act_read'] == "1"
                                                    )
                                                ) { ?>

                                                    <?php if (isset($_GET["report"])) {
                                                        $report = "&report=OK";
                                                    } else {
                                                        $report = "";
                                                    } ?>
                                                    <a href="<?= base_url("transactionnond?transaction_id=" . $usr->transaction_id . "&transaction_no=" . $usr->transaction_no. $report); ?>" class="btn btn-xs btn-info"><span class="fa fa-cubes"></span></a>
                                                <?php } ?>

                                                

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
                                                            isset(session()->get("halaman")['29']['act_update'])
                                                            && session()->get("halaman")['29']['act_update'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                            <input type="hidden" name="transaction_id" value="<?= $usr->transaction_id; ?>" />
                                                        </form>
                                                    <?php } ?>
                                                <?php } ?>

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
                                                            isset(session()->get("halaman")['29']['act_delete'])
                                                            && session()->get("halaman")['29']['act_delete'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                            <input type="hidden" name="transaction_id" value="<?= $usr->transaction_id; ?>" />
                                                        </form>
                                                    <?php } ?>
                                                <?php } ?>
                                            </td>
                                            <td><?= $usr->transaction_date; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $usr->transaction_no; ?></td>
                                            <td><?= $usr->user_name; ?></td>
                                            <td>
                                                <?php $product = $this->db->table("transactiond")
                                                    ->join("product", "product.product_id=transactiond.product_id", "left")
                                                    ->where("transaction_id", $usr->transaction_id)
                                                    ->get();
                                                foreach ($product->getResult() as $product) {
                                                    echo $product->product_name . " (" . $product->transactiond_qty . "), ";
                                                }
                                                ?>
                                            </td>
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
    var title = "<?= (isset($_GET["report"])) ? "Laporan" : ""; ?> Barang Keluar";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>