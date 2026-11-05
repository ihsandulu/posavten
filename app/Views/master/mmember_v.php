<?php echo $this->include("template/header_v"); ?>

<div class='container-fluid'>
    <div class='row'>
        <div class='col-12'>
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <?php if (!isset($_GET['member_id']) && !isset($_POST['new']) && !isset($_POST['edit'])) {
                            $coltitle = "col-md-10";
                        } else {
                            $coltitle = "col-md-8";
                        } ?>
                        <div class="<?= $coltitle; ?>">
                            <h4 class="card-title"></h4>
                            <!-- <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6> -->
                        </div>
                        <?php if (!isset($_POST['new']) && !isset($_POST['edit']) && !isset($_GET['report'])) { ?>
                            <?php if (isset($_GET["member_id"])) { ?>
                                <form action="<?= site_url("member"); ?>" method="get" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button class="btn btn-warning btn-block btn-lg" value="OK" style="">Back</button>
                                    </h1>
                                </form>
                            <?php } ?>
                            <form method="post" class="col-md-2">
                                <h1 class="page-header col-md-12">
                                    <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                    <input type="hidden" name="member_id" />
                                </h1>
                            </form>
                        <?php } ?>
                    </div>

                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Member";
                                $ketpassword="Kosongkan jika tidak ingin merubah password!";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Add Member";
                                $ketpassword=$member_password;
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">

                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="positionm_id">Grade:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $positionm = $this->db->table("positionm")
                                            ->where("store_id",session()->get("store_id"))
                                            ->orderBy("positionm_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select required class="form-control select" id="positionm_id" name="positionm_id">
                                            <option value="" <?= ($positionm_id == "") ? "selected" : ""; ?>>Pilih Grade Member</option>
                                            <?php
                                            foreach ($positionm->getResult() as $positionm) { ?>
                                                <option value="<?= $positionm->positionm_id; ?>" <?= ($positionm_id == $positionm->positionm_id) ? "selected" : ""; ?>><?= $positionm->positionm_name; ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>

                               
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="member_name">Nama:</label>
                                    <div class="col-sm-10">
                                        <input required type="text" autofocus class="form-control" id="member_name" name="member_name" placeholder="" value="<?= $member_name; ?>">

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="member_email">Email:</label>
                                    <div class="col-sm-10">
                                        <input type="email" autofocus class="form-control" id="member_email" name="member_email" placeholder="" value="<?= $member_email; ?>">

                                    </div>
                                </div>


                               
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="member_password">Password:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="member_password" name="member_password" placeholder="<?=$ketpassword;?>" value="">
                                    </div>
                                </div>

                                

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="member_no">Member No.:<br/>(Isi dengan whatsapp jika ada!)</label>
                                    <div class="col-sm-12">
                                        <input type="text" autofocus class="form-control" id="member_no" name="member_no" placeholder="" value="<?= $member_no; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="member_address">Alamat:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="member_address" name="member_address" placeholder="" value="<?= $member_address; ?>">

                                    </div>
                                </div>



                                <input type="hidden" name="member_id" value="<?= $member_id; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href=<?= site_url("member"); ?>">Back</button>
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
                                        <th>Grade</th>
                                        <th>Member No.</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $usr = $this->db
                                        ->table("member")
                                        ->join("positionm", "positionm.positionm_id=member.positionm_id", "left")
                                        ->join("store", "store.store_id=member.store_id", "left")
                                        ->where("member.store_id", session()->get("store_id"))
                                        ->orderBy("member_id", "desc")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    foreach ($usr->getResult() as $usr) { ?>
                                        <tr>
                                            <?php if (!isset($_GET["report"])) { ?>
                                                <td style="padding-left:0px; padding-right:0px;">

                                                    <form method="post" class="btn-action" style="">
                                                        <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                        <input type="hidden" name="member_id" value="<?= $usr->member_id; ?>" />
                                                    </form>

                                                    <form method="post" class="btn-action" style="">
                                                        <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                        <input type="hidden" name="member_id" value="<?= $usr->member_id; ?>" />
                                                    </form>
                                                </td>
                                            <?php } ?>
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $usr->positionm_name; ?></td>
                                            <td><?= $usr->member_no; ?></td>
                                            <td><?= $usr->member_name; ?></td>
                                            <td><?= $usr->member_email; ?></td>
                                            <td><?= $usr->member_address; ?></td>
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
    var title = "Master Member";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>