﻿<?php
require_once "vendor/autoload.php";

use Gsons\App;
date_default_timezone_set("PRC");
//需要监听的房间
$config = [
    "HuYa" => [
        // 177330 => '环星M-喵小柒II',
        // 520006 => '依然是婷er',
        // 274748 => 'RD清妍',
        // 16704887 => '万古-韩六六',
        // 500911 => '血色-小野',
        // 450193 => '话社-排骨',
        // 126438 => '六感JX-熊萝莉',
        // 191327 => '小西瓜',
        // 517518 => 'MY-车老板',
        // 15456523 => '波多野结盒',
        // 937430 => "苑苑",
        // 15396023 => "zy大娜娜",
        // 609677 => "妍颜",
        // 628718 => "二狗",
        // 756504 => "佐佑",
        // 16024402 => "小考拉",
        // 820297 => "Z-Miko",
        // 571358 => "孙艺嫣",
        821511 => "QL-安妮",
        19394437 => "ST-薇薇安",
        // 13671194 => "崔欧尼",
        // 546169 => "烟儿",
        // 519840 => "DV-Timi"
    ],
    // "Egame"=>[
    //     619364874=>'菇七七'
    // ],
    "CC" => [
        347831124 => "七秒",
        // 347946388 => "彤彤",
        // 346509863 => "小静静",
        // 347851457 => "小可Angel的",
        // 346950429 => "小棱子"
    ],
    "DouYu" => [
        7130628 => "由悠",
        1360423 => "seven悠悠",
        // 5469810 => "乔妹",
        // 6653695 => "福茶",
        // 7126038 => "张慕言",
        // 968987 => "南妹儿",
        // 5818349 => '米儿',
        // 4632993 => '小深深儿'
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
        43873157 => '小师妹'
    ]
];

$record_path = "./video";
App::run($config, true, false,$record_path);
