<?php echo $this->include("template/header_v"); 
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

                        <form action="<?= base_url("purchase"); ?>" method="get" class="col-md-2">
                            <h1 class="page-header col-md-12">
                                <button class="btn btn-warning btn-block btn-lg" value="OK" style="">Back</button>
                                <?php if (isset($_GET["report"])) { ?>
                                    <input name="report" value="OK" type="hidden" />
                                <?php } ?>
                                    <input name="from" value="<?=$from;?>" type="hidden" />
                                    <input name="to" value="<?=$to;?>" type="hidden" />
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
                                    isset(session()->get("halaman")['18']['act_create'])
                                    && session()->get("halaman")['18']['act_create'] == "1"
                                )
                            ) { ?>
                                <form method="post" class="col-md-2">
                                    <h1 class="page-header col-md-12">
                                        <button name="new" class="btn btn-info btn-block btn-lg" value="OK" style="">New</button>
                                        <input type="hidden" name="purchased_id" />
                                    </h1>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if (isset($_POST['new']) || isset($_POST['edit'])) { ?>
                        <div class="">
                            <?php if (isset($_POST['edit'])) {
                                $namabutton = 'name="change"';
                                $judul = "Update Detil Pembelian";
                            } else {
                                $namabutton = 'name="create"';
                                $judul = "Tambah Detil Pembelian";
                            } ?>
                            <div class="lead">
                                <h3><?= $judul; ?></h3>
                            </div>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="product_id">Produk:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $product = $this->db->table("product")
                                            ->join("unit", "unit.unit_id=product.unit_id", "left")
                                            ->where("product.store_id", session()->get("store_id"))
                                            ->where("product.product_supplier", "1")
                                            ->orderBy("product.product_name", "ASC")
                                            ->get();
                                        //echo $this->db->getLastQuery();
                                        ?>
                                        <select required onchange="unitname()" class="form-control select" id="product_id" name="product_id">
                                            <option unit_name="" product_buy="" value="" <?= ($product_id == "") ? "selected" : ""; ?>>Pilih Produk</option>
                                            <?php
                                            foreach ($product->getResult() as $product) { ?>
                                                <option unit_name="<?= $product->unit_name; ?>" product_buy="<?= $product->product_buy; ?>" value="<?= $product->product_id; ?>" <?= ($product_id == $product->product_id) ? "selected" : ""; ?>><?= $product->product_name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <script>
                                            function unitname() {
                                                let unit_name = $('#product_id option:selected').attr("unit_name");
                                                let product_buy = $('#product_id option:selected').attr("product_buy");
                                                $("#unit_name").html(unit_name);
                                                $("#purchased_sprice").val(product_buy);
                                            }
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="purchased_batch">Batch:</label>
                                    <div class="col-sm-10">
                                        <input type="text" autofocus class="form-control" id="purchased_batch" name="purchased_batch" placeholder="" value="<?= $purchased_batch; ?>"
                                            onkeydown="handleEnter(event)">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="purchased_outdate">Out of Date:</label>
                                    <div class="col-sm-10">
                                        <input type="date" autofocus class="form-control" id="purchased_outdate" name="purchased_outdate" placeholder="" value="<?= $purchased_outdate; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="purchased_qty">Qty (<span id="unit_name"></span>):</label>
                                    <div class="col-sm-10">
                                        <input required onkeyup="tagihan()" type="text" autofocus class="form-control" id="purchased_qty" name="purchased_qty" placeholder="" value="<?= $purchased_qty; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="purchased_sprice">Harga Satuan:</label>
                                    <div class="col-sm-12">
                                        <input required onkeyup="tagihan()" type="number" autofocus class="form-control" id="purchased_sprice" name="purchased_sprice" placeholder="" value="<?= $purchased_sprice; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="purchased_discount">Diskon Satuan (%):</label>
                                    <div class="col-sm-12">
                                        <input required onkeyup="tagihan()" type="number" autofocus class="form-control" id="purchased_discount" name="purchased_discount" placeholder="" value="<?= $purchased_discount; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-12" for="purchased_price">Total:</label>
                                    <div class="col-sm-12">
                                        <input readonly onkeyup="tagihan()" type="number" autofocus class="form-control" id="purchased_price" name="purchased_price" placeholder="" value="<?= $purchased_price; ?>">
                                    </div>
                                </div>
                                <!-- <?php if ($this->request->getGET("purchase_ppn") == 0) {
                                            $hide = "";
                                        } else {
                                            $hide = "hide";
                                            $purchased_ppn = intval($this->request->getGET("purchase_ppn"));
                                            if ($purchased_price == "") {
                                                $purchased_price = 0;
                                            }
                                            if ($purchased_ppn > 0) {
                                                $ppn = $purchased_ppn / 100 * $purchased_price;
                                            } else {
                                                $ppn = 0;
                                            }
                                            $purchased_bill = $purchased_price + $ppn;
                                        } ?> -->
                                <div class="form-group <?= $hide; ?>">
                                    <label class="control-label col-sm-12" for="purchased_ppn">PPN(%):</label>
                                    <div class="col-sm-12">
                                        <input onkeyup="tagihan()" type="number" autofocus class="form-control" id="purchased_ppn" name="purchased_ppn" placeholder="" value="<?= $purchased_ppn; ?>">
                                    </div>
                                </div>
                                <div class="form-group <?= $hide; ?>">
                                    <label class="control-label col-sm-12" for="purchased_bill">Tagihan setelah PPN:</label>
                                    <div class="col-sm-12">
                                        <input readonly type="number" autofocus class="form-control" id="purchased_bill" name="purchased_bill" placeholder="" value="<?= $purchased_bill; ?>">
                                    </div>
                                </div>
                                <script>
                                    function tagihan() {
                                        let qty = $("#purchased_qty").val();
                                        let sprice = $("#purchased_sprice").val();
                                        let total = qty * sprice;
                                        let discount = $("#purchased_discount").val();
                                        let tdiscountnom = 0;
                                        if (discount > 0) {
                                            tdiscountnom = discount / 100 * total;
                                        }
                                        let price = parseInt(total) - parseInt(tdiscountnom);
                                        $("#purchased_price").val(price);
                                        let ppn = $("#purchased_ppn").val();
                                        if (ppn > 0) {
                                            ppnnom = ppn / 100 * price;
                                        } else {
                                            ppnnom = 0;
                                        }
                                        let bill = parseInt(price) + parseInt(ppnnom);
                                        $('#purchased_bill').val(bill);
                                    }
                                </script>


                                <input type="hidden" name="purchased_qtyb" value="<?= $purchased_qty; ?>" />
                                <input type="hidden" name="purchased_id" value="<?= $purchased_id; ?>" />
                                <input type="hidden" name="purchased_bill_before" value="<?= $purchased_bill; ?>" />
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" id="submit" class="btn btn-primary col-md-5" <?= $namabutton; ?> value="OK">Submit</button>
                                        <button class="btn btn-warning col-md-offset-1 col-md-5" onClick="location.href=<?= site_url("purchased?from=".$from."&to=".$to); ?>">Back</button>
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
                                            <th>Aksi</th>
                                        <?php } ?>
                                        <th>No.</th>
                                        <th>Toko</th>
                                        <th>Kadaluarsa</th>
                                        <th>Batch</th>
                                        <th>Produk</th>
                                        <th>Qty</th>
                                        <th>Nominal</th>
                                        <th>Diskon(%)</th>
                                        <th>Total</th>
                                        <?php if ($this->request->getGET("purchase_ppn") == 0) { ?>
                                            <th>PPN</th>
                                            <th>Tagihan(setelah PPN)</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $builder = $this->db
                                        ->table("purchased")
                                        ->join("purchase", "purchase.purchase_id=purchased.purchase_id", "left")
                                        ->join("store", "store.store_id=purchased.store_id", "left")
                                        ->join("product", "product.product_id=purchased.product_id", "left")
                                        ->where("purchased.store_id", session()->get("store_id"))
                                        ->where("purchased.purchase_id", $this->request->getGet("purchase_id"));
                                    if (isset($_GET["from"]) && $_GET["from"] != "") {
                                        $builder->where("purchase.purchase_date >=", $this->request->getGet("from"));
                                    }
                                    if (isset($_GET["to"]) && $_GET["to"] != "") {
                                        $builder->where("purchase.purchase_date <=", $this->request->getGet("to"));
                                    }
                                    $usr = $builder
                                        ->orderBy("purchased_id", "DESC")
                                        ->get();
                                    // echo $this->db->getLastquery();
                                    $no = 1;
                                    foreach ($usr->getResult() as $usr) {
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
                                                            isset(session()->get("halaman")['18']['act_update'])
                                                            && session()->get("halaman")['18']['act_update'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-warning " name="edit" value="OK"><span class="fa fa-edit" style="color:white;"></span> </button>
                                                            <input type="hidden" name="purchased_id" value="<?= $usr->purchased_id; ?>" />
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
                                                            isset(session()->get("halaman")['18']['act_delete'])
                                                            && session()->get("halaman")['18']['act_delete'] == "1"
                                                        )
                                                    ) { ?>
                                                        <form method="post" class="btn-action" style="">
                                                            <button class="btn btn-sm btn-danger delete" onclick="return confirm(' you want to delete?');" name="delete" value="OK"><span class="fa fa-close" style="color:white;"></span> </button>
                                                            <input type="hidden" name="purchased_id" value="<?= $usr->purchased_id; ?>" />
                                                            <input type="hidden" name="purchased_bill" value="<?= $usr->purchased_bill; ?>" />
                                                            <input type="hidden" name="purchased_qty" value="<?= $usr->purchased_qty; ?>" />
                                                            <input type="hidden" name="product_id" value="<?= $usr->product_id; ?>" />
                                                        </form>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td><?= $no++; ?></td>
                                            <td><?= $usr->store_name; ?></td>
                                            <td><?= $usr->purchased_outdate; ?></td>
                                            <td><?= $usr->purchased_batch; ?></td>
                                            <td><?= $usr->product_name; ?></td>
                                            <td><?= number_format($usr->purchased_qty, 0, ".", ","); ?></td>
                                            <td><?= number_format($usr->purchased_sprice, 0, ".", ","); ?></td>
                                            <td><?= number_format($usr->purchased_discount, 0, ".", ","); ?></td>
                                            <td><?= number_format($usr->purchased_price, 0, ".", ","); ?></td>
                                            <?php if ($this->request->getGET("purchase_ppn") == 0) { ?>
                                                <td><?= $usr->purchased_ppn; ?> %</td>
                                                <td><?= number_format($usr->purchased_bill, 0, ".", ","); ?></td>
                                            <?php } ?>
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
    var title = "Laporan Detil Pembelian <?= $this->request->getGET("purchase_no"); ?> (<?= $this->request->getGET("supplier_name"); ?>)";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
</script>
<script>
    function handleEnter(event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Mencegah form submit
            caribatch(); // Panggil fungsi kamu
        }
    }

    function caribatch() {
        let product_batch = $("#purchased_batch").val();
        // alert('<?= base_url("api/caribatch"); ?>?product_batch='+product_batch);
        $.get("<?= base_url("api/caribatch"); ?>", {
                product_batch: product_batch
            })
            .done(function(data) {
                $("#product_id").val(data).trigger('change');
            });
    }
</script>

<?php echo  $this->include("template/footer_v"); ?>