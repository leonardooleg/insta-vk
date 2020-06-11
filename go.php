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
    $count_insta=count($links_insta);
    $count_day =$data->count_day;

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

    $last=admin_stat_insta(1); ///*Тільки 1 раз за день*/
    if($last->count>=1){
        file_put_contents($status_api, $all_status.';insta-limit-day'.';finish;instagram');
        sleep(10);
    }else{
        insta_create();
        $min_crt =index_import();
        // Request with proxy

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
                        break ; //ящо вже існує останній в базі
                    }
                    $likes = $post['likesCount'];
                    $views = $post['videoViews'];
                    $crt = $views / $likes;
                    if ($crt >= $min_crt->crt) {
                        $file = get_file($post['videoStandardResolutionUrl'], $post['owner']['id'], $post['shortCode']);
                        $video_duration = video_duration($file);
                        if ($video_duration<51){
                            $group_id = $post['owner']['id'];
                            $group_name = $post['owner']['username'];
                            $file_id = $post['id'];
                            $n=$dbh->exec("INSERT INTO instagrams (group_name, group_id, post, likes, views, file_id) VALUES ('$group_name', '$group_id', '$shortCode', '$likes', '$views', '$file_id')");
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


    /*Добалятор ВК*/
    if($last->count>=1){
        file_put_contents($status_api, $all_status.';vk-limit-day'.';finish;vk');
        sleep(10);
    }else {
        $all_instagram = all_instagram();
        $count_vk = 1;
        if (!file_exists($status_api)) {
            $fp = fopen($status_api, 'a');
            fwrite($fp, '0');
            fclose($fp);
        } else {
            file_put_contents($status_api, '0;0;0;vk');
        }
        foreach ($all_instagram as $instagram) {

            $group_id = array_rand($links_vk);
            $group_id = $links_vk[$group_id];
            $access_token = '7877701cb9373cfb663a1daa81167e832454887b315565d0e5db849f824c9ddc97ad4aa77337d159cc91f';
            //$message      = 'Hello, world!2';


            $image = __DIR__ . '/uploads/' . $instagram["group_id"] . '/' . $instagram["post"] . '.mp4';

            // Получение сервера vk для загрузки изображения.
            $server = file_get_contents('https://api.vk.com/method/video.save?group_id=' . $group_id . '&access_token=' . $access_token . '&v=5.107');
            $server = json_decode($server);

            if ($server->error->error_msg == 'Access to adding post denied: you can only add 50 posts a day') {
                break;
            }

            if (!empty($server->response->upload_url)) {
                // Отправка изображения на сервер.
                if (function_exists('curl_file_create')) {
                    $curl_file = curl_file_create($image);
                } else {
                    $curl_file = '@' . $image;
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $server->response->upload_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, array('video_file' => $curl_file));
                $upload = curl_exec($ch);
                curl_close($ch);

                $upload = json_decode($upload);
                if ($upload->video_hash) {
                    // Отправляем сообщение.
                    $params = array(
                        'v' => '5.107',
                        'access_token' => $access_token,
                        'owner_id' => '-' . $group_id,
                        'from_group' => '1',
                        'publish_date' => $need_time[$count_vk - 1],
                        'attachments' => 'video' . $upload->owner_id . '_' . $upload->video_id
                    );

                    $add_vk = file_get_contents('https://api.vk.com/method/wall.post?' . http_build_query($params));
                }


                    $unlink = unlink($image);
                    $del_instagram = del_instagram($instagram["id"]);

            }

            if ($count_day == $count_vk) {
                break;
            }
            file_put_contents($status_api, $count_vk . ';' . $instagram["post"] . '.mp4;0;vk');
            $count_vk++;

        }
    }
    $count_vk=$count_vk-1;
    file_put_contents($status_api, $count_vk.';'.$instagram["post"].'.mp4;finish;vk');



    /*Добалятор ВК*/


   /* header("location: import.php");*/











}


