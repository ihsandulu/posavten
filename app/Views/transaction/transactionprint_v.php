<?php echo $this->include("template/headersaja_v"); ?>
<style>
    .img_product {
        width: 100%;
        height: 150px !important;
        border: rgba(155, 155, 155, 0.5) solid 1px;
        border-radius: 4px;
    }



    .centerpage {
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }

    .hide {
        display: none;
    }


    @media print {

        html,
        body,
        div {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0px !important;
            line-height: 100%;
            width: 7.2cm;
            font-size: 13px !important;
        }

        #storename_title {
            margin: bottom 30px, im !important;
        }

        p {
            margin-bottom: 0px;
        }



        .tebal10 {
            font-weight: bold;
        }

        .tebal12 {
            font-weight: bold;
        }

        .tebal14 {
            font-weight: bold;
        }

        .tebal16 {
            font-weight: bold;
        }

        th,
        td {
            padding: 0px 1px 0px 1px;
            font-size: 13px;
            line-height: 100% !important;
        }

        .pagebreak {
            page-break-after: always;
        }
    }

    .border {
        border: black solid 1px;
    }
</style>

<?php
$store = $this->db->table("store")->where("store_id", session()->get("store_id"))->get()->getRow();
$builder = $this->db->table("transaction")
    ->where("transaction_id", $this->request->getGet("transaction_id"));
$transaction = $builder->get();
// echo $this->db->getLastQuery();
if ($builder->countAll() > 0) {
    foreach ($transaction->getResult() as $transaction) {
?>
        <div class='container-fluid p-0'>
            <div class='row p-0'>
                <div class="col-12 text-center" id="storename_title" style="font-weight:bold; padding: 5px 0 0 0; font-size:30px; border-top: black solid 1px; margin-top:10px!important;"><?= $store->store_name; ?></div>
                <div class="col-12 text-center" style="padding:0px; font-size:13px;"><?= $store->store_address; ?></div>
                <div class="col-12 text-center" style="padding: 0 0 5px 0; font-size:13px; border-bottom: black solid 1px; margin-bottom:0px!important;">Mobile : <?= $store->store_phone; ?>, <?= session()->get("store_web"); ?></div>
                <div class="col-4 mt-3 p-0 tebal10" style="margin-top:5px!important;">Invoice No.</div>
                <div class="col-8 mt-3 p-0 text-right" style="margin-top:5px!important;"><?= $transaction->transaction_no; ?></div>
                <div class="col-4 mb-3 p-0 tebal10" style="">Date</div>
                <div class="col-8 mb-3 p-0 text-right" style=""><?= date("d M Y", strtotime($transaction->transaction_date)); ?></div>
                <div class="col-12" style="padding:0px;">
                    <table id="" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <!-- <table id="dataTable" class="table table-condensed table-hover w-auto dtable"> -->
                        <thead class="">
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $usr = $this->db
                                ->table("transactiond")
                                ->select("*,SUM(transactiond_qty)AS qty, SUM(transactiond_price)AS price,")
                                ->join("product", "product.product_id=transactiond.product_id", "left")
                                ->join("unit", "unit.unit_id=product.unit_id", "left")
                                ->where("product.store_id", session()->get("store_id"))
                                ->where("transactiond.transaction_id", $this->request->getGet("transaction_id"))
                                ->groupBy("transactiond.product_id")
                                ->orderBy("product_name", "ASC")
                                ->get();
                            //echo $this->db->getLastquery();
                            $no = 1;
                            $tprice = 0;
                            $discount = 0;
                            foreach ($usr->getResult() as $usr) {
                            ?>
                                <tr>
                                    <td class="text-left">
                                        <?= $no++; ?>. <?= $usr->product_name; ?><br />
                                        <?= $usr->product_batch; ?>
                                    </td>
                                    <?php
                                    $qty = $usr->qty;
                                    $price = $usr->price;
                                    $tprice += $price;
                                    ?>
                                    <td>
                                        <?= number_format($qty, 0, ".", ",") ?> <?= $usr->unit_name; ?>
                                    </td>
                                    <td><?= number_format($price, 0, ".", ",") ?></td>
                                </tr>
                            <?php }
                            $tdiskon = $transaction->transaction_discount;
                            $resep = $transaction->transaction_resep;
                            $dtprice = $tprice - $discount;
                            ?>
                            <tr>
                                <th colspan="2" class="text-right">Total</th>
                                <th>
                                    <?= number_format($tprice, 0, ".", ","); ?>
                                    <input type="hidden" id="dtagihan" value="<?= $tprice; ?>" />
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-right">Discount</th>
                                <th>
                                    <?= number_format($tdiskon, 0, ".", ","); ?>
                                    <input type="hidden" id="diskon" value="<?= $tdiskon; ?>" />
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-right">Jasa Resep</th>
                                <th>
                                    <?= number_format($resep, 0, ".", ","); ?>
                                    <input type="hidden" id="resep" value="<?= $resep; ?>" />
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-right">Total Tagihan</th>
                                <th>
                                    <?= number_format($transaction->transaction_bill, 0, ".", ","); ?>
                                    <input type="hidden" id="tagihan" value="<?= $transaction->transaction_bill; ?>" />
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-right">Bayar</th>
                                <th class="dibayar"><?= number_format($transaction->transaction_pay, 0, ".", ","); ?></th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-right">Kembalian</th>
                                <th class="kembalian"><?= number_format($transaction->transaction_change, 0, ".", ","); ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12 mt-3 pt-0 text-center" style="line-height:100%!important;">
                    <?= $store->store_noteinvoice; ?>
                </div>
                <!--  <div class="col-4 row mt-5 p-0" style=""  align="center">
                <div class="col-12"><strong class="tebal10">Hormat Kami,</strong></div>
                <div class="col-12" style="height:50px;">&nbsp;</div>
                <div class="col-12" style=""><strong><?= session()->get("user_name"); ?></strong></div>
            </div> -->
            </div>
        </div>

    <?php }
} else { ?>
    <h1 class="centerpage">Data tidak ditemukan!</h1>
<?php } ?>
<script>
    /*  setTimeout(function() {
        this.close();
    }, 500); */
