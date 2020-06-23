
        <!-- HEADER MOBILE-->
        <?php  include_once('header_mobile.php'); ?>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->

        <?php  include_once('left_menu.php'); ?>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->

            <?php  include_once('header_desktop.php'); ?>

            <!-- HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1">Обзор</h2>
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-25">
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c1">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-time"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo $admin_stat_insta_1->count; ?></h2>
                                                <span>За последний час</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c2">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-calendar"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo $admin_stat_insta_24->count;?></h2>
                                                <span>за последние 24 часа</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c3">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-view-week"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo $admin_stat_insta_168->count;?></h2>
                                                <span>за неделю</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c4">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-calendar-check"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo $admin_stat_insta_all->count;?></h2>
                                                <span>за все время</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="au-card recent-report">
                                    <div class="au-card-inner">
                                        <h3 class="title-2">Спарсенных видео</h3>
                                        <div class="chart-info">
                                            <div class="chart-info__left">
                                                <div class="chart-note">
                                                    <span class="dot dot--blue"></span>
                                                    <span>количество</span>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="recent-report__chart">
                                            <canvas id="recent-rep-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <h2 class="title-1 m-b-25 ">Топ 10 видео недели</h2>
                                <div class="table-responsive table--no-card m-b-40">
                                    <table class="table table-borderless table-striped table-earning ">
                                        <thead class="">
                                        <tr  class="bg-primary">
                                            <th  class="bg-primary">Группа</th>
                                            <th  class="bg-primary">Видео</th>
                                            <th  class="bg-primary">CTR</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        global $dbh;
                                        $week_sth = $dbh->prepare("SELECT * FROM `instagrams` WHERE posting >= DATE_SUB(NOW() , INTERVAL 168 HOUR) ORDER BY ctr DESC LIMIT 10");
                                        $week_sth->execute();
                                        $week_results = $week_sth->fetchAll();
                                        foreach ($week_results as $week_result){
                                            echo ' <tr>';
                                            echo '<td><a href="https://www.instagram.com/'.$week_result["group_name"].'/" target="_blank">'.$week_result["group_name"].'</a></td><td><a href="https://www.instagram.com/p/'.$week_result["post"].'/" target="_blank">'.$week_result["post"].'</a></td><td>'.$week_result["ctr"].'</td>';
                                            echo ' </tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h2 class="title-1 m-b-25">Топ 10 видео месяца</h2>
                                <div class="table-responsive table--no-card m-b-40">
                                    <table class="table table-borderless table-striped table-earning">
                                        <thead>
                                        <tr>
                                            <th class="bg-success">Группа</th>
                                            <th class="bg-success" >Видео</th>
                                            <th class="bg-success">CTR</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        global $dbh;
                                        $month_sth = $dbh->prepare("SELECT * FROM `instagrams` WHERE posting >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) ORDER BY ctr DESC LIMIT 10");
                                        $month_sth->execute();
                                        $month_results = $month_sth->fetchAll();
                                        foreach ($month_results as $month_result){
                                            echo ' <tr>';
                                            echo '<td><a href="https://www.instagram.com/'.$month_result["group_name"].'/" target="_blank">'.$month_result["group_name"].'</a></td><td><a href="https://www.instagram.com/p/'.$month_result["post"].'/" target="_blank">'.$month_result["post"].'</a></td><td>'.$month_result["ctr"].'</td>';
                                            echo ' </tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-9">
                                <h2 class="title-1 m-b-25">Последние 10 групп</h2>
                                <div class="table-responsive table--no-card m-b-40">
                                    <table class="table table-borderless table-striped table-earning">
                                        <thead>
                                            <tr>

                                                <th>Группа</th>
                                                <th >За последний проход</th>
                                                <th >Всего</th>
                                                <th>Когда в последний раз</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        global $dbh;
                                        $sth = $dbh->prepare("SELECT * FROM `last_instagram` ORDER BY time_added DESC LIMIT 10");
                                        $sth->execute();
                                        $results = $sth->fetchAll();
                                        foreach ($results as $result){
                                            echo ' <tr>';
                                            echo '<td><a href="https://www.instagram.com/'.$result["group_name"].'/" target="_blank">'.$result["group_name"].'</a></td><td>'.$result["last_count"].'</td><td>'.$result["all_count"].'</td><td>'.$result["time_added"].'</td>';
                                            echo ' </tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <h2 class="title-1 m-b-25">Top 10 группы</h2>
                                <div class="au-card au-card--bg-blue au-card-top-countries m-b-40">
                                    <div class="au-card-inner">
                                        <div class="table-responsive">
                                            <table class="table table-top-countries">
                                                <tbody>
                                                <?php
                                                global $dbh;
                                                $sth = $dbh->prepare("SELECT * FROM `last_instagram` ORDER BY all_count DESC LIMIT 10");
                                                $sth->execute();
                                                $results = $sth->fetchAll();
                                                foreach ($results as $result){
                                                    echo ' <tr>';
                                                    echo '<td><a href="https://www.instagram.com/'.$result["group_name"].'/" target="_blank" style="color:#fff">'.$result["group_name"].'</a></td><td>'.$result["all_count"].'</td>';
                                                    echo ' </tr>';
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
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
            <!-- END MAIN CONTENT-->
            <!-- END PAGE CONTAINER-->
        </div>
