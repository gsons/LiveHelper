﻿﻿<?php
require_once "vendor/autoload.php";

use Gsons\App;

//需要监听的房间
$config = [
    "HuYa" => [
        // 995713 => 'CICI',
        // 249648 => "全素妍",
        139236 => '瑶池-虞姿',
        317279=>'xx',
        // 22499481 => 'AzZ丶猫崽崽三分糖',
        516171=>'wangxinen',
        12150 =>'kongkong',
        //243680=>"华星-刘钞钞"
        // 920857 => '朴允烟',
        // 20214983 => '众乐-牛宝宝',
        // 539127 => 'MH、侑贤',
        // 612916=>'长腿兮兮',
        // 137045 => '音动-蓉儿',
        // 596593=>'盛鸽-楚楚',
        // 860177247=>'王雨霏',
        // 17106609=>'小师妹',
        // 520131=>'泡芙'
    ],
    "Egame" => [
        619364874 => '菇七七',
        240790603 => 'Qn梦琳'
    ],
    "CC" => [
        347831124 => "七秒",
        347946388 => "彤彤",
        346509863 => "小静静",
        347851457 => "小可Angel的"
    ],
    "DouYu" => [
        //6509544 => '小Q呀I',
        7130628 => "由悠",
        1360423 => "seven悠悠",
        968987 => "南妹儿",
        10865197=>'xiaoqian',
        //5153172 => "大慕慕吖",
        // 2273214 => "林66呐",
        // 7339082 => "浙江小志玲",
        // 6584315 => "柒柒Bb",
        // 7244437 => "向榆o",
        //7746333 => "奶优米呀"
    ],
    "YY" => [
        95825545 => "漫漫",
        41064110 => "心儿",
        1353213733 => "宠儿",
        1330802494 => "晓晓",
        22490906 => '小白白',
        26713667 => '嫣嫣'

    ],
    "Inke" => [
        704386978 => "诺诺",
        714172814 => "韩静儿",
        10284346 => "MMMMM",
        722035883 => "语嫣",
        43873157 => '小师妹',
        715471317 => '临兮兮',
        919084011 => '祖儿',
        188214787 => '白菜'
    ]
];

$record_path = "./video";
//初始化配置
App::init();
//开始监听录制
App::run($config, true, false, $record_path);