</script>
<script>
    function isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    window.onload = function() {
        if (isMobile()) {
            // Buat teks struk manual
            const teksStruk = `<?= strtoupper($store->store_name) . "\n"; ?>
<?= $store->store_address . "\n"; ?>
Mobile: <?= $store->store_phone . ", " . session()->get("store_web") . "\n"; ?>
--------------------------------\n
Invoice No: <?= $transaction->transaction_no . "\n"; ?>
Tanggal   : <?= date("d M Y", strtotime($transaction->transaction_date)) . "\n"; ?>
--------------------------------\n
<?php
$usr = $this->db
    ->table("transactiond")
    ->select("*, SUM(transactiond_qty) AS qty, SUM(transactiond_price) AS price")
    ->join("product", "product.product_id=transactiond.product_id", "left")
    ->join("unit", "unit.unit_id=product.unit_id", "left")
    ->where("product.store_id", session()->get("store_id"))
    ->where("transactiond.transaction_id", $this->request->getGet("transaction_id"))
    ->groupBy("transactiond.product_id")
    ->orderBy("product_name", "ASC")
    ->get()
    ->getResult();

$no = 1;
$tprice = 0;
foreach ($usr as $item) {
    $qty = $item->qty;
    $price = $item->price;
    $tprice += $price;
    echo sprintf(
        "%d. %s\n    %s x %s %s = %s\n",
        $no++,
        $item->product_name,
        number_format($qty, 0),
        number_format($price / $qty, 0),
        $item->unit_name,
        number_format($price, 0)
    );
}
$tdiskon = $transaction->transaction_discount;
$resep = $transaction->transaction_resep;
?>
--------------------------------\n
Total      : <?= number_format($tprice, 0) . "\n"; ?>
Discount   : <?= number_format($tdiskon, 0) . "\n"; ?>
Jasa Resep : <?= number_format($resep, 0) . "\n"; ?>
Tagihan    : <?= number_format($transaction->transaction_bill, 0) . "\n"; ?>
Bayar      : <?= number_format($transaction->transaction_pay, 0) . "\n"; ?>
Kembalian  : <?= number_format($transaction->transaction_change, 0) . "\n"; ?>
--------------------------------\n
<?= $store->store_noteinvoice . "\n"; ?>`;

            // Kirim ke RawBT
            const encoded = encodeURIComponent(teksStruk);
            window.location.href = "rawbt:" + encoded;

        } else {
            // Jalankan window.print untuk desktop
            window.print();
            setTimeout(() => window.close(), 500);
        }
    };
</script>



<?php echo  $this->include("template/footersaja_v"); ?>