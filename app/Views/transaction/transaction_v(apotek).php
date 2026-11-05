<?php echo $this->include("template/header_v"); ?>
<style>
.caption-1 figcaption {
  position: absolute;
  bottom: 0;
  right: 0;
}
.caption-2 figcaption {
  width: 80%;
  position: absolute;
  bottom: 1rem;
  left: 10%;
  background: rgba(255, 255, 255, 0.6);
}
.caption-3 figcaption {
  position: absolute;
  bottom: 0;
  right: 0;
  transform: translateY(-50%);
}
.separator {
  border-bottom: 1px dashed #aaa;
}
.text-small{font-size: 8px;}
.text-small0{font-size: 12px;}
.text-small1 {
  font-size: 14px;
}
.text-small2 {
  font-size: 15px;
}
.img_product{
    width:100%; 
    height:150px !important;    
  border:rgba(155, 155, 155, 0.5) solid 1px;
  border-radius:4px;
}
.divimg_product{margin-bottom:10px; }
.pointer{cursor: pointer;}
.figcaption{background-color: rgba(0, 0, 0, 0.8); border-radius:2px; padding:5px; }
#listproduct{overflow-y: scroll;}
#listproduct::-webkit-scrollbar {
  display: none;
}
#listproduct {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
.centerpage{
    position: fixed;
    left:50%;
    top:50%;
    transform:translate(-50%,-50%);
}
.hide{display: none;}
#bayara{
    background-color:white; 
    padding:50px!important; 
    border:rgba(200,100,200,0.1) solid 1px; 
    border-radius:5px; 
    box-shadow:rgba(200,100,200,0.1) 0px 0px 5px 5px;
    z-index: 100;
}
.absolute-top-right{
    position: absolute;
    right:5px;
    top:5px;
}
.disabled{opacity: 0.1;}
</style>

