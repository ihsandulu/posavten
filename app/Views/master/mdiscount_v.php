<?php echo $this->include("template/header_v"); ?>
<style>
    .text-small {
        font-size: 10px;
        margin-bottom: 10px;
        line-height: 12px;
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
                                    isset(session()->get("halaman")['8']['act_create'])
                                    && session()->get("halaman")['8']['act_create'] == "1"
                                )
                            ) { ?>
                                <form method="post" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                        <input type="hidden" name="discount_id" />
                                    </h1>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Discount";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Discount";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="category_id">Category Member:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $positionm = $this->db->table("positionm")
                                            ->where("store_id", session()->get("store_id"))
                                            ->orderBy("positionm_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select autofocus required class="form-control select" id="positionm_id" name="positionm_id">
                                            <option value="" <?= ($positionm_id == "") ? "selected" : ""; ?>>Category Member</option>
                                            <?php
                                            foreach ($positionm->getResult() as $positionm) { ?>
                                                <option value="<?= $positionm->positionm_id; ?>" <?= ($positionm_id == $positionm->positionm_id) ? "selected" : ""; ?>><?= $positionm->positionm_name; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>
                                <hr />
                                <div class="form-group bg-secondary text-white p-5">
                                    <label class="control-label col-sm-2">Purchase:</label>
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label class="control-label col-sm-12" for="discount_percent<?= $positionm->positionm_id; ?>"><span style="font-weight:bold; color:powderblue;">Minimum</span>:</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="discount_buymin" name="discount_buymin" value="<?= $discount_buymin; ?>">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="control-label col-sm-12" for="discount_percent<?= $positionm->positionm_id; ?>"><span style="font-weight:bold; color:cyan;">Maximum</span>:</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="discount_buymax" name="discount_buymax" value="<?= $discount_buymax; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="form-group bg-secondary text-white p-5">
                                    <label class="control-label col-sm-2">Discount:</label>
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label class="control-label col-sm-12" for="discount_percent<?= $positionm->positionm_id; ?>"><span style="font-weight:bold; color:powderblue;">(Percent %)</span>:</label>
                                            <div class="col-sm-10">
                                                <input onkeyup="itungprice(<?= $positionm->positionm_id; ?>)" type="number" class="form-control" id="discount_percent<?= $positionm->positionm_id; ?>" name="discount_percent" value="<?= $discount_percent; ?>">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="control-label col-sm-12" for="discount_percent<?= $positionm->positionm_id; ?>"><span style="font-weight:bold; color:cyan;">(Price)</span>:</label>
                                            <div class="col-sm-10">
                                                <input onkeyup="itungpercent(<?= $positionm->positionm_id; ?>)" type="number" class="form-control" id="discount_nominal<?= $positionm->positionm_id; ?>" name="discount_nominal" value="<?= $discount_nominal; ?>">
                                            </div>
                                        </div>
                                        <script>
                                            function itungprice(id) {
                                                $("#discount_nominal" + id).val(0);
                                            }

                                            function itungpercent(id) {
                                                $("#discount_percent" + id).val(0);
                                            }
                                        </script>
                                    </div>
                                </div>

                                <input type="hidden" name="discount_id" value="<?= $discount_id; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <a type="button" class="btn btn-warning col-md-offset-1 col-md-5" href="<?= base_url("mdiscount"); ?>">Back</a>
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
                            <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                                <thead class="">
                                    <tr>
                                        <?php if (!isset($_GET["report"])) { ?>
                                            <th>Action</th>
                                        <?php } ?>
                                        <th>No.</th>
                                        <th>Member</th>
                                        <th>Rules</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // echo $x;die;
                                    $usr = $this->db
                                        ->table("discount")
                                        ->join("positionm", "positionm.positionm_id=discount.positionm_id", "left")
                                        ->join("store", "store.store_id=discount.store_id", "left")
                                        ->where("discount.store_id", session()->get("store_id"))
                                        ->orderBy("discount_id", "DESC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    foreach ($usr->getResult() as $usr) {
                                        if ($usr->positionm_id == 0) {
                                            $member = "";
                                        } else {
                                            $member = $usr->positionm_name;
                                        }
                                        if ($usr->discount_buymin > 0) {
                                            $lebihdari = "lebih dari atau sama dengan Rp. " . number_format($usr->discount_buymin,0,",",".");
                                        } else {
                                            $lebihdari = "";
                                        }
                                        if ($usr->discount_buymax > 0) {
                                            $kurangdari = "dan kurang dari atau sama dengan Rp. " . number_format($usr->discount_buymax,0,",",".");
                                        } else {
                                            $kurangdari = "";
                                        }
                                        if ($usr->discount_nominal == 0) {
                                            $diskon = "";
                                        } else {
                                            $diskon = number_format($usr->discount_nominal, 0, ",", ".");
                                        }
                                        if ($usr->discount_percent == 0) {
                                            $diskon = "";
                                        } else {
                                            $diskon = number_format($usr->discount_percent, 0, ",", "."). "%";
                                        }
                                        $rule = "Jika pembelian $member $lebihdari $kurangdari, maka mendapat diskon $diskon";
                                    ?>
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
                                                            isset(session()->get("halaman")['28']['act_update'])
                                                            && session()->get("halaman")['28']['act_update'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                            <input type="hidden" name="discount_id" value="<?= $usr->discount_id; ?>" />
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
                                                            isset(session()->get("halaman")['28']['act_delete'])
                                                            && session()->get("halaman")['28']['act_delete'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                            <input type="hidden" name="discount_id" value="<?= $usr->discount_id; ?>" />
                                                        </form>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->positionm_name; ?></td>
                                            <td><?= $rule; ?></td>
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
    var title = "Master Discount";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>