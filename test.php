<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2020-07-31
 * Time: 23:57
 */

require_once "vendor/autoload.php";
date_default_timezone_set("PRC");
$huya = new \Gsons\Live\HuYaLive();
$roomIdArr = $huya->getAvRoomId();
$list = [];
$count = 0;
foreach ($roomIdArr as $roomId => $name) {
    $liveUrl =$huya->getLiveUrl($roomId);
    $list[$name]=$liveUrl;
    echo PHP_EOL.$name.':'.$liveUrl.PHP_EOL;
    $count++;
    if ($count > 50) break;
}

\Gsons\lib\TvRoom::sendLiveUrl('一起看', $list);
//for(;;){
//    \Gsons\lib\TvRoom::sendLiveUrl('一起看', $list);
//    sleep(10);
//}


