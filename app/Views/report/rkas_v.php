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
                                    isset(session()->get("halaman")['15']['act_create'])
                                    && session()->get("halaman")['15']['act_create'] == "1"
                                )
                            ) { ?>
                                <form method="post" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                        <input type="hidden" name="kas_id" />
                                    </h1>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Kas";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Kas";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="kas_type">Type:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $builder = $this->db->table("supplier")
                                            ->where("store_id", session()->get("store_id"))
                                            ->where("supplier_bill >", "0");
                                        if (isset($_GET["kas_type"])) {
                                            $builder->where("kas_type", $this->request->getGet("kas_type"));
                                        }
                                        $supplier = $builder
                                            ->orderBy("supplier_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select required class="form-control select" id="kas_type" name="kas_type">
                                            <option value="" <?= ($kas_type == "") ? "selected" : ""; ?>>Pilih Tipe</option>
                                            <option value="masuk" <?= ($kas_type == "masuk") ? "selected" : ""; ?>>Debet</option>
                                            <option value="keluar" <?= ($kas_type == "keluar") ? "selected" : ""; ?>>Kredit</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="kas_date">Date:</label>
                                    <div class="col-sm-10">
                                        <input required type="date" autofocus class="form-control" id="kas_date" name="kas_date" placeholder="" value="<?= $kas_date; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="kas_nominal">Nominal:</label>
                                    <div class="col-sm-10">
                                        <input required type="number" autofocus class="form-control" id="kas_nominal" name="kas_nominal" placeholder="" value="<?= $kas_nominal; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="kas_description">Keterangan:</label>
                                    <div class="col-sm-10">
                                        <input required type="text" autofocus class="form-control" id="kas_description" name="kas_description" placeholder="" value="<?= $kas_description; ?>">
                                    </div>
                                </div>


                                <input type="hidden" name="store_id" value="<?= session()->get("store_id"); ?>" />
                                <input type="hidden" name="kas_id" value="<?= $kas_id; ?>" />
                                <input type="hidden" name="kas_asli" value="1" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href=<?= site_url("kas"); ?>">Back</button>
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
                                        ->where("kas.store_id", session()->get("store_id"));
                                    if (isset($_GET["from"]) && $_GET["from"] != "") {
                                        $builder->where("kas.kas_date >=", $this->request->getGet("from"));
                                    } else {
                                        $builder->where("kas.kas_date", date("Y-m-d"));
                                    }
                                    if (isset($_GET["to"]) && $_GET["to"] != "") {
                                        $builder->where("kas.kas_date <=", $this->request->getGet("to"));
                                    } else {
                                        $builder->where("kas.kas_date", date("Y-m-d"));
                                    }
                                    $usr = $builder
                                        ->orderBy("kas_id", "DESC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    $tkasnom = 0;
                                    foreach ($usr->getResult() as $usr) {
                                        if ($usr->kas_type == "masuk") {
                                            $tkasnom += $usr->kas_nominal;
                                        } else {
                                            $tkasnom -= $usr->kas_nominal;
                                        }
                                    ?>
                                        <tr>
                                            <?php if (!isset($_GET["report"])) { ?>
                                                <td style="padding-left:0px; padding-right:0px;">
                                                    <?php if ($usr->kas_asli == 1) { ?>
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
                                                                isset(session()->get("halaman")['15']['act_update'])
                                                                && session()->get("halaman")['15']['act_update'] == "1"
                                                            )
                                                        ) { ?>
                                                            <form method="post" class="btn-action" style="">
                                                                <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                                <input type="hidden" name="kas_id" value="<?= $usr->kas_id; ?>" />
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
                                                                isset(session()->get("halaman")['15']['act_delete'])
                                                                && session()->get("halaman")['15']['act_delete'] == "1"
                                                            )
                                                        ) { ?>
                                                            <form method="post" class="btn-action" style="">
                                                                <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                                <input type="hidden" name="kas_id" value="<?= $usr->kas_id; ?>" />
                                                            </form>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->kas_date; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= ($usr->kas_shift)?$usr->kas_shift:""; ?></td>
                                            <td><?= $usr->kas_description; ?></td>
                                            <td><?= $usr->kas_type; ?></td>
                                            <td><?= number_format($usr->kas_nominal, 0, ".", ","); ?></td>
                                        </tr>
                                    <?php } ?>

                                    <tr>
                                        <td></td>
                                        <td><?= $no; ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">Total&nbsp;</td>
                                        <td><?= number_format($tkasnom, 0, ".", ","); ?></td>
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
    var title = "Laporan Kas";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>