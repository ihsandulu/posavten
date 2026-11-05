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
                            $coltitle = "col-md-10";
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
                                    isset(session()->get("halaman")['26']['act_create'])
                                    && session()->get("halaman")['26']['act_create'] == "1"
                                )
                            ) { ?>
                                <form method="post" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                        <input type="hidden" name="stockopname_id" />
                                    </h1>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Stockopname";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Stockopname";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="stockopname_datetime">Tanggal:</label>
                                    <div class="col-sm-10">
                                        <input required type="datetime-local" class="form-control" id="stockopname_datetime" name="stockopname_datetime" placeholder="" value="<?= $stockopname_datetime; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="product_id">Produk:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $product = $this->db->table("product")
                                            ->where("store_id", session()->get("store_id"))
                                            ->orderBy("product_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select required onchange="isi(); selisih();" class="form-control select" id="product_id" name="product_id">
                                            <option value="" <?= ($product_id == "0" || $product_id == "") ? "selected" : ""; ?>>Pilih Produk</option>
                                            <?php
                                            foreach ($product->getResult() as $product) { ?>
                                                <option value="<?= $product->product_id; ?>" <?= ($product_id == $product->product_id) ? "selected" : ""; ?> product_name="<?= $product->product_name; ?>" product_buy="<?= $product->product_buy; ?>" product_ube="<?= $product->product_ube; ?>" product_batch="<?= $product->product_batch; ?>" product_expiredate="<?= $product->product_expiredate; ?>" stockopname_awal="<?= $product->product_stock; ?>"><?= $product->product_name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" class="form-control" id="product_name" name="product_name" placeholder="" value="<?= $product_name; ?>">
                                        <input type="hidden" class="form-control" id="product_buy" name="product_buy" placeholder="" value="<?= $product_buy; ?>">
                                        <script>
                                            function isi() {
                                                let productid = $("#product_id option:selected");
                                                let product_name = productid.attr("product_name");
                                                let product_buy = productid.attr("product_buy");
                                                let product_ube = productid.attr("product_ube");
                                                let product_batch = productid.attr("product_batch");
                                                let stockopname_awal = productid.attr("stockopname_awal");
                                                $("#product_name").val(product_name);
                                                $("#product_buy").val(product_buy);
                                                $("#product_ube").val(product_ube);
                                                $("#product_batch").val(product_batch);
                                                $("#product_expiredate").val(product_expiredate);
                                                <?php if (isset($_POST['edit'])) { ?>
                                                    $("#stockopname_awal").val(<?= $stockopname_awal; ?>);
                                                    $("#stockopname_awal1").val(<?= $stockopname_awal; ?>);
                                                <?php } else { ?>
                                                    $("#stockopname_awal").val(stockopname_awal);
                                                    $("#stockopname_awal1").val(stockopname_awal);
                                                <?php } ?>

                                            }
                                            setTimeout(() => {
                                                isi();
                                            }, 300);
                                        </script>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="product_ube">UBE:</label>
                                    <div class="col-sm-10">
                                        <input type="text" required readonly class="form-control" id="product_ube" name="product_ube" placeholder="" value="<?= $product_ube; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="product_batch">Batch:</label>
                                    <div class="col-sm-10">
                                        <input autofocus type="text" required class="form-control"
                                            id="product_batch"
                                            name="product_batch"
                                            placeholder=""
                                            value="<?= $product_batch; ?>">
                                    </div>
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $("#product_batch").keypress(function(e) {
                                            if (e.which == 13) { // 13 = kode Enter
                                                e.preventDefault(); // cegah submit otomatis
                                                isibatch(); // panggil function kamu
                                            }
                                        });
                                    });

                                    function isibatch() {
                                        let product_batch = $("#product_batch").val();
                                        // alert("<?= base_url("api/isibatch?product_batch="); ?>"+product_batch);
                                        $.get("<?= base_url("api/isibatch"); ?>", {
                                                product_batch: product_batch
                                            })
                                            .done(function(res) {
                                                let product_id = res.product_id;
                                                let product_name = res.product_name;
                                                let product_buy = res.product_buy;
                                                let product_ube = res.product_ube;
                                                let stockopname_awal = res.product_stock;
                                                let product_expiredate = res.product_expiredate;
                                                 $("#product_id").val(product_id).trigger("change");
                                                $("#product_name").val(product_name);
                                                $("#product_buy").val(product_buy);
                                                $("#product_ube").val(product_ube);
                                                $("#product_batch").val(product_batch);
                                                $("#product_expiredate").val(product_expiredate);
                                                <?php if (isset($_POST['edit'])) { ?>
                                                    $("#stockopname_awal").val(<?= $stockopname_awal; ?>);
                                                    $("#stockopname_awal1").val(<?= $stockopname_awal; ?>);
                                                <?php } else { ?>
                                                    $("#stockopname_awal").val(stockopname_awal);
                                                    $("#stockopname_awal1").val(stockopname_awal);
                                                <?php } ?>

                                            });

                                    }
                                </script>

                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="product_expiredate">Expired Date:</label>
                                    <div class="col-sm-10">
                                        <input type="date" required readonly class="form-control" id="product_expiredate" name="product_expiredate" placeholder="" value="<?= $product_expiredate; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="stockopname_awal">Stock Awal:</label>
                                    <div class="col-sm-10">
                                        <input required readonly onkeyup="rupiahnumerik(this)" change="selisih();" type="text" class="form-control" id="stockopname_awal" name="stockopname_awal" placeholder="" value="<?= $stockopname_awal; ?>">
                                    </div>
                                    <script>
                                        rupiahnumerik($("#stockopname_awal"))
                                    </script>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="stockopname_hitung">Stock Hitung:</label>
                                    <div class="col-sm-10">
                                        <input required onkeyup="rupiahnumerik(this)" change="selisih()" type="text" class="form-control" id="stockopname_hitung" name="stockopname_hitung" placeholder="" value="<?= $stockopname_hitung; ?>">
                                    </div>
                                    <script>
                                        rupiahnumerik($("#stockopname_hitung"))
                                    </script>
                                </div>
                                <script>
                                    function selisih() {
                                        let awal = $("#stockopname_awal1").val();
                                        if (isNaN(awal)) {
                                            awal = 0
                                        }
                                        let hitung = $("#stockopname_hitung1").val();
                                        if (isNaN(hitung)) {
                                            hitung = 0
                                        }
                                        let selisih = hitung - awal;
                                        let nselisih = selisih * $("#product_buy").val();
                                        $("#stockopname_selisih").val(selisih);
                                        $("#stockopname_nselisih").val(nselisih);
                                        // rupiahnumerik($("#stockopname_selisih"));
                                        // rupiahnumerik($("#stockopname_nselisih"));
                                    }
                                </script>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="stockopname_selisih">Selisih:</label>
                                    <div class="col-sm-10">
                                        <input type="text" required readonly class="form-control" id="stockopname_selisih" name="stockopname_selisih" placeholder="" value="<?= $stockopname_selisih; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="stockopname_nselisih">Nilai Selisih:</label>
                                    <div class="col-sm-10">
                                        <input type="text" required readonly class="form-control" id="stockopname_nselisih" name="stockopname_nselisih" placeholder="" value="<?= $stockopname_nselisih; ?>">
                                    </div>
                                </div>

                                <input type="hidden" name="stockopname_id" value="<?= $stockopname_id; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href=<?= site_url("product"); ?>">Back</button>
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
                                        <th>Tanggal</th>
                                        <th>Ube</th>
                                        <th>Produk</th>
                                        <th>Expired</th>
                                        <th>Stok Awal</th>
                                        <th>Stok Hitung</th>
                                        <th>Selisih</th>
                                        <th>Nilai Selisih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $usr = $this->db
                                        ->table("stockopname")
                                        ->join("store", "store.store_id=stockopname.store_id", "left")
                                        ->where("stockopname.store_id", session()->get("store_id"))
                                        ->orderBy("product_name", "ASC")
                                        ->get();
                                    //echo $this->db->getLastquery();
                                    $no = 1;
                                    foreach ($usr->getResult() as $usr) {
                                        if ($usr->stockopname_hitung == "" || $usr->stockopname_hitung == null) {
                                            $usr->stockopname_hitung = 0;
                                        }
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
                                                            isset(session()->get("halaman")['26']['act_update'])
                                                            && session()->get("halaman")['26']['act_update'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                            <input type="hidden" name="stockopname_id" value="<?= $usr->stockopname_id; ?>" />
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
                                                            isset(session()->get("halaman")['26']['act_delete'])
                                                            && session()->get("halaman")['26']['act_delete'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-danger delete" onclick="return confirm('Peringatan! Mendelete data ini tidak akan mengembalikan data stok sebelum nya pada table master produk. Apakah anda yakin akan mendelete data ini?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                            <input type="hidden" name="stockopname_id" value="<?= $usr->stockopname_id; ?>" />
                                                            <input type="hidden" name="stockopname_awal" value="<?= $usr->stockopname_awal; ?>" />
                                                            <input type="hidden" name="stockopname_datetime" value="<?= $usr->stockopname_datetime; ?>" />
                                                            <input type="hidden" name="product_id" value="<?= $usr->product_id; ?>" />
                                                        </form>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->stockopname_datetime; ?></td>
                                            <td><?= $usr->product_ube; ?></td>
                                            <td><?= $usr->product_name; ?></td>
                                            <td><?= $usr->product_expiredate; ?></td>
                                            <td><?= number_format($usr->stockopname_awal, 0, ".", ","); ?></td>
                                            <td><?= number_format($usr->stockopname_hitung, 0, ".", ","); ?></td>
                                            <td><?= number_format($usr->stockopname_selisih, 0, ".", ","); ?></td>
                                            <td><?= number_format($usr->stockopname_nselisih, 0, ".", ","); ?></td>
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
    var title = "Stockopname";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>

<?php echo  $this->include("template/footer_v"); ?>