<div class='container-fluid'>
    <div class='row'>
        <div class='col-7'>
            <div class="card">
                <div class="card-body">
                    <div class="row"> 
                        <div class="col-6 row">                       
                            <div class="col-6">
                                <button onclick="plistproduct('gambar','');" class="btn btn-info btn-block">Bergambar</button>
                            </div>                               
                            <div class="col-6">
                                <button onclick="plistproduct('list','');" class="btn btn-info btn-block">List Data</button>
                            </div>                        
                        </div>                       
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <input type="hidden" id="typesearch" value="gambar"/>
                                <input  onkeyup="cariproduk();" id="cariproduk" type="text" class="form-control" placeholder="Cari Produk" aria-label="Cari Produk" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary fa fa-search" type="button"></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 row" id="listproduct">
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class='col-5'>
            <div class="card">
                <div class="card-body row">
                    <input id="fokus" type="hidden" value="barcode"/>
                    <div id="test"></div>
                    <div class="col-12">                       
                        <div class="input-group mb-3">
                            <input id="inputbarcode" autofocus type="text" class="form-control" placeholder="Scan Barcode" aria-label="Scan Barcode" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary fa fa-edit" type="button"></button>
                            </div>
                        </div>                       
                    </div>
                    <div class="col-12 row">                          
                        <input type="hidden" id="modalstatus" value="keluar"/>  
                        <input type="hidden" id="listnotastatus" value="2"/>                          
                        <input type="hidden" id="kasterakhirval"/>                        
                        <input type="hidden" id="kasshift" value="0"/>
                        <div class="col-12 p-0">                             
                            <button id="btnmodalawal"  data-toggle="tooltip" data-placement="top" title="Modal Awal dari Owner" onclick="modalkas('masuk');" class="btn  btn-primary fa fa-money mb-2" type="button"></button>   
                            <button  data-toggle="tooltip" data-placement="top" title="Stor Uang ke Owner" onclick="modalkas('keluar');" class="btn fa fa-mail-forward btn-primary mb-2 btn-child" type="button">
                                <!-- <span class="fa-stack fa-xs">
                                    <i class="fa fa-money fa-stack-1x"></i>
                                    <i class="fa fa-ban fa-stack-2x text-danger"></i>
                                </span> -->
                            </button>    
                            <button  data-toggle="tooltip" data-placement="top" title="Transaksi Pending" onclick="listnota(2);nota(0);" class="btn  btn-warning fa fa-flag-checkered mb-2 btn-child" type="button"></button>
                            <button  data-toggle="tooltip" data-placement="top" title="Transaksi Sukses" onclick="listnota(0);nota(0);" class="btn  btn-success fa fa-check mb-2 btn-child" type="button"></button>
                            <button  data-toggle="tooltip" data-placement="top" title="Refresh Halaman" onclick="listnota(2);nota(0);" class="btn  btn-info fa fa-refresh mb-2 btn-child" type="button"></button>
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
                                    isset(session()->get("halaman")['13']['act_create']) 
                                    && session()->get("halaman")['13']['act_create'] == "1"
                                )
                            ) { ?>
                            <button data-toggle="tooltip" data-placement="top" title="Buat Nota Baru" onclick="createnota();" class="btn  btn-primary fa fa-plus mb-2 btn-child" type="button"></button>
                            <?php }?>
                        </div>
                        <div id="keterangan" class="alert alert-info col-12  p-1 text-center" role="alert"></div>          
                        
                        <?php 
                        if(isset($_GET["from"])&&$_GET["from"]!=""){
                            $from=$_GET["from"];
                        }else{
                            $from=date("Y-m-d");
                        }

                        if(isset($_GET["to"])&&$_GET["to"]!=""){
                            $to=$_GET["to"];
                        }else{
                            $to=date("Y-m-d");
                        }

                        ?>
                        <form class="form-inline" >
                            <label for="from">Dari:</label>&nbsp;
                            <input onchange="listnota(-1)" type="date" id="from" name="from" class="form-control" value="<?=$from;?>">&nbsp;
                            <label for="to">Ke:</label>&nbsp;
                            <input onchange="listnota(-1)" type="date" id="to" name="to" class="form-control" value="<?=$to;?>">&nbsp;
                            <button onclick="hariini()" type="button" class="btn btn-primary">Hari Ini</button>
                        </form>
                        
                        <div class="my-1" id="listnota"></div>   
                        <div class="separator my-3"></div>                    
                    </div>
                    <div class="col-12" id="nota">
                        
                    </div>
                    <div  onclick="fokus('bayar')" onfocusout="fokus('barcode');" class="modal " id="bayar">                       
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Pembayaran</h4>
                                </div>
                                <div class="modal-body">
                                    <div>Tagihan : Rp. <span class="bill"></span></div>
                                    <div class="form-inline">
                                        <label for="uang">Jumlah Uang:</label> &nbsp
                                        <input onclick="fokus('bayar')" onkeyup="kembalian();" type="number" class="form-control" id="uang"> &nbsp
                                        <button onclick="pelunasan();" type="button" class="btn btn-primary">Submit</button>
                                    </div>
                                    <div>Pembayaran : Rp. <span class="dibayar"></span></div>
                                    <div>Kembalian : Rp. <span class="kembalian"></span></div>
                                </div>
                                <div class="modal-footer">
                                    <button onclick="fokus('barcode');" type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div onclick="fokus('modalawal');" onfocusout="fokus('barcode');" class="modal " id="showmodalawal">                       
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Kas Masuk</h4>
                                </div>
                                <div class="modal-body">
                                    <div>Shift : <span class="kasshift"></span></div>
                                    <div>Modal Awal : Rp. <span class="kasawal"></span></div>
                                    <div>Kas Terakhir : Rp. <span class="kasterakhir"></span></div>
                                    <div class="form-inline">
                                        <label for="modalawal">Jumlah Uang:</label> &nbsp
                                        <input type="number" class="form-control" id="modalawal"> &nbsp
                                        <button onclick="kasmodal('masuk')" type="button" class="btn btn-primary">Submit</button>
                                    </div>
                                    <div>Keterangan : <span class="keteranganmodalawal">Modal awal dari owner.</span></div>
                                </div>
                                <div class="modal-footer">
                                    <button onclick="fokus('barcode');" type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div onclick="fokus('modalakhir');" onfocusout="fokus('barcode');" class="modal " id="showmodalakhir">                       
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Kas Keluar</h4>
                                </div>
                                <div class="modal-body">
                                    <div>Shift : <span class="kasshift"></span></div>
                                    <div>Modal Awal : Rp. <span class="kasawal"></span></div>
                                    <div>Kas Terakhir : Rp. <span class="kasterakhir"></span></div>
                                    <div class="form-inline">
                                        <label for="modalakhir">Jumlah Uang:</label> &nbsp
                                        <input type="number" class="form-control" id="modalakhir"> &nbsp
                                        <button onclick="kasmodal('keluar')" type="button" class="btn btn-primary">Submit</button>
                                    </div>
                                    <div>Keterangan : <span class="keteranganmodalakhir">Stor uang kepada owner.</span></div>
                                </div>
                                <div class="modal-footer">
                                    <button onclick="fokus('barcode');" type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div onclick=""  class="modal " id="jmlnota">                       
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Tambah Jumlah Produk</h4>
                                </div>
                                <div class="modal-body" id="listmember">
                                    <form class="form-inline" action="/action_page.php">
                                        <label for="member_name" class="mr-sm-2">Jml:</label>
                                        <input onkeyup="insertnotaproduk();" type="number" class="form-control mb-2 mr-sm-2" placeholder="Masukkan Jumlah" id="qtyproduct" value="1"> &nbsp
                                        <input id="qtyproduct_id" value="0"/>
                                        <button onclick="insertnotaproduk();" type="button" class="btn btn-primary">Submit</button>
                                    </form>                               
                                </div>
                                <div class="modal-footer">
                                    <button onclick="fokus('barcode');" type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function hariini(){
                            $("#from").val('<?=date("Y-m-d");?>');
                            $("#to").val('<?=date("Y-m-d");?>');
                            listnota(-1);
                        }
                        function modalstatus(){
                            $.get("<?=base_url("posisishift");?>")
                            .done(function(data){ 
                                $("#modalstatus").val(data);
                                if(data=='keluar'){                             
                                    $(".btn-child").prop('disabled', true);
                                    $("#btnmodalawal").attr('data-original-title', 'Modal Awal dari Owner');
                                }
                                if(data=='masuk'){                                
                                    $(".btn-child").prop('disabled', false);     
                                    $("#btnmodalawal").attr('data-original-title', 'Update Modal Awal');
                                }
                            });                     
                        }
                        function modalnominal(){
                            $.get("<?=base_url("nominalkas");?>")
                            .done(function(data){                              
                                 $("#kasterakhirval").val(data);   
                                 $(".kasterakhir").html(formatRupiah(data));
                            });                   
                        }
                        function modalawal(){
                            $.get("<?=base_url("modalawalkas");?>")
                            .done(function(data){                  
                                 $(".kasawal").html(formatRupiah(data));
                            });                   
                        }
                        function shift(){
                            $.get("<?=base_url("shift");?>")
                            .done(function(data){                  
                                 $(".kasshift").html(data);          
                                 $("#kasshift").val(data);
                            });                   
                        }
                        function datamodalkas(){
                            //realtime kas
                            modalnominal();
                            //modal awal
                            modalawal();
                            //shift
                            shift();
                        }
                        </script>
                        <script>
                        function modalkas(type){
                            if(type=='masuk'){
                                $("#showmodalawal").modal();
                                fokus('modalawal');
                            }
                            if(type=='keluar'){
                                $("#showmodalakhir").modal();
                                fokus('modalakhir');
                                $('#modalakhir').val(0).focus();
                            }

                            datamodalkas();
                            
                        }
                        </script>
                        <script>
                        function kasmodal(kas_type){
                            let store_id=<?=session()->get("store_id");?>;
                            let ok = confirm('Are you sure?');
                            let en = false;
                            let kasterakhir = $("#kasterakhirval").val(); 
                            // alert(ok);
                            let kas_nominal=0;
                            if(ok==true){
                                if(kas_type=='masuk'){
                                // alert(kas_type);
                                   kas_nominal=$("#modalawal").val();
                                    $("#showmodalawal").modal('hide');
                                    $('#modalawal').val(0);
                                    en = true;
                                }else if(kas_type=='keluar'){
                                    kas_nominal=$("#modalakhir").val();
                                    kas_nominal=parseInt(kas_nominal);
                                    if(kasterakhir==kas_nominal){
                                        $("#showmodalakhir").modal('hide');
                                        $('#modalakhir').val(0);
                                        en = true;
                                    }else{
                                        en = false;
                                        alert("Jumlah penarikan tidak sama dengan aktual kas!");
                                        setTimeout(function(){
                                           fokus('modalakhir');
                                           $("#modalakhir").val(0);
                                        },500);
                                    }
                                }
                                // alert(kas_type);
                                if(en==true){
                                    // alert("<?=base_url("kasmodal");?>?kas_type="+kas_type+"&kas_nominal="+kas_nominal+"&store_id="+store_id);
                                    $.get("<?=base_url("kasmodal");?>",{kas_type:kas_type,kas_nominal:kas_nominal,store_id:store_id})
                                    .done(function(data){
                                        alert(data);
                                        modalstatus();
                                    });
                                    setTimeout(function(){
                                        datamodalkas();
                                    },1000);
                                }
                            }
                        }

                        </script>
                        <script>
                        function fokus(type){
                            switch (type) {
                                case 'barcode':
                                    $("#inputbarcode").focus();
                                    $("#fokus").val("barcode"); 
                                break;         
                                case 'cari':
                                    $("#cariproduk").focus();
                                    $("#fokus").val("cari"); 
                                break;         
                                case 'bayar':
                                    $("#uang").focus();
                                    $("#fokus").val("bayar"); 
                                break;          
                                case 'modalawal':
                                    $("#modalawal").focus();
                                    $("#fokus").val("modalawal"); 
                                break;            
                                case 'modalakhir':
                                    $("#modalakhir").focus();
                                    $("#fokus").val("modalakhir"); 
                                break;            
                                case 'insertqty':
                                    $("#qtyproduct").focus();
                                    $("#fokus").val("insertqty"); 
                                break;                                                             
                                default:
                                    $("#inputbarcode").focus();
                                    $("#fokus").val("barcode"); 
                                break;
                            }
                        }
                        </script>
                        <script>
                        function print(transaction_id){
                            window.open('<?=base_url("transactionprint?transaction_id=");?>'+transaction_id,'_blank');
                        }
                        function cekstatus(transaction_id){
                             $.get("<?=base_url("cekstatus");?>",{transaction_id:transaction_id})
                            .done(function(data){  
                                if(data==0){
                                    $("#printicon").show();
                                }else{
                                    $("#printicon").hide();
                                }
                            });
                        }
                        function pelunasan(){                           
                            let transaction_id = $("#transaction_id").val();
                            let transaction_no = $("#transaction_no").val();
                            let transaction_bill = $("#tagihan").val();
                            let transaction_pay=$("#bayarannya").val();
                            let transaction_change=$("#kembaliannya").val();
                            let shift=$("#kasshift").val();
                            // $('#test').html('<?=base_url("pelunasan");?>?transaction_id='+transaction_id+"&transaction_bill="+transaction_bill+"&transaction_pay="+transaction_pay+"&transaction_change="+transaction_change+"&shift="+shift+"&transaction_no="+transaction_no);
                            $.get("<?=base_url("pelunasan");?>",{transaction_id:transaction_id,transaction_bill:transaction_bill,transaction_pay:transaction_pay,transaction_change:transaction_change,shift:shift,transaction_no:transaction_no})
                            .done(function(data){                                 
                                updatestatus(transaction_id, data);
                                print(transaction_id);                                
                                $("#bayar").modal('hide');
                                cekstatus(transaction_id);
                                fokus('barcode');
                            });
                        }
                        function formatRupiah(num){
                            var str = num.toString().replace("", ""), parts = false, output = [], i = 1, formatted = null;
                            if(str.indexOf(".") > 0) {
                                parts = str.split(".");
                                str = parts[0];
                            }
                            str = str.split("").reverse();
                            for(var j = 0, len = str.length; j < len; j++) {
                                if(str[j] != ",") {
                                output.push(str[j]);
                                if(i%3 == 0 && j < (len - 1)) {
                                    output.push(".");
                                }
                                i++;
                                }
                            }
                            formatted = output.reverse().join("");
                            return("" + formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
                        };
                        function kembalian(){
                            let uang = $("#uang").val();
                            let tagihan = $("#tagihan").val();
                            let kembalian = uang-tagihan;
                            $("#kembaliannya").val(kembalian);
                            $("#bayarannya").val(uang);
                            // alert(kembalian);
                            $(".dibayar").html(formatRupiah(uang));
                            $(".kembalian").html(formatRupiah(kembalian));
                        }
                        function closebayar(){
                            $("#bayar").hide();
                        }
                        function bayar(){
                            $("#bayar").modal();
                            fokus('bayar');
                            let tagihan = $("#tagihan").val();
                            $(".bill").html(formatRupiah(tagihan));  
                        }
                        function cariproduk(){
                            let product_name=$("#cariproduk").val();
                            let type=$("#typesearch").val();
                            plistproduct(type,product_name);
                        }
                        function refreshlistproduct(){
                            let typelist=$("#typesearch").val();
                            plistproduct(typelist,'');
                        }
                        function plistproduct(type,product_name){
                            if(type=="gambar"){
                                // alert("<?=base_url("listproductgambar");?>?product_name="+product_name);
                                $.get("<?=base_url("listproductgambar");?>",{product_name:product_name})
                                .done(function(data1){
                                    $("#listproduct").html(data1);
                                    $("#typesearch").val("gambar");
                                });
                            }
                            if(type=="list"){
                                $.get("<?=base_url("listproductlist");?>",{product_name:product_name})
                                .done(function(data2){
                                    $("#listproduct").html(data2);
                                    $("#typesearch").val("list");
                                });
                            }  
                            // fokus('barcode');                          
                        }
                        function listnota(transaction_status){                         
                            let from =$("#from").val();
                            let to =$("#to").val();
                            if(transaction_status=='-1'){transaction_status=$("#listnotastatus").val();}
                            // alert("<?=base_url("listnota");?>?transaction_status="+transaction_status+"&from="+from+"&to="+to);
                            $.get("<?=base_url("listnota");?>",{transaction_status:transaction_status,from:from,to:to})
                            .done(function(data){
                                $("#listnota").html(data);
                                $("#listnotastatus").val(transaction_status);
                                if(transaction_status==0){$("#keterangan").html("Transaksi Sukses");}
                                if(transaction_status==2){$("#keterangan").html("Transaksi Pending");}
                            });
                        }
                        function nota(transaction_id){
                            // alert("<?=base_url("nota");?>?transaction_id="+transaction_id);
                            $.get("<?=base_url("nota");?>",{transaction_id:transaction_id})
                            .done(function(data){
                                $("#nota").html(data);
                                setTimeout(function(){                                    
                                    cekstatus(transaction_id);
                                    fokus('barcode');                                  
                                },100);
                            });
                        }
                        function createnota(){
                            let transaction_shift=$("#kasshift").val();
                            $.get("<?=base_url("createnota");?>",{transaction_shift:transaction_shift})
                            .done(function(data){
                                // alert(data);
                                listnota($("#listnotastatus").val());
                                nota(data);
                            });
                        }
                        function insertnotaproduk(){
                            let product_id = $("#qtyproduct_id").val();
                            let qty = $("#qtyproduct").val();
                            insertnotaqty(product_id,qty);
                        }
                        function insertjmlnota(product_id){
                            fokus('insertqty');
                            $("#jmlnota").modal();
                            $("#qtyproduct_id").val(product_id);                            
                        }
                        //masukin product hanya multi qty
                        function insertnotaqty(product_id,transactiond_qty){
                            let transaction_id = $("#transaction_id").val();
                            // alert("<?=base_url("insertnota");?>?transaction_id="+transaction_id+"&product_id="+product_id);
                            $.get("<?=base_url("insertnota");?>",{transaction_id:transaction_id,product_id:product_id,transactiond_qty:transactiond_qty})
                            .done(function(data){
                                // alert(data);
                                listnota($("#listnotastatus").val());
                                nota(transaction_id);
                                
                                refreshlistproduct();
                            });
                        }
                        //masukin product hanya satu pcs
                        function insertnota(product_id){
                            let transaction_id = $("#transaction_id").val();
                            // alert("<?=base_url("insertnota");?>?transaction_id="+transaction_id+"&product_id="+product_id);
                            $.get("<?=base_url("insertnota");?>",{transaction_id:transaction_id,product_id:product_id})
                            .done(function(data){
                                // alert(data);
                                listnota($("#listnotastatus").val());
                                nota(transaction_id);
                                
                                refreshlistproduct();
                            });
                        }
                        function insertnotabarcode(product_batch){

                            let transaction_id = $("#transaction_id").val();
                            $.get("<?=base_url("insertnota");?>",{transaction_id:transaction_id,product_batch:product_batch})
                            .done(function(data){
                                // alert(data);
                                listnota($("#listnotastatus").val());
                                nota(transaction_id);
                            });
                        }
                        function deletenota(transaction_id){
                            // alert("<?=base_url("deletenota");?>?transaction_id="+transaction_id);
                            let ok = confirm(' you want to delete?');
                            // alert(ok);
                            if(ok==true){
                                $.get("<?=base_url("deletenota");?>",{transaction_id:transaction_id})
                                .done(function(data){
                                    // alert(data);
                                    listnota($("#listnotastatus").val());
                                    nota(transaction_id);
                                    refreshlistproduct();
                                });
                            }
                        }
                        function updatestatus(transaction_id, transaction_status){
                            // alert("<?=base_url("updatestatus");?>?transaction_id="+transaction_id+"&transaction_status="+transaction_status);                            
                            $.get("<?=base_url("updatestatus");?>",{transaction_id:transaction_id,transaction_status:transaction_status})
                            .done(function(data){
                                $("#status").val(transaction_status);
                                cekstatus(transaction_id);
                            });
                        }
                        function updateqty(transactiond_id, type){
                            // alert("<?=base_url("updateqty");?>?transactiond_id="+transactiond_id+"&type="+type);                            
                            $.get("<?=base_url("updateqty");?>",{transactiond_id:transactiond_id,type:type})
                            .done(function(data){
                                // alert(data);
                                listnota($("#listnotastatus").val());
                                let transaction_id = $("#transaction_id").val();
                                nota(transaction_id);
                                updatestatus(transaction_id, 2);
                                refreshlistproduct();
                            });
                        }
                        function deletetransactiond(transaction_id,product_id,product_qty){
                            // alert("<?=base_url("deletetransactiond");?>?transactiond_id="+transactiond_id+"&product_id="+product_id+"&product_qty="+product_qty);
                            let ok = confirm(' you want to delete?');
                            // alert(ok);
                            if(ok==true){
                                $.get("<?=base_url("deletetransactiond");?>",{transaction_id:transaction_id,product_id:product_id,product_qty:product_qty})
                                .done(function(data){
                                    // alert(data);
                                    listnota($("#listnotastatus").val());
                                    nota(transaction_id);
                                    updatestatus(transaction_id, 2);
                                    refreshlistproduct();
                                });
                            }
                        }
                        $(document).ready(function(){
                            listnota($("#listnotastatus").val());
                            plistproduct('gambar','');
                            closebayar();
                            modalstatus();
                            shift();
                        });
                        $(document).on("keyup", function(e){ 
                            let ifokus = $("#fokus").val();                           
                            if(e.which == 9 && (ifokus=="" || ifokus=="barcode")){
                                fokus('cari');
                            }                          
                            if(e.which == 9 && ifokus=="cari"){
                                fokus('barcode');
                            }
                            // alert(e.which);
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
                                    isset(session()->get("halaman")['13']['act_create']) 
                                    && session()->get("halaman")['13']['act_create'] == "1"
                                )
                            ) {?>
                            let transaction_id =   $("#transaction_id").val();                
                            if(e.which == 17 && transaction_id>0){
                               bayar();
                            }
                                                   
                            if(e.which == 13 && ifokus=="barcode"){
                                let product_batch = $("#inputbarcode").val();
                                if(product_batch==""){alert("Barcode tidak boleh kosong!");}
                                insertnotabarcode(product_batch);
                                $("#inputbarcode").val("");
                            }    
                                                      
                            if(e.which == 13 && ifokus=="bayar"){
                                pelunasan();
                            }     
                                                      
                            if(e.which == 13 && ifokus=="modalawal"){
                                kasmodal('masuk');
                            }     
                                                      
                            if(e.which == 13 && ifokus=="modalakhir"){
                                kasmodal('keluar');
                            }   
                            <?php }?>                       
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select').select2();
    var title = "Poin of Sale";
    $("title").text(title);
    $(".card-title").text(title);
    $("#page-title").text(title);
    $("#page-title-link").text(title);
    setTimeout(() => {
        $(".sidebartoggler").click();
        $(".page-titles").hide();
        $("#inputbarcode").focus();
    }, 300);
    
</script>

<?php echo  $this->include("template/footer_v"); ?>