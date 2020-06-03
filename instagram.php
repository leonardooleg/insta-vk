<?php
include('config/functions.php');
if (!isLoggedIn()) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
}

require __DIR__ . '/../vendor/autoload.php';
include_once('layout/head.php');


  include_once('views/header_mobile.php');
  include_once('views/left_menu.php');
$index_import = index_import();
  ?>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->

            <?php  include_once('views/header_desktop.php'); ?>

            <!-- HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
        <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">


                <div class="row m-t-30">
                    <div class="col-md-12">
                        <div class="alert alert-info" role="alert">
                           Всего не добавленных записей: <?php echo count_instagram(); ?>
                        </div>
                        <!-- DATA TABLE-->
                        <div class="table-responsive m-b-40">
                            <table class="table table-borderless table-data3">
                                <thead>
                                <tr>
                                    <th>Группа</th>
                                    <th>Пост</th>
                                    <th>Лайки</th>
                                    <th>Просмотры</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                               $all_instagram =all_instagram();
                                foreach ($all_instagram as $result){
                                    echo ' <tr>';
                                    echo '<td><a href="https://www.instagram.com/'.$result["group_name"].'/" target="_blank" >'.$result["group_name"].'</a></td><td><a href="https://www.instagram.com/p/'.$result["post"].'/" target="_blank" >'.$result["post"].'</a></td><td>'.$result["likes"].'</td><td>'.$result["views"].'</td>';
                                    echo ' </tr>';
                                }
                                ?>

                                </tbody>
                            </table>
                        </div>
                        <!-- END DATA TABLE-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="copyright">
                            <p>Copyright © 2018 Colorlib. All rights reserved. Template by <a href="https://colorlib.com">Colorlib</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php

include_once('layout/footer.php');