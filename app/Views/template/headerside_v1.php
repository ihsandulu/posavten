<div class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar hidebar" style="overflow:auto;">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="nav-devider"></li>
                <li class="nav-label">Home</li>
                <li>
                    <a class="" href="<?= base_url(); ?>" aria-expanded="false">
                        <i class="fa fa-tachometer"></i><span class="hide-menu">Dashboard</span>
                    </a>

                </li>
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
                        isset(session()->get("halaman")['1']['act_read']) 
                        && session()->get("halaman")['1']['act_read'] == "1"
                    )
                ) { ?>
                <li class="nav-label">Master</li>
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
                        isset(session()->get("halaman")['6']['act_read']) 
                        && session()->get("halaman")['6']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("mstore"); ?>" aria-expanded="false"><i class="fa fa-building"></i><span class="hide-menu">Toko</span></a>
                </li>
                <?php }?>
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
                        isset(session()->get("halaman")['2']['act_read']) 
                        && session()->get("halaman")['2']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="has-arrow  " href="#" aria-expanded="false" data-toggle="collapse" data-target="#demo"><i class="fa fa-user"></i><span class="hide-menu">Manajemen User <span class="label label-rouded label-warning pull-right">2</span></span></a>
                    <ul aria-expanded="false" id="demo" class="collapse">
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
                                isset(session()->get("halaman")['3']['act_read']) 
                                && session()->get("halaman")['3']['act_read'] == "1"
                            )
                        ) { ?>
                        <li><a href="<?= base_url("mposition"); ?>"><i class="fa fa-caret-right"></i> &nbsp;Posisi</a></li>
                        <?php }?>
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
                                isset(session()->get("halaman")['5']['act_read']) 
                                && session()->get("halaman")['5']['act_read'] == "1"
                            )
                        ) { ?>
                        <li><a href="<?= base_url("muser"); ?>"><i class="fa fa-caret-right"></i> &nbsp;User</a></li>
                        <?php }?>
                    </ul>
                </li>
                <?php }?>

                
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
                        isset(session()->get("halaman")['22']['act_read']) 
                        && session()->get("halaman")['22']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="has-arrow  " href="#" aria-expanded="false" data-toggle="collapse" data-target="#demo1"><i class="fa fa-users"></i><span class="hide-menu">Manajemen Member <span class="label label-rouded label-warning pull-right">2</span></span></a>
                    <ul aria-expanded="false" id="demo1" class="collapse">
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
                                isset(session()->get("halaman")['23']['act_read']) 
                                && session()->get("halaman")['23']['act_read'] == "1"
                            )
                        ) { ?>
                        <li><a href="<?= base_url("mpositionm"); ?>"><i class="fa fa-caret-right"></i> &nbsp;Grade Member</a></li>
                        <?php }?>
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
                                isset(session()->get("halaman")['21']['act_read']) 
                                && session()->get("halaman")['21']['act_read'] == "1"
                            )
                        ) { ?>
                        <li><a href="<?= base_url("mmember"); ?>"><i class="fa fa-caret-right"></i> &nbsp;Member</a></li>
                        <?php }?>
                    </ul>
                </li>
                <?php }?>

               

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
                        isset(session()->get("halaman")['19']['act_read']) 
                        && session()->get("halaman")['19']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("msupplier"); ?>" aria-expanded="false"><i class="fa fa-address-book-o "></i><span class="hide-menu">Supplier</span></a>
                </li>
                <?php }?>


                <!-- <?php 
                if (
                    (
                        isset(session()->get("position_administrator")[0][0]) 
                        && (
                            session()->get("position_administrator") == "1" 
                            || session()->get("position_administrator") == "2"
                        )
                    ) ||
                    (
                        isset(session()->get("halaman")['7']['act_read']) 
                        && session()->get("halaman")['7']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("mppn"); ?>" aria-expanded="false"><i class="fa fa-money"></i><span class="hide-menu">PPN</span></a>
                </li>
                <?php }?> -->

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
                        isset(session()->get("halaman")['10']['act_read']) 
                        && session()->get("halaman")['10']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("mcategory"); ?>" aria-expanded="false"><i class="fa fa-cubes"></i><span class="hide-menu">Kategori</span></a>
                </li>
                <?php }?>

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
                        isset(session()->get("halaman")['11']['act_read']) 
                        && session()->get("halaman")['11']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("munit"); ?>" aria-expanded="false"><i class="fa fa-gear"></i><span class="hide-menu">Unit</span></a>
                </li>
                <?php }?>

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
                        isset(session()->get("halaman")['8']['act_read']) 
                        && session()->get("halaman")['8']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("mproduct"); ?>" aria-expanded="false"><i class="fa fa-cube"></i><span class="hide-menu">Produk</span></a>
                </li>
                <?php }?>

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
                        isset(session()->get("halaman")['22']['act_read']) 
                        && session()->get("halaman")['22']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("maccount"); ?>" aria-expanded="false"><i class="fa fa-bookmark-o"></i><span class="hide-menu">Akun</span></a>
                </li>
                <?php }?>

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
                        isset(session()->get("halaman")['23']['act_read']) 
                        && session()->get("halaman")['23']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("mbank"); ?>" aria-expanded="false"><i class="fa fa-bank"></i><span class="hide-menu">Bank</span></a>
                </li>
                <?php }?>

                <?php }?>

               


                <!-- //Transaction// -->
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
                        isset(session()->get("halaman")['9']['act_read']) 
                        && session()->get("halaman")['9']['act_read'] == "1"
                    )
                ) { ?>
                <li class="nav-label">Transaksi</li>

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
                        isset(session()->get("halaman")['13']['act_read']) 
                        && session()->get("halaman")['13']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("transaction"); ?>" aria-expanded="false"><i class="fa fa-handshake-o"></i><span class="hide-menu">POS</span></a>
                </li>
                <?php }?>

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
                        isset(session()->get("halaman")['18']['act_read']) 
                        && session()->get("halaman")['18']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("purchase"); ?>" aria-expanded="false"><i class="fa fa-cart-arrow-down"></i><span class="hide-menu">Pembelian</span></a>
                </li>
                <?php }?>

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
                        isset(session()->get("halaman")['20']['act_read']) 
                        && session()->get("halaman")['20']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("payment"); ?>" aria-expanded="false"><i class="fa fa-money"></i><span class="hide-menu">Pembayaran</span></a>
                </li>
                <?php }?>

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
                        isset(session()->get("halaman")['26']['act_read']) 
                        && session()->get("halaman")['26']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("stockopname"); ?>" aria-expanded="false"><i class="fa fa-cubes"></i><span class="hide-menu">Stockopname</span></a>
                </li>
                <?php }?>

                <?php }?>

                <!-- //Report// -->
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
                        isset(session()->get("halaman")['14']['act_read']) 
                        && session()->get("halaman")['14']['act_read'] == "1"
                    )
                ) { ?>
                <li class="nav-label">Laporan</li>

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
                        isset(session()->get("halaman")['15']['act_read']) 
                        && session()->get("halaman")['15']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("rkas"); ?>" aria-expanded="false"><i class="fa fa-book"></i><span class="hide-menu">Kas</span></a>
                </li>
                <?php }?>
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
                        isset(session()->get("halaman")['18']['act_read']) 
                        && session()->get("halaman")['18']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("purchase?report=OK"); ?>" aria-expanded="false"><i class="fa fa-cart-arrow-down"></i><span class="hide-menu">Pembelian</span></a>
                </li>
                <?php }?>
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
                        isset(session()->get("halaman")['16']['act_read']) 
                        && session()->get("halaman")['16']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("rtransaction"); ?>" aria-expanded="false"><i class="fa fa-file-text-o"></i><span class="hide-menu">Penjualan</span></a>
                </li>
                <?php }?>
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
                        isset(session()->get("halaman")['25']['act_read']) 
                        && session()->get("halaman")['25']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("rprodukmasuk"); ?>" aria-expanded="false"><i class="fa fa-cubes"></i><span class="hide-menu">Produk Masuk</span></a>
                </li>
                <?php }?>
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
                        isset(session()->get("halaman")['24']['act_read']) 
                        && session()->get("halaman")['24']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("rprodukkeluar"); ?>" aria-expanded="false"><i class="fa fa-cubes"></i><span class="hide-menu">Produk Keluar</span></a>
                </li>
                <?php }?>
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
                        isset(session()->get("halaman")['27']['act_read']) 
                        && session()->get("halaman")['27']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("rhistorystock"); ?>" aria-expanded="false"><i class="fa fa-th-list"></i><span class="hide-menu">History Stok</span></a>
                </li>
                <?php }?>
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
                        isset(session()->get("halaman")['21']['act_read']) 
                        && session()->get("halaman")['21']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("rneraca"); ?>" aria-expanded="false"><i class="fa fa-book"></i><span class="hide-menu">Neraca</span></a>
                </li>
                <?php }?>
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
                        isset(session()->get("halaman")['17']['act_read']) 
                        && session()->get("halaman")['17']['act_read'] == "1"
                    )
                ) { ?>
                <li> 
                    <a class="  " href="<?= base_url("rlabarugi"); ?>" aria-expanded="false"><i class="fa fa-trophy"></i><span class="hide-menu">Laba Rugi</span></a>
                </li>
                <?php }?>

                <?php }?>

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</div>