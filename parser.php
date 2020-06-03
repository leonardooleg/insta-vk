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
                <div class="card">
                    <div class="form-horizontal">
                        <div class="card-header">
                            <strong>Парсинг </strong> сайтов
                        </div>
                        <div class="card-body card-block">
                            <div class="alert alert-danger" role="alert">
                                <div class="count"></div>  <div class="link"> Ожидаю начало</div>
                            </div>
                            <div class="alert alert-success pars-instagram" role="alert" style="display: none">
                                Все новые видео с Инстаграм скачаны
                            </div>
                            <div class="alert alert-info insert-vk" role="alert" style="display: none">
                                <div class="count-vk"></div>  <div class="group"> Ожидаю начало для ВК</div>
                            </div>
                            <div class="alert alert-success insert-vk" role="alert" style="display: none">
                                Все новые видео добавленны для ВК
                            </div>

                        </div>
                        <div class="card-footer">
                            <button id="runScript" class="btn btn-primary btn-sm">
                                <i class="fa fa-dot-circle-o"></i> Запустить
                            </button>

                        </div>
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

?>
        <script type="application/javascript">
            function showProcess ( sucsess, offset, link ) {
                $('.count').text(offset+' видео добавлено с Инстаграм. ');
                $('.link').text('Текущий сайт:  ' + link);
                $('#runScript').text('В процессе парсинга Инстаграм');
                $('#runScript').click(function(){
                    document.location.href=document.location.href
                });
                sleep(1000);
                scriptOffset();

            }
            function showProcessVK ( sucsess, offset, link ) {
                $('.alert-info.insert-vk').show();
                $('.count-vk').text(offset+' добавлено для ВК. ');
                $('.group').text('Видео:  ' + link);
                $('#runScript').text('В процессе добавления ВК');
                $('#runScript').click(function(){
                    document.location.href=document.location.href
                });
                sleep(1000);
                scriptOffset();

            }

            function scriptOffset () {
                $.ajax({
                    url: "go_status.php",
                    success: function (data) {
                        data = $.parseJSON(data);
                        if(data.type =='instagram'){
                            if (data.sucsess < 1) {
                                showProcess(data.sucsess, data.offset, data.link);
                            } else {
                                $('.alert-success.pars-instagram').show();
                                $('.count').text(data.offset);
                                $('#runScript').text('Закончил Инстаграм');
                            }
                        }else{
                            if (data.sucsess < 1) {
                                showProcessVK(data.sucsess, data.offset, data.link);
                            } else {
                                $('.alert-success.insert-vk').show();
                                $('.count-vk').text(data.offset);
                                $('#runScript').text('Все Закончил ');
                            }
                        }

                    }
                });
            }

            $(document).ready(function() {
                $('#runScript').click(function() {
                    $.ajax({
                        url: "go.php",
                        type: "POST",
                    });
                    scriptOffset();
                    sleep(20000);
                    scriptOffset();
                });

            });
            function sleep(milliseconds) {
                var start = new Date().getTime();
                for (var i = 0; i < 1e7; i++) {
                    if ((new Date().getTime() - start) > milliseconds){
                        break;
                    }
                }
            }

        </script>


