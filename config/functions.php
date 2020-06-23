<?php
session_start();
// connect to database
require __DIR__ . '/../vendor/autoload.php';
require_once(__DIR__ .'/../getid3/getid3.php'); ///бібліотека для длини відео
include_once "conf.php";
use Phpfastcache\Helper\Psr16Adapter;
use InstagramScraper\Instagram;
Unirest\Request::verifyPeer(false);

$dbh = new PDO("mysql:dbname={$db_table};host={$hostname}", $db_login, $db_pass);

$errors   = array();


// call the login() function if register_btn is clicked
if (isset($_POST['login_btn'])) {
    login();
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['login']);
    header("location: ../login.php");
}


// LOGIN USER
function login(){
    global $pas, $errors;
    // grap form values
    $password = md5($_POST['password']);
    // make sure form is filled properly
    if (empty($password)) {
        array_push($errors, "Password is required");
    }
    // attempt login if no errors on form
    if (count($errors) == 0) {
        $pas = md5($pas);

        if ($password == $pas) { // user found
                $_SESSION['login'] = $pas;
                header('location: admin.php');
        }else{
                $_SESSION['login'] = false;
                header('location: login.php');
        }
    }
}

function isLoggedIn()
{
    global $pas;
    if(isset($_SESSION['login'])) $password = $_SESSION['login'];

    $pas = md5($pas);
    if(isset($password)){
        if ($password == $pas) {
            return true;
        }else{
            return false;
        }
    }
}

function admin_stat_insta($time){
    global $dbh;
    $sth = $dbh->prepare("SELECT  SUM(last_count) as count FROM `last_instagram` WHERE time_added >= DATE_SUB(NOW() , INTERVAL $time HOUR)");
    $sth->execute();
    $result = $sth->fetchObject();

    return $result;
}
function admin_stat_insta_all(){
    global $dbh;
    $sth = $dbh->prepare("SELECT  SUM(all_count) as count FROM `last_instagram`");
    $sth->execute();
    $result = $sth->fetchObject();

    return $result;
}

function display_error() {
    global $errors;

    if (count($errors) > 0){
        echo '<div class="error">';
        foreach ($errors as $error){
            echo $error .'<br>';
        }
        echo '</div>';
    }
}

function index_import(){
    global $dbh;
    $query = "SELECT * FROM `set_pars` WHERE 1";
    $stmt = $dbh->query($query);
    $row =$stmt->fetchObject();
    return $row;
}

function video_duration($filename){
    $getID3 = new getID3;
    $file = $getID3->analyze($filename);
    $duration=$file['playtime_seconds'];
    return $duration;
}

function get_file($url,$id,$name){
    /* $proxy =get_proxy();
     $proxy_ip =$proxy[0];
     $proxy_port=$proxy[1];
     $proxy_login=$proxy[2];
     $proxy_pasw=$proxy[3];
 */

    if (!file_exists('uploads/'.$id)) {
        mkdir('uploads/'.$id, 0777, true);
    }
    $link= 'uploads/'.$id.'/'.$name.'.mp4';

    $timeout = 15;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    // curl_setopt($ch, CURLOPT_PROXY, $proxy_ip.':'.$proxy_port );

    // curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_login.':'.$proxy_pasw);
    // curl_setopt($ch, CURLOPT_PROXYTYPE, CURLE_SSL_PEER_CERTIFICATE);
    $output = curl_exec($ch);
    curl_close($ch);
    if(!$output){
        echo 'proxxy error';
        //get_file($url,$id,$name);
    }
    $fh = fopen($link, 'w');
    fwrite($fh, $output);
    fclose($fh);

    return $link;
}

function last_insta_bd($name){
    global $dbh;
    $query = "SELECT * FROM `last_instagram` WHERE `group_name`='$name'";
    $stmt = $dbh->query($query);
    $row =$stmt->fetchObject();
    return $row;
}

function old_instagram(){
    global $dbh;
    $sth = $dbh->prepare("SELECT * FROM `instagrams` ");
    $sth->execute();
    $results = $sth->fetchAll();
    return $results;
}
function all_instagram(){
    global $dbh;
    $sth = $dbh->prepare("SELECT * FROM `instagrams` WHERE `status` = 1 ");
    $sth->execute();
    $results = $sth->fetchAll();
    return $results;
}
function priority_all_instagram($limit){
    global $dbh;
    $sth = $dbh->prepare("SELECT * FROM `instagrams` WHERE `status` = 1 ORDER BY ctr DESC LIMIT $limit");
    $sth->execute();
    $results = $sth->fetchAll();
    return $results;
}
function count_instagram(){
    global $dbh;
    $query = "SELECT count(*) as count_insta FROM instagrams  WHERE `status` = 1";
    $stmt = $dbh->query($query);
    $row =$stmt->fetchObject();
    return $row->count_insta;
}
function all_count_instagram(){
    global $dbh;
    $query = "SELECT count(*) as count_insta FROM instagrams ";
    $stmt = $dbh->query($query);
    $row =$stmt->fetchObject();
    return $row->count_insta;
}
function del_instagram($id){
    global $dbh;
    $status =2;
    $posting = date('Y-m-d H:i:s');
    $sql = "UPDATE instagrams SET status=:status, posting=:posting WHERE id=:id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':posting', $posting, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $results =  $stmt->execute();

    return $results;
}
function get_proxy(){
    $lines = file('proxy.txt');
    // $lines = file('proxys_166388.txt');
    $rand_keys = array_rand($lines);
    $line= str_replace("\n",'',$lines[$rand_keys]);

    $ret= explode(':',$line);
    return $ret;
}
//$instagram = Instagram::withCredentials('leonardooleg2', 'rtd4653', new Psr16Adapter('Files'));
$instagram = Instagram::withCredentials('leonardooleg3', 'rtd465321', new Psr16Adapter('Files'));
$instagram->login(); // will use cached session if you want to force login $instagram->login(true)
$instagram->saveSession();  //DO NOT forget this in order to save the session, otherwise have no sense
function insta_create(){
    $proxy =get_proxy();
    $proxy_ip =$proxy[0];
    $proxy_port=$proxy[1];
    $proxy_login=$proxy[2];
    $proxy_pasw=str_replace("\r",'', $proxy[3]);



    Instagram::disableProxy();
    Instagram::setProxy([
        'address' => $proxy_ip,
        'port'    => $proxy_port,
        'tunnel'  => true,
        'timeout' => 25,
        'type' => CURLPROXY_SOCKS5_HOSTNAME,
        'auth' => [
            'user' => $proxy_login,
            'pass' => $proxy_pasw,
            'method' => CURLAUTH_BASIC
        ],



    ]);

}

function vk_insert($instagram,$group_id,$count_vk,$need_time){
    global $access_token ;
    $image = realpath(__DIR__ . '/..') .'/uploads/' . $instagram["group_id"] . '/' . $instagram["post"] . '.mp4';

    // Получение сервера vk для загрузки изображения.
    $server = file_get_contents('https://api.vk.com/method/video.save?group_id=' . $group_id . '&access_token=' . $access_token . '&v=5.107');
    $server = json_decode($server);

    if ($server->error->error_msg == 'Access to adding post denied: you can only add 50 posts a day') {
        return false;
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
        return true;
    }
}
?>