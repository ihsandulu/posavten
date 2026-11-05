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

                        <form action="<?= base_url("rtransaction"); ?>" method="get" class="col-md-2">
                            <h1 class="page-header col-md-12">
                                <button class="btn btn-warning btn-block btn-lg" value="OK" style="">Back</button>
                            </h1>
                        </form>
                    </div>
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
                        <table id="example231" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                            <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                            <thead class="">
                                <tr>
                                    <th>No.</th>
                                    <th>Toko</th>
                                    <th>Produk</th>
                                    <th>No. Transaksi</th>
                                    <th>Qty</th>
                                    <th>Nominal</th>
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
                                    ->where("transaction.transaction_status", "0");
                                if (isset($_GET["from"]) && $_GET["from"] != "") {
                                    $builder->where("transaction_date >=", $this->request->getGet("from"));
                                } else {
                                    $builder->where("transaction_date", date("Y-m-d"));
                                }
                                if (isset($_GET["to"]) && $_GET["to"] != "") {
                                    $builder->where("transaction_date <=", $this->request->getGet("to"));
                                } else {
                                    $builder->where("transaction_date", date("Y-m-d"));
                                }
                                $usr = $builder
                                    ->orderBy("product_name", "ASC")
                                    ->get();
                                //echo $this->db->getLastquery();
                                $no = 1;
                                foreach ($usr->getResult() as $usr) { ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $usr->store_name; ?></td>
                                        <td class="text-left"><?= $usr->product_name; ?></td>
                                        <td><?= $usr->transaction_no; ?></td>
                                        <td class="text-right"><?= number_format($usr->transactiond_qty, 0, ".", ","); ?></td>
                                        <td class="text-right"><?= number_format($usr->transactiond_price, 0, ".", ","); ?></td>
                                    </tr>
                                <?php } ?>
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
    var title = "Laporan Produk Keluar";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>