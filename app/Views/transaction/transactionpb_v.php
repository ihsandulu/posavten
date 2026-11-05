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
                                        <input type="hidden" name="cicilan_id" />
                                    </h1>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Detail Pembayaran";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Detail Pembayaran";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="cicilan_date">Date:</label>
                                    <div class="col-sm-10">
                                        <input required type="date"  class="form-control" id="cicilan_date" name="cicilan_date" placeholder="" value="<?= $cicilan_date?$cicilan_date:date("Y-m-d"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="cicilan_nominal">Nominal:</label>
                                    <div class="col-sm-10">
                                        <input required type="text"  class="form-control" id="cicilan_nominal" name="cicilan_nominal" placeholder="" value="<?= $cicilan_nominal; ?>">
                                    </div>
                                </div>



                                <input type="hidden" name="cicilan_id" value="<?= $cicilan_id; ?>" />
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
                                        <th>Date</th>
                                        <th>Cicilan Ke</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                        ->table("cicilan")
                                        ->where("cicilan.transaction_id", $this->request->getGet("transaction_id"));
                                    if (isset($_GET["from"]) && $_GET["from"] != "") {
                                        $builder->where("cicilan.cicilan_date >=", $this->request->getGet("from"));
                                    }
                                    if (isset($_GET["to"]) && $_GET["to"] != "") {
                                        $builder->where("cicilan.cicilan_date <=", $this->request->getGet("to"));
                                    }
                                    $usr = $builder
                                        ->orderBy("cicilan_id", "ASC")
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
                                                            <input type="hidden" name="cicilan_id" value="<?= $usr->cicilan_id; ?>" />
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
                                                            <input type="hidden" name="cicilan_id" value="<?= $usr->cicilan_id; ?>" />
                                                            <input type="hidden" name="cicilan_date" value="<?= $usr->cicilan_date; ?>" />
                                                        </form>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td><?= $usr->cicilan_date; ?></td>
                                            <td>Cicilan <?= $no++; ?></td>
                                            <td><?= number_format($usr->cicilan_nominal, 0, ".", ","); ?></td>
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
    var title = "<?= (isset($_GET["report"])) ? "Laporan" : ""; ?> Detail Pembayaran";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>