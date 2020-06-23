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
                            <?php
                            if($_GET['time']=='old'){
                            echo "Всего записей: ".all_count_instagram();
                            }else{
                             echo "Всего не добавленных записей: ".count_instagram();
                            }
                            ?>


                        </div>



                        <!-- DATA TABLE-->
                        <div class="table-responsive m-b-40 row d-flex flex-wrap">
                            <div class="order-2 card col-md-12">
                                <div class="card-header">
                                    Фильтр по CTR
                                </div>
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputEmail4">Минимум ctr</label>
                                            <input type="text" class="form-control" id="min" name="min" value="0.0">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputPassword4"> Максимум ctr</label>
                                            <input  class="form-control" type="text" id="max" name="max" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="order-12 col-md-12">
                                <table id="instagram" class="table table-borderless table-data3 ">
                                    <thead>
                                    <tr>
                                        <th>Группа</th>
                                        <th>Пост</th>
                                        <th class="text-center">Лайки</th>
                                        <th class="text-center">Просмотры</th>
                                        <th>CTR</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($_GET['time']=='old'){
                                        $all_instagram =old_instagram();
                                    }else{
                                        $all_instagram =all_instagram();
                                    }

                                   $stat=array();
                                    foreach ($all_instagram as $result){
                                        if($result['ctr']>=0.01 and $result['ctr']<0.10){
                                            $stat['0-0.1'][]=$result['ctr'];
                                        }elseif($result['ctr']>=0.1 and $result['ctr']<0.20){
                                            $stat['0.1-0.19'][]=$result['ctr'];
                                        }elseif($result['ctr']>=0.20 and $result['ctr']<0.30){
                                            $stat['0.2-0.29'][]=$result['ctr'];
                                        }elseif($result['ctr']>=0.30 and $result['ctr']<0.40){
                                            $stat['0.3-0.39'][]=$result['ctr'];
                                        }elseif($result['ctr']>=0.40 and $result['ctr']<0.50){
                                            $stat['0.4-0.49'][]=$result['ctr'];
                                        }elseif($result['ctr']>=0.50 and $result['ctr']<0.60){
                                            $stat['0.5-0.59'][]=$result['ctr'];
                                        }elseif($result['ctr']>=0.60 and $result['ctr']<0.70){
                                            $stat['0.6-0.69'][]=$result['ctr'];
                                        }elseif($result['ctr']>=0.70 and $result['ctr']<0.80){
                                            $stat['0.7-0.79'][]=$result['ctr'];
                                        }elseif($result['ctr']>=0.80 and $result['ctr']<0.90){
                                            $stat['0.8-0.89'][]=$result['ctr'];
                                        }elseif($result['ctr']>=0.90 and $result['ctr']<1.00){
                                            $stat['0.9-1.0'][]=$result['ctr'];
                                        }
                                        echo ' <tr>';
                                        echo '<td><a href="https://www.instagram.com/'.$result["group_name"].'/" target="_blank" >'.$result["group_name"].'</a></td><td><a href="https://www.instagram.com/p/'.$result["post"].'/" target="_blank" >'.$result["post"].'</a></td><td class="text-center">'.$result["likes"].'</td><td class="text-center">'.$result["views"].'</td><td>'.$result["ctr"].'</td>';
                                        echo ' </tr>';
                                    }
                                    ?>

                                    </tbody>
                                </table>
                            </div>
                            <div class="order-1 col-md-12">
                                <table class="table table-bordered table-dark  ">
                                    <thead>
                                    <tr>
                                        <th scope="col">Диапазон CTR</th>
                                        <th scope="col">Количество материала</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($stat as $id =>$one_stat ){
                                        echo ' <tr>';
                                        echo ' <td>'.$id.'</td> <td>'.count($one_stat).'</td>';
                                        echo ' </tr>';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <br><hr><br><br>
                            </div>

                        </div>
                        <!-- END DATA TABLE-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="copyright">
                            <p>Copyright © 2020</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php

include_once('layout/footer.php');