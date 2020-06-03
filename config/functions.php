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
   // curl_setopt($ch, CURLOPT_PROXY, $proxy_ip.':'.$proxy_port );
   // curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_login.':'.$proxy_pasw);
   // curl_setopt($ch, CURLOPT_PROXYTYPE, CURLE_SSL_PEER_CERTIFICATE);
    $output = curl_exec($ch);
    curl_close($ch);
    if(!$output){
        echo ' proxxy error ';
        get_file($url,$id,$name);
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


function all_instagram(){
    global $dbh;
    $sth = $dbh->prepare("SELECT * FROM `instagrams`");
    $sth->execute();
    $results = $sth->fetchAll();
    return $results;
}
function count_instagram(){
    global $dbh;
    $query = "SELECT count(*) as count_insta FROM instagrams ";
    $stmt = $dbh->query($query);
    $row =$stmt->fetchObject();
    return $row->count_insta;
}
function del_instagram($id){
    global $dbh;
    $sth = $dbh->prepare("DELETE FROM `instagrams` WHERE id = $id");
    $sth->execute();
    $results = $sth->execute();
    return $results;
}
function get_proxy(){
    $lines = file('proxys_166388.txt');
   $rand_keys = array_rand($lines);
   $line= str_replace("\n",'',$lines[$rand_keys]);

    $ret= explode(':',$line);
    return $ret;
}
$instagram = Instagram::withCredentials('leonardooleg2', 'rtd4653', new Psr16Adapter('Files'));
$instagram->login(); // will use cached session if you want to force login $instagram->login(true)
$instagram->saveSession();  //DO NOT forget this in order to save the session, otherwise have no sense
function insta_create(){
    global $instagram;
    $proxy =get_proxy();
    $proxy_ip =$proxy[0];
    $proxy_port=$proxy[1];
    $proxy_login=$proxy[2];
    $proxy_pasw=$proxy[3];



    Instagram::disableProxy();
    Instagram::setProxy([
        'address' => $proxy_ip,
        'port'    => $proxy_port,
        'tunnel'  => true,
        'timeout' => 15,
        'type' => CURLE_SSL_PEER_CERTIFICATE,
        'auth' => [
            'user' => $proxy_login,
            'pass' => $proxy_pasw,
            'method' => CURLAUTH_BASIC
        ],


    ]);

}
?>