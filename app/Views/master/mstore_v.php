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
                        <?php } ?>
                    </div>

                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Toko";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Toko";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_name">Toko:</label>
                                    <div class="col-sm-10">
                                        <input required type="text" autofocus class="form-control" id="store_name" name="store_name" placeholder="" value="<?= $store_name; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_address">Alamat:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="store_address" name="store_address" placeholder="" value="<?= $store_address; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_owner">Owner:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="store_owner" name="store_owner" placeholder="" value="<?= $store_owner; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_phone">Phone:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="store_phone" name="store_phone" placeholder="" value="<?= $store_phone; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_wa">Whatsapp:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="store_wa" name="store_wa" placeholder="" value="<?= $store_wa; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_web">Web:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="store_web" name="store_web" placeholder="" value="<?= $store_web; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_resep">Prosentase Resep:<br/><span class="text-danger">Prosentase kanaikan harga ketika memakai resep.</span></label>
                                    <div class="col-sm-10">
                                        <input type="number" autofocus class="form-control" id="store_resep" name="store_resep" placeholder="" value="<?= $store_resep; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_noteinvoice">Catatan Nota Penjualan:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="store_noteinvoice" name="store_noteinvoice" placeholder="Mis:Barang tidak dapat ditukar." value="<?= $store_noteinvoice; ?>"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_picture">Logo:</label>
                                    <div class="col-sm-10">
                                        <input type="file" autofocus class="form-control" id="store_picture" name="store_picture" placeholder="" value="<?= $store_picture; ?>">
                                        <?php if($store_picture!=""){$user_image="images/store_picture/".$store_picture;}else{$user_image="images/store_picture/no_image.png";}?>
                                          <img id="store_picture_image" width="100" height="100" src="<?=base_url($user_image);?>"/>
                                          <script>
                                            function readURL(input) {
                                                if (input.files && input.files[0]) {
                                                    var reader = new FileReader();
                                        
                                                    reader.onload = function (e) {
                                                        $('#store_picture_image').attr('src', e.target.result);
                                                    }
                                        
                                                    reader.readAsDataURL(input.files[0]);
                                                }
                                            }
                                        
                                            $("#store_picture").change(function () {
                                                readURL(this);
                                            });
                                          </script>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_upahharian">Upah Harian:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="store_upahharian" name="store_upahharian" placeholder="Upah Harian" value="<?= $store_upahharian; ?>"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_upahbulanan">Upah Bulanan:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="store_upahbulanan" name="store_upahbulanan" placeholder="Upah Harian" value="<?= $store_upahbulanan; ?>"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_lemburupah">Lembur:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="store_lemburupah" name="store_lemburupah" placeholder="Upah Harian" value="<?= $store_lemburupah; ?>"/>
                                    </div>
                                </div>

                                

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_psakit">Potongan Sakit:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="store_psakit" name="store_psakit" placeholder="Upah Harian" value="<?= $store_psakit; ?>"/>
                                    </div>
                                </div>
                                

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_pizin">Potongan Izin:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="store_pizin" name="store_pizin" placeholder="Upah Harian" value="<?= $store_pizin; ?>"/>
                                    </div>
                                </div>
                                

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_pcuti">Potongan Cuti:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="store_pcuti" name="store_pcuti" placeholder="Upah Harian" value="<?= $store_pcuti; ?>"/>
                                    </div>
                                </div>
                                

                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="store_palpha">Potongan Alpha:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="store_palpha" name="store_palpha" placeholder="Upah Harian" value="<?= $store_palpha; ?>"/>
                                    </div>
                                </div>

                                <input type="hidden" name="store_id" value="<?= $store_id; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href=<?= site_url("store"); ?>">Back</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php } else { ?>
                        <?php if ($message != "") { ?>
                            <div class="alert alert-info alert-dismissable">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong><?= $message; ?></strong><br /><?= $uploadstore_picture; ?>
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
                                        <th>Toko</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $usr = $this->db
                                        ->table("store")
                                        ->where("store.store_id",session()->get("store_id"))
                                        ->orderBy("store_name", "ASC")
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
                                                            isset(session()->get("halaman")['28']['act_update']) 
                                                            && session()->get("halaman")['28']['act_update'] == "1"
                                                        )
                                                    ) { ?>
                                                    <form method="post" class="btn-action" style="">
                                                        <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                        <input type="hidden" name="store_id" value="<?= $usr->store_id; ?>" />
                                                    </form>
                                                    <?php }?>  
                                                </td>
                                            <?php } ?>
                                            <td><?= $usr->store_name; ?></td>
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
    var title = "Master Toko";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>