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
                        <?php if (!isset($_POST['new']) && !isset($_POST['edit']) && !isset($_GET['report'])) { ?>
                            <?php if (isset($_GET["user_id"])) { ?>
                                <form action="<?= site_url("user"); ?>" method="get" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button class="btn btn-warning btn-block btn-lg" value="OK" style="">Back</button>
                                    </h1>
                                </form>
                            <?php } ?>
                            <form method="post" class="col-md-2">
                                <h1 class="page-header col-md-12">
                                    <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                    <input type="hidden" name="user_id" />
                                </h1>
                            </form>
                        <?php } ?>
                    </div>

                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update User";
                                $ketpassword = "Kosongkan jika tidak ingin merubah password!";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah User";
                                $ketpassword = "Jangan dikosongkan!";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="position_id">Jabatan:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $position = $this->db->table("position")
                                            ->where("position_administrator!=", "1")
                                            ->where("store_id", session()->get("store_id"))
                                            ->orderBy("position_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select required class="form-control select" id="position_id" name="position_id">
                                            <option value="" <?= ($position_id == "") ? "selected" : ""; ?>>Pilih Jabatan</option>
                                            <?php
                                            foreach ($position->getResult() as $position) { ?>
                                                <option value="<?= $position->position_id; ?>" <?= ($position_id == $position->position_id) ? "selected" : ""; ?>><?= $position->position_name; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="user_name">Username:</label>
                                    <div class="col-sm-10">
                                        <input required type="text" autofocus class="form-control" id="user_name" name="user_name" placeholder="" value="<?= $user_name; ?>">

                                    </div>
                                </div>



                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="user_password">Password:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="user_password" name="user_password" placeholder="<?= $ketpassword; ?>" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="user_email">Email:</label>
                                    <div class="col-sm-10">
                                        <input required type="email" autofocus class="form-control" id="user_email" name="user_email" placeholder="" value="<?= $user_email; ?>">

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="user_wa">Whatsapp:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="user_wa" name="user_wa" placeholder="" value="<?= $user_wa; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="user_npwp">NPWP:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="user_npwp" name="user_npwp" placeholder="" value="<?= $user_npwp; ?>">

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="user_address">Alamat:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="user_address" name="user_address" placeholder="" value="<?= $user_address; ?>">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="user_upah">Jenis Upah:</label>
                                    <div class="col-sm-10">
                                        <select required class="form-control select" id="user_upah" name="user_upah">
                                            <option value="0" <?= ($user_upah == "0") ? "selected" : ""; ?>>Bulanan</option>
                                            <option value="1" <?= ($user_upah == "1") ? "selected" : ""; ?>>Harian</option>
                                        </select>

                                    </div>
                                </div>


                                <input type="hidden" name="user_id" value="<?= $user_id; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href=<?= site_url("user"); ?>">Back</button>
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
                                        <th>Toko</th>
                                        <th>Posisi</th>
                                        <th>Name</th>
                                        <th>Alamat</th>
                                        <th>Email</th>
                                        <th>Whatsapp</th>
                                        <th>NPWP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $usr = $this->db
                                        ->table("user")
                                        ->join("position", "position.position_id=user.position_id", "left")
                                        ->join("store", "store.store_id=user.store_id", "left")
                                        ->where("position.position_administrator !=", "1")
                                        ->where("user.store_id", session()->get("store_id"))
                                        ->orderBy("user_id", "desc")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    foreach ($usr->getResult() as $usr) { ?>
                                        <tr>
                                            <?php if (!isset($_GET["report"])) { ?>
                                                <td style="padding-left:0px; padding-right:0px;">

                                                    <form method="post" class="btn-action" style="">
                                                        <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                        <input type="hidden" name="user_id" value="<?= $usr->user_id; ?>" />
                                                    </form>

                                                    <form method="post" class="btn-action" style="">
                                                        <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                        <input type="hidden" name="user_id" value="<?= $usr->user_id; ?>" />
                                                    </form>
                                                </td>
                                            <?php } ?>
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $usr->position_name; ?></td>
                                            <td><?= $usr->user_name; ?></td>
                                            <td><?= $usr->user_address; ?></td>
                                            <td><?= $usr->user_email; ?></td>
                                            <td><?= $usr->user_wa; ?></td>
                                            <td><?= $usr->user_npwp; ?></td>
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
    var title = "Master User";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>