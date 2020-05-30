<?php
session_start();
// connect to database

include_once "conf.php";


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
    $password = $_SESSION['login'];
    $pas = md5($pas);
    if ($password == $pas) {
        return true;
    }else{
        return false;
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

function get_file($url,$id,$name){
    global $loginpassw;
    global $proxy_ip ;
    global $proxy_port;


    if (!file_exists('uploads/'.$id)) {
        mkdir('uploads/'.$id, 0777, true);
    }
    $link= 'uploads/'.$id.'/'.$name.'.mp4';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
    curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTPS');
    curl_setopt($ch, CURLOPT_PROXY, $proxy_ip);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $loginpassw);
    $output = curl_exec($ch);
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

function del_instagram($id){
    global $dbh;
    $sth = $dbh->prepare("DELETE FROM `instagrams` WHERE id = $id");
    $sth->execute();
    $results = $sth->execute();
    return $results;
}

?>