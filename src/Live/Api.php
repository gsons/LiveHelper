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
    public function getLiveUrl($roomId);
    public function getDancingRoomId();
}