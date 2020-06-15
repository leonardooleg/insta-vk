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
                                    <form action="import_save.php" method="post" enctype="multipart/form-data" class="form-horizontal">
                                        <div class="card-header">
                                            <strong>Настройка парсинга</strong> сайтов
                                        </div>
                                          <div class="card-body card-block">

                                                <div class="row form-group">
                                                    <div class="col col-md-3">
                                                        <label  for="select" class=" form-control-label">Статус</label>
                                                    </div>
                                                    <div class="col-12 col-md-9">
                                                        <select name="status" id="select" class="form-control">
                                                            <option value="0" <?php if($index_import->status!=1) echo 'selected'; ?>>Отключен</option>
                                                            <option value="1" <?php if($index_import->status==1) echo 'selected'; ?>>Включен</option>
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="row form-group">
                                                    <div class="col col-md-3">
                                                        <label for="textarea-input" class=" form-control-label">Ссылки на странички Instagram</label>
                                                    </div>
                                                    <div class="col-12 col-md-9">
                                                        <textarea name="links_insta" id="textarea-input" rows="9" placeholder="Новая с новой строки" class="form-control"><?php if(isset($index_import->links_insta)) echo str_replace(";", "\n", $index_import->links_insta); ?></textarea>
                                                    </div>
                                                </div>

                                                <div class="row form-group">
                                                    <div class="col col-md-3">
                                                        <label for="textarea-input" class=" form-control-label">ID групп ВК</label>
                                                    </div>
                                                    <div class="col-12 col-md-9">
                                                        <textarea name="links_vk" id="textarea-input" rows="9" placeholder="Новая с новой строки" class="form-control"><?php if(isset($index_import->links_vk)) echo str_replace(";", "\n", $index_import->links_vk); ?></textarea>
                                                    </div>
                                                </div>
                                              <div class="row form-group">
                                                  <div class="col col-md-3">
                                                      <label for="text-input" class=" form-control-label">Мінімум CRT</label>
                                                  </div>
                                                  <div class="col-12 col-md-9">
                                                      <input type="text" id="text-input" name="crt" <?php if(isset($index_import->links_vk)) echo  'value="'.$index_import->crt.'"'; ?> placeholder="Text" class="form-control">
                                                      <small class="form-text text-muted">например 0.5, максимум 1 ( рейтинг лайки / просмотры) </small>
                                                  </div>
                                              </div>
                                              <div class="row form-group">
                                                  <div class="col col-md-3">
                                                      <label for="text-input" class=" form-control-label">Количество ВК постов за раз</label>
                                                  </div>
                                                  <div class="col-12 col-md-9">
                                                      <input type="number" id="text-input" name="count_day" <?php if(isset($index_import->links_vk)) echo  'value="'.$index_import->count_day.'"'; ?> placeholder="количество" class="form-control">
                                                      <small class="form-text text-muted">не больше 33</small>
                                                  </div>
                                              </div>



                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fa fa-dot-circle-o"></i> Сохранить
                                            </button>

                                        </div>
                                    </form>
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