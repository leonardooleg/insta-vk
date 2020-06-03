<?php
include('config/functions.php');
include_once('layout/head.php');
if (!isLoggedIn()) {
    echo file_get_contents('views/login.html');
}else{

    header("location: admin.php");
    session_destroy();
    exit;

}


include_once('layout/footer.php');