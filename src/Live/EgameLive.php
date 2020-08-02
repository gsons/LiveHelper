<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/21
 * Time: 17:30
 */

namespace Gsons\Live;

use Gsons\HttpCurl;

class EGameLive extends Live
{
    const BASE_ROOM_URL = "https://egame.qq.com/%s";
    const SITE_NAME = "企鹅电竞";
    /**
     * @param $roomId
     * @throws \ErrorException
     * @return string
     */
    public function getLiveUrl($roomId)
    {

        $curl = new HttpCurl([], false);
        $curl->setReferrer('https://egame.qq.com');
        $roomUrl = sprintf(self::BASE_ROOM_URL, $roomId);
        $curl->get($roomUrl);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $html = $curl->response;
        preg_match("/var playerInfo = (.*?);/", $html, $match);
        if (isset($match[1])) {
            $arr = json_decode($match[1], true);
            if (isset($arr['urlArray'][0]['playUrl'])) {
                return $arr['urlArray'][0]['playUrl'];
            }
        }
        throw new \ErrorException('failed to get live url');
    }

    //todo 企鹅电竞暂未这个接口
    public function getDancingRoomId()
    {

    }

    public function getAvRoomId(){

    }

}