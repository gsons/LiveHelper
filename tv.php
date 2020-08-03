<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2020-07-31
 * Time: 23:57
 */

require_once "vendor/autoload.php";

use think\Cache;
use Gsons\lib\TvRoom;

date_default_timezone_set("PRC");
\Gsons\lib\Error::register();

Cache::init([
    'type' => 'File',
    'path' => './cache/',
    'prefix' => '',
    'expire' => 0,
]);


$config = ['YY' => 'YY直播', 'HuYa' => '虎牙直播', 'DouYu' => '斗鱼直播', 'Egame' => '企鹅电竞'];

$temp = Cache::get('TV_LIVE_URL');
if ($temp) {
    $listArr = $temp;
} else {
    $listArr = TvRoom::getLiveArr($config);
    Cache::set('TV_LIVE_URL', $listArr);
}

try {
    $res = TvRoom::sendLiveUrl($listArr);
} catch (\ErrorException $e) {
    echo $e->getMessage() . PHP_EOL;
    $res = false;
}
echo $res ? '发送成功' : '发送失败';
