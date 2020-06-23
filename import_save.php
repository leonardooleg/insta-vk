<?php
require  'vendor/autoload.php';

include('config/functions.php');


if (!isLoggedIn()) {
    header("location: login.php");
    session_destroy();
    exit;
}else{
    /*Збереження до бази посилань*/
    $links_insta=str_replace("\r\n", ";",$_POST['links_insta']);
    $links_vk=str_replace("\r\n", ";",$_POST['links_vk']);
    $priority_links_vk=str_replace("\r\n", ";",$_POST['priority_links_vk']);
    $status=$_POST['status'];
    $count_day=$_POST['count_day'];
    $priority_count_day=$_POST['priority_count_day'];
    $crt=$_POST['crt'];
    $priority_crt=$_POST['priority_crt'];
    $max_crt=$_POST['max_crt'];
    $id=1;
    $sql = "UPDATE set_pars SET links_insta=:links_insta, links_vk=:links_vk, priority_links_vk=:priority_links_vk, crt=:crt, priority_crt=:priority_crt, max_crt=:max_crt, count_day=:count_day,priority_count_day=:priority_count_day, status=:status WHERE id=:id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':links_insta', $links_insta, PDO::PARAM_STR);
    $stmt->bindParam(':links_vk', $links_vk, PDO::PARAM_STR);
    $stmt->bindParam(':priority_links_vk', $priority_links_vk, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':count_day', $count_day, PDO::PARAM_INT);
    $stmt->bindParam(':priority_count_day', $priority_count_day, PDO::PARAM_INT);
    $stmt->bindParam(':crt', $crt, PDO::PARAM_STR);
    $stmt->bindParam(':priority_crt', $priority_crt, PDO::PARAM_STR);
    $stmt->bindParam(':max_crt', $max_crt, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    /*Збереження до бази посилань*/
    $arr = $stmt->errorInfo();
    //print_r($arr);


    header("location: import.php");











}


