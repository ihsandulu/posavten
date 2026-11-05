<?php echo $this->include("template/header_v"); ?>
<?php
$tnominal = 0;

if (isset($_GET["from"])) {
    $from = $this->request->getGet("from");
} else {
    $from = date("Y-m-d");
}
if (isset($_GET["to"])) {
    $to = $this->request->getGet("to");
} else {
    $to = date("Y-m-d");
}
?>
<div class='container-fluid'>
    <div class='row'>
        <div class='col-12'>
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <?php if (!isset($_GET['user_id']) && !isset($_POST['new']) && !isset($_POST['edit']) && !isset($_GET['purchase_id'])) {
                            $coltitle = "col-md-10";
                        } else {
                            $coltitle = "col-md-8";
                        } ?>
                        <div class="<?= $coltitle; ?>">
                            <h4 class="card-title"></h4>
                            <!-- <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6> -->
                        </div>
                        <?php if (isset($_GET['purchase_id'])) { ?>
                            <a href="<?= urldecode($this->request->getGet("url")); ?>" method="get" class="col-md-2">
                                <h1 class="page-header col-md-12">
                                    <button class="btn btn-warning btn-block btn-lg" value="OK" style="">Back</button>
                                </h1>
                            </a>
                        <?php } ?>
                        <?php if (!isset($_POST['new']) && !isset($_POST['edit']) && !isset($_GET['report'])) { ?>
                            <?php if (isset($_GET["user_id"])) { ?>
                                <form action="<?= site_url("user"); ?>" method="get" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button class="btn btn-warning btn-block btn-lg" value="OK" style="">Back</button>
                                    </h1>
                                    <input name="from" value="<?= $from; ?>" type="hidden" />
                                    <input name="to" value="<?= $to; ?>" type="hidden" />
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
                                    isset(session()->get("halaman")['20']['act_create'])
                                    && session()->get("halaman")['20']['act_create'] == "1"
                                )
                            ) { ?>
                                <form method="post" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                        <input type="hidden" name="payment_id" />
                                    </h1>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Pembayaran";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Pembayaran";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="supplier_id">Supplier:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $builder = $this->db->table("supplier")
                                            ->where("store_id", session()->get("store_id"));
                                        if (isset($_GET["supplier_id"])) {
                                            $builder->where("supplier_id", $this->request->getGet("supplier_id"));
                                        } else {
                                            $builder->where("supplier_bill >", "0");
                                        }
                                        $supplier = $builder
                                            ->orderBy("supplier_name", "ASC")
                                            ->get();
                                        // echo $this->db->getLastQuery();
                                        ?>
                                        <select required class="form-control select" id="supplier_id" name="supplier_id">
                                            <option value="" <?= ($supplier_id == "") ? "selected" : ""; ?>>Pilih Supplier</option>
                                            <?php
                                            foreach ($supplier->getResult() as $supplier) { ?>
                                                <option value="<?= $supplier->supplier_id; ?>" <?= ($supplier_id == $supplier->supplier_id) ? "selected" : ""; ?>><?= $supplier->supplier_name; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="payment_nominal">Nominal:</label>
                                    <div class="col-sm-10">
                                        <input required type="number" autofocus class="form-control" id="payment_nominal" name="payment_nominal" placeholder="" value="<?= $payment_nominal; ?>">
                                    </div>
                                </div>


                                <input type="hidden" name="payment_id" value="<?= $payment_id; ?>" />
                                <input type="hidden" name="payment_nominal_before" value="<?= $payment_nominal; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href=<?= site_url("payment?from=" . $from . "&to=" . $to); ?>">Back</button>
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
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>

                        <?php if ($message != "") { ?>
                            <div class="alert alert-info alert-dismissable">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong><?= $message; ?></strong>
                            </div>
                        <?php } ?>

                        <div class="table-responsive m-t-40">
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                                <thead class="">
                                    <tr>
                                        <?php if (!isset($_GET["report"])) { ?>
                                            <th>Aksi</th>
                                        <?php } ?>
                                        <th>No.</th>
                                        <th>Tanggal</th>
                                        <th>Toko</th>
                                        <th>Supplier</th>
                                        <th>No. Pembayaran</th>
                                        <th>Kasir</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                        ->table("payment")
                                        ->join("purchase", "purchase.purchase_id=payment.purchase_id", "left")
                                        ->join("supplier", "supplier.supplier_id=payment.supplier_id", "left")
                                        ->join("store", "store.store_id=payment.store_id", "left")
                                        ->join("user", "user.user_id=payment.cashier_id", "left")
                                        ->where("payment.store_id", session()->get("store_id"));
                                    if (isset($_GET["from"]) && $_GET["from"] != "") {
                                        $builder->where("payment.payment_date >=", $this->request->getGet("from"));
                                    } else {
                                        $builder->where("payment.payment_date", date("Y-m-d"));
                                    }
                                    if (isset($_GET["to"]) && $_GET["to"] != "") {
                                        $builder->where("payment.payment_date <=", $this->request->getGet("to"));
                                    } else {
                                        $builder->where("payment.payment_date", date("Y-m-d"));
                                    }
                                    if (isset($_GET["purchase_id"]) && $_GET["purchase_id"] > 0) {
                                        $builder->where("payment.purchase_id", $this->request->getGet("purchase_id"));
                                    }
                                    $usr = $builder
                                        ->orderBy("payment_id", "DESC")
                                        ->get();
                                    // echo $this->db->getLastquery();die;
                                    $no = 1;
                                    $tnominal = 0;
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
                                                            isset(session()->get("halaman")['20']['act_update'])
                                                            && session()->get("halaman")['20']['act_update'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                            <input type="hidden" name="payment_id" value="<?= $usr->payment_id; ?>" />
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
                                                            isset(session()->get("halaman")['20']['act_delete'])
                                                            && session()->get("halaman")['20']['act_delete'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                            <input type="hidden" name="payment_id" value="<?= $usr->payment_id; ?>" />
                                                            <input type="hidden" name="supplier_id" value="<?= $usr->supplier_id; ?>" />
                                                            <input type="hidden" name="payment_nominal" value="<?= $usr->payment_nominal; ?>" />
                                                        </form>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->payment_date; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $usr->supplier_name; ?></td>
                                            <td>
                                                <?= $usr->payment_no; ?>
                                                <?php if ($usr->purchase_no != '') {
                                                    echo "<br/>(" . $usr->purchase_no . ")";
                                                } ?>
                                            </td>
                                            <td><?= $usr->user_name; ?></td>
                                            <td><?= number_format($usr->payment_nominal, 0, ".", ",");
                                                $tnominal += $usr->payment_nominal; ?></td>
                                        </tr>
                                    <?php } ?>

                                    <tr>
                                        <?php if (!isset($_GET["report"])) { ?>
                                            <td></td>
                                        <?php } ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">Total&nbsp;</td>
                                        <td><?= number_format($tnominal, 0, ".", ","); ?></td>
                                    </tr>
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
    <?php if (isset($_GET["purchase_no"])) {
        $purchase_no = $_GET["purchase_no"] . " | Tagihan : " . number_format($_GET["kas_nominal"], 0, ".", ",") . " | Sisa : " . number_format($_GET["kas_nominal"] - $tpayment, 0, ".", ",");
    } else {
        $purchase_no = "";
    } ?>
    var title = "Pembayaran <?= $purchase_no; ?>";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>