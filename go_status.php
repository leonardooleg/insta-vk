<?php

$status_api='go_status.txt';
$offset=file_get_contents($status_api);
$arr=explode(';',$offset);
$offset=$arr[0];
$link=$arr[1];
$finish=$arr[2];
$type=$arr[3];

if ($finish == 'finish') {
    $sucsess = 1;
    // file_put_contents($status_api, '0');
} else {
    $sucsess = 0;
}

$output = Array('offset' => $offset, 'sucsess' => $sucsess, 'link' =>$link, 'type' =>$type);
echo json_encode($output);
