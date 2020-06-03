<?php
include('config/functions.php');
if (!isLoggedIn()) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
}


include_once('layout/head.php');
$admin_stat_insta_1=admin_stat_insta(1);
$admin_stat_insta_24=admin_stat_insta(24);
$admin_stat_insta_168=admin_stat_insta(168);
$admin_stat_insta_all=admin_stat_insta_all();

include_once('views/index.php');



include_once('layout/footer.php');


$sth = $dbh->prepare("SELECT EXTRACT(MONTH FROM `time_added`) as month, EXTRACT(YEAR FROM `time_added`) as year, SUM(`all_count`) as total FROM last_instagram WHERE  YEAR(`time_added`) = YEAR(NOW()) GROUP BY month, year ORDER BY year DESC, month DESC");
$sth->execute();
$stata = $sth->fetchAll();
$stata_rev = array_reverse($stata);

foreach ($stata_rev as $one){
    $stata_one[$one['month']]=$one;
}

for ($m=1;$m<=12;$m++ ){
    if($stata_one[$m]['total']>=1){
        $stata_arr[]="'".$stata_one[$m]['total']."'";
        $stata_arr_max[]=$stata_one[$m]['total'];
    }else{
        $stata_arr[]=0;

    }
}
$max=max($stata_arr_max);
$stata=implode(',',$stata_arr);
?>

<script type="application/javascript">
    (function ($) {
        // USE STRICT
        "use strict";
        try {
            // Recent Report
            const brandService = 'rgba(0,173,95,0.8)'

            var elements = 12
            var data1 = [<?php echo $stata;?>]

            var ctx = document.getElementById("recent-rep-chart");
            if (ctx) {
                ctx.height = 250;
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Нояюрь', 'Декабрь'],
                        datasets: [
                            {
                                label: 'Видео',
                                backgroundColor: brandService,
                                borderColor: 'transparent',
                                pointHoverBackgroundColor: '#fff000',
                                borderWidth: 0,
                                data: data1

                            },
                        ]
                    },
                    options: {
                        maintainAspectRatio: true,
                        legend: {
                            display: false
                        },
                        responsive: true,
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    drawOnChartArea: true,
                                    color: '#f2f2f2'
                                },
                                ticks: {
                                    fontFamily: "Poppins",
                                    fontSize: 12
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    maxTicksLimit: 5,
                                    stepSize: 50,
                                    max: <?php echo intval($max);?>,
                                    fontFamily: "Poppins",
                                    fontSize: 12
                                },
                                gridLines: {
                                    display: true,
                                    color: '#f2f2f2'

                                }
                            }]
                        },
                        elements: {
                            point: {
                                radius: 0,
                                hitRadius: 10,
                                hoverRadius: 4,
                                hoverBorderWidth: 3
                            }
                        }


                    }
                });
            }

            // Percent Chart
            var ctx = document.getElementById("percent-chart");
            if (ctx) {
                ctx.height = 280;
                var myChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        datasets: [
                            {
                                label: "My First dataset",
                                data: [60, 40],
                                backgroundColor: [
                                    '#00b5e9',
                                    '#fa4251'
                                ],
                                hoverBackgroundColor: [
                                    '#00b5e9',
                                    '#fa4251'
                                ],
                                borderWidth: [
                                    0, 0
                                ],
                                hoverBorderColor: [
                                    'transparent',
                                    'transparent'
                                ]
                            }
                        ],
                        labels: [
                            'Видео'
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        cutoutPercentage: 55,
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        },
                        legend: {
                            display: false
                        },
                        tooltips: {
                            titleFontFamily: "Poppins",
                            xPadding: 15,
                            yPadding: 10,
                            caretPadding: 0,
                            bodyFontSize: 16
                        }
                    }
                });
            }

        } catch (error) {
            console.log(error);
        }




    })(jQuery);
</script>
