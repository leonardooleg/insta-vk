<?php


include('config/functions.php');
if ($_GET['captcha']=='go'){
    $captcha=true;
    $isLoggedIn=true;
}else{
    $isLoggedIn=isLoggedIn();
}
//if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;} // Отвечаем только на Ajax

if (!$isLoggedIn) {
    header("location: login.php");
    session_destroy();
    exit;
}else{

    $data=index_import();
    $links_vk=explode(';',$data->links_vk);
    $links_insta = explode(';',$data->links_insta);
    shuffle($links_insta); //перемішати масив
    $count_insta=count($links_insta);
    $count_day =$data->count_day;
    $priority_count_day =$data->priority_count_day;

    /*Для статуса*/
    $status_api='go_status.txt';
    if (!file_exists($status_api)) {
        $fp = fopen($status_api, 'a');
        fwrite($fp, '0' );
        fclose($fp);
    }else{
        file_put_contents($status_api, '0;0;0;instagram');
    }
    $all_status=1;
    /*Для статуса*/

    /*Парсер Інсти*/

    $last=admin_stat_insta(5); ///*Тільки 1 раз за день*/
    if($last->count>=1){
        file_put_contents($status_api, $all_status.';insta-limit-day'.';finish;instagram');
        sleep(10);
    }else{
        insta_create();
        $set_pars =index_import();
        $min_crt =$set_pars->crt;
        $max_crt =$set_pars->max_crt;
        // Request with proxy
        $insta_limit_day=1;
        foreach ($links_insta as $link_insta) {
            $i=0;
            $insta_name = str_replace("https://www.instagram.com/", "", $link_insta);
            $insta_name = str_replace("/", "", $insta_name);
            $posts = $instagram->getMedias($insta_name);
            if(!isset($posts[0]['type'])){
                sleep(4);
            }

            $last_insta_bd =last_insta_bd($insta_name);
            if ($last_insta_bd->post == $posts[0]['shortCode'] ){
                file_put_contents($status_api, $all_status.';continue-'.$posts[0]['shortCode'].';continue;instagram');
                continue;
            }
            // PDO error mode is set to exception
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // the transaction begins
            $dbh->beginTransaction();
            // our SQL statements

            foreach ($posts as $post) {
                if ($post['type'] == 'video') {
                    $shortCode = $post['shortCode'];
                    if ($last_insta_bd->post == $shortCode ){
                        file_put_contents($status_api, $all_status.';continue-'.$shortCode.';break;instagram');
                        break ; //ящо вже існує останній в базі
                    }
                    $likes = $post['likesCount'];
                    $views = $post['videoViews'];
                    $crt = round($likes / $views,2)  ;
                    if ($crt >= $min_crt and $crt <= $max_crt) {
                        $file = get_file($post['videoStandardResolutionUrl'], $post['owner']['id'], $post['shortCode']);
                        $video_duration = video_duration($file);
                        if ($video_duration<51){
                            $time_added = date('Y-m-d H:i:s');
                            $group_id = $post['owner']['id'];
                            $group_name = $post['owner']['username'];
                            $file_id = $post['id'];
                            $n=$dbh->exec("INSERT INTO instagrams (group_name, group_id, post, likes, views, ctr, file_id, status, time_added) VALUES ('$group_name', '$group_id', '$shortCode', '$likes', '$views', '$crt', '$file_id', 1, '$time_added')");
                            if($i===0){
                                $last_pars=$shortCode;
                            }
                            $i++;
                            file_put_contents($status_api, $all_status.';'.$link_insta.';0;instagram');
                            $all_status++;
                        }else{
                            $unlink = unlink($file);
                        }
                    }
                }
            }
            // commit the transaction
            $dbh->commit();

            /*Зберігаємо ласт*/
            if($last_pars) {
                $time_added = date('Y-m-d H:i:s');
                if ($last_insta_bd->id >= 1) {
                    $all_count = $last_insta_bd->all_count + $i;
                    $sql = "UPDATE last_instagram SET group_name=:group_name, post=:post, last_count=:last_count, all_count=:all_count, time_added=:time_added WHERE id=:id";
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindParam(':group_name', $insta_name, PDO::PARAM_STR);
                    $stmt->bindParam(':post', $last_pars, PDO::PARAM_STR);
                    $stmt->bindParam(':last_count', $i, PDO::PARAM_STR);
                    $stmt->bindParam(':all_count', $all_count , PDO::PARAM_STR);
                    $stmt->bindParam(':time_added', $time_added, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $last_insta_bd->id, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    $sql = "INSERT INTO last_instagram (group_name, post, last_count, all_count, time_added) VALUES (?,?,?,?,?)";
                    $dbh->prepare($sql)->execute([$insta_name, $last_pars, $i, $last_insta_bd->all_count + $i, $time_added]);
                }
            }else{

                $sql = "UPDATE last_instagram SET last_count=:last_count, time_added=NOW() WHERE id=:id";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':last_count', $i, PDO::PARAM_STR);
                $stmt->bindParam(':id', $last_insta_bd->id, PDO::PARAM_INT);
                $stmt->execute();
            }
            /*Зберігаємо ласт*/
            sleep(5);
            if($insta_limit_day==$insta_limit) break;
            $insta_limit_day++;
        }

    }

    /*Парсер Інсти*/

    $all_status=$all_status-1;
    file_put_contents($status_api, $all_status.';'.$link_insta.';finish;instagram');



    /*Часовий інтервал*/
    $d = strtotime("+1 day");
    $date= date("Y-m-d ", $d);

    for($i = 0, $a = 30; $i < 33; $i++){
        $b= $date.date("H:i", strtotime("today 7 hours 30 minutes + $a minutes")).':37.623812';
        $need_time[]= strtotime($b);
        //echo strtotime($b).'<br>';
        $a += 30;

    }
    /*Часовий інтервал*/
    /*Часовий інтервал прыорытетний*/


    for($ip = 0, $ap = 30; $ip < 23; $ip++){
        $bp= $date.date("H:i", strtotime("today 1 hours 03 minutes + $ap minutes")).':37.623812';
        $priority_need_time[]= strtotime($bp);
        //echo strtotime($b).'<br>';
        $ap += 60;

    }
    /*Часовий інтервал пріоритетний*/


    /*Добалятор ВК*/

    $count_vk = 1;

    if (!file_exists($status_api)) {
        $fp = fopen($status_api, 'a');
        fwrite($fp, '0');
        fclose($fp);
    } else {
        file_put_contents($status_api, '0;0;0;vk');
    }

    ///Добалятор приоритетных ВК
    $priority_all_instagram = priority_all_instagram($priority_count_day);

    $priority_links_vk=explode(';',$data->priority_links_vk);
    if ($priority_links_vk[0]!=''){
        foreach ($priority_links_vk as $priority_link_vk) {
            foreach ($priority_all_instagram as $priority_instagram) {
                $vk_insert = vk_insert($priority_instagram, $priority_link_vk, $count_vk, $priority_need_time);//////
                if ($vk_insert == false) {
                    break;
                }
                if ($count_day == $count_vk) {
                    break;
                }
                file_put_contents($status_api, $count_vk . ';priority-' . $priority_instagram["post"] . '.mp4;0;vk');
                $count_vk++;

            }
        }
    }

    ///Добалятор звичайних ВК

    $all_instagram = all_instagram();
    foreach ($all_instagram as $instagram) {
        $group_id = array_rand($links_vk);
        $group_id = $links_vk[$group_id];

        $vk_insert = vk_insert($instagram,$group_id,$count_vk,$need_time); ///////

        if (!$vk_insert) {
            break;
        }

        if ($count_day == $count_vk) {
            break;
        }
        file_put_contents($status_api, $count_vk . ';' . $instagram["post"] . '.mp4;0;vk');
        $count_vk++;

    }

    $count_vk=$count_vk-1;
    file_put_contents($status_api, $count_vk.';'.$instagram["post"].'.mp4;finish;vk');



    /*Добалятор ВК*/


    /* header("location: import.php");*/











}







