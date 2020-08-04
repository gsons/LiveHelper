<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2020-08-04
 * Time: 23:58
 */

require_once "vendor/autoload.php";

$presss=\Gsons\Live\Live::record("http://qh1-hls.live.huajiao.com/live_huajiao_v2/_LC_QH1_non_h265_SD_21493637915965528471617280_OX/index.m3u8",'./video','3.mp4',10,true);
while (1){
    sleep(2);
    $res = proc_get_status($presss);
     var_dump($res['running']);
}