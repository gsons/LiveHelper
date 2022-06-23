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


$hu=new \Gsons\Live\CCLive();
file_put_contents('1.html',$hu->getLiveUrl('416399818'));
exit;
$config = ['CC' => 'CC直播', 'YY' => 'YY直播', 'HuYa' => '虎牙直播', 'DouYu' => '斗鱼直播', 'Egame' => '企鹅电竞'];
$temp = Cache::get('TV_LIVE_URL');
if ($temp) {
    $listArr = $temp;
} else {
    $listArr = TvRoom::getLiveArr($config);
    Cache::set('TV_LIVE_URL', $listArr);
}
try {
    $res = TvRoom::sendLiveUrl($listArr);
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    $res = false;
}
echo $res ? '发送成功!' : '发送失败!';
