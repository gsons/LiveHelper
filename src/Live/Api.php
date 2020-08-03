<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/1
 * Time: 1:07
 */

namespace Gsons\Live;

interface Api
{
    //获取直播源地址
    public function getLiveUrl($roomId);

    //获取正在跳舞直播间
    public function getDancingRoomId();

    //获取一起看直播间
    public function getAvRoomId();
}