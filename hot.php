<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2020-08-04
 * Time: 23:58
 */

require_once "vendor/autoload.php";

use Gsons\App;

App::init();
try{
    print_r(App::getHotConfig('HuYa'));
}
catch (\Exception $e){
    echo $e->getMessage();
}


//$config = [ 'HuYa' => '虎牙直播', 'DouYu' => '斗鱼直播'];
//App::spiderHot($config);