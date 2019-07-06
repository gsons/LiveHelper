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
    public static function getLiveUrl($roomId);
    public static function getDancingRoomId();
}