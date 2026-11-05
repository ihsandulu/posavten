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
                                        <input type="hidden" name="absen_id" />
                                    </h1>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Absensi";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Absensi";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="absen_date">Tgl:</label>
                                    <div class="col-sm-10">
                                        <input required type="date" autofocus class="form-control" id="absen_date" name="absen_date" placeholder="" value="<?= $absen_date; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="user_id">Nama:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $user = $this->db->table("user")
                                            ->where("store_id", session()->get("store_id"))
                                            ->orderBy("user_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select class="form-control select" id="user_id" name="user_id">
                                            <option value="" <?= ($user_id == "") ? "selected" : ""; ?>>Pilih Karyawan</option>
                                            <?php
                                            foreach ($user->getResult() as $user) { ?>
                                                <option value="<?= $user->user_id; ?>" <?= ($user_id == $user->user_id) ? "selected" : ""; ?>><?= $user->user_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="absen_resep">Status:</label>
                                    <div class="col-sm-10">
                                        <select onchange="iabsen()"  class="form-control select" id="absen_status" name="absen_status">
                                            <option value="0" <?= ($absen_status == "0") ? "selected" : ""; ?>>Hadir</option>
                                            <option value="1" <?= ($absen_status == "1") ? "selected" : ""; ?>>Sakit</option>
                                            <option value="2" <?= ($absen_status == "2") ? "selected" : ""; ?>>Izin</option>
                                            <option value="3" <?= ($absen_status == "3") ? "selected" : ""; ?>>Cuti</option>
                                            <option value="4" <?= ($absen_status == "4") ? "selected" : ""; ?>>Alpha</option>
                                        </select>
                                    </div>
                                <script>
                                    function iabsen(){
                                        let absen_status = $("#absen_status").val();
                                        let store_id = '<?=session()->get("store_id");?>';
                                        $.get("<?=base_url("api/iabsen");?>",{absen_status:absen_status,store_id:store_id})
                                        .done(function(data){
                                            $("#absen_potongan").val(data);
                                        });
                                    }
                                </script>

                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="absen_potongan">Potongan:</label>
                                    <div class="col-sm-10">
                                        <input required type="text" autofocus class="form-control" id="absen_potongan" name="absen_potongan" placeholder="" value="<?= $absen_potongan; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="absen_description">Keterangan:</label>
                                    <div class="col-sm-10">
                                        <input required type="text" autofocus class="form-control" id="absen_description" name="absen_description" placeholder="" value="<?= $absen_description; ?>">
                                    </div>
                                </div>


                                <input type="hidden" name="absen_id" value="<?= $absen_id; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button type="button" class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href='<?= base_url("absen"); ?>'">Back</button>
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
                                        <th>Nama</th>
                                        <th>Jenis Absensi</th>
                                        <th>Potongan</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                        ->table("absen")
                                        ->join("store", "store.store_id=absen.store_id", "left")
                                        ->join("user", "user.user_id=absen.user_id", "left")
                                        ->where("absen.store_id", session()->get("store_id"));
                                    if (isset($_GET["from"]) && $_GET["from"] != "") {
                                        $builder->where("absen.absen_date >=", $this->request->getGet("from"));
                                    } else {
                                        $builder->where("absen.absen_date", date("Y-m-d"));
                                    }
                                    if (isset($_GET["to"]) && $_GET["to"] != "") {
                                        $builder->where("absen.absen_date <=", $this->request->getGet("to"));
                                    } else {
                                        $builder->where("absen.absen_date", date("Y-m-d"));
                                    }
                                    $usr = $builder
                                        ->orderBy("absen.absen_id", "DESC")
                                        ->get();
                                    // echo $this->db->getLastquery();
                                    $no = 1;
                                    $tbill = 0;
                                    $tpay = 0;
                                    $tchange = 0;
                                    $status = array("Hadir", "Sakit", "Izin", "Cuti", "Alpha");
                                    foreach ($usr->getResult() as $usr) {
                                    ?>
                                        <tr>
                                            <td>
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
                                                            isset(session()->get("halaman")['31']['act_update'])
                                                            && session()->get("halaman")['31']['act_update'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                            <input type="hidden" name="absen_id" value="<?= $usr->absen_id; ?>" />
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
                                                            isset(session()->get("halaman")['31']['act_delete'])
                                                            && session()->get("halaman")['31']['act_delete'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                            <input type="hidden" name="absen_id" value="<?= $usr->absen_id; ?>" />
                                                        </form>
                                                    <?php } ?>
                                                <?php } ?>



                                            </td>
                                            <td><?= $usr->absen_date; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $usr->user_name; ?></td>
                                            <td><?= $status[$usr->absen_status]; ?></td>
                                            <td><?= number_format($usr->absen_potongan,0,",","."); ?></td>
                                            <td><?= $usr->absen_description; ?></td>
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
    var title = "<?= (isset($_GET["report"])) ? "Laporan" : ""; ?> Absensi";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>