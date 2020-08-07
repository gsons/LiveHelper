<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020-08-04
 * Time: 9:53
 */

namespace Gsons\Live;


use Gsons\HttpCurl;

class HuajiaoLive extends  Live
{
    const API_LIVE_URL="https://h.huajiao.com/api/getFeedInfo?_rate=xd&stype=m3u8&sid=1596505786539.0925&liveid=%s&_=1596505786543&callback=Zepto1596505786534";

    /**
     * 直播ID
     * @param $roomId
     * @return mixed
     * @throws \ErrorException
     */
    function getLiveUrl($roomId)
    {
        $curl=new HttpCurl();
        $curl->get(sprintf(self::API_LIVE_URL,$roomId));
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }else{
            preg_match("/Zepto1596505786534\((.*)?\)/",$curl->response,$match);
            if(isset($match[1])&&$arr=json_decode($match[1],true)){
                 if(isset($arr['data']['live'])){
                     return $arr['data']['live']['main'];
                 }
            }
            throw new \ErrorException("failed to get live url {$roomId}");
        }

    }

    function getDancingRoom()
    {
        // TODO: Implement getDancingRoomId() method.
        return [];
    }

    function getTvRoom()
    {
        // TODO: Implement getAvRoomId() method.
        return [];
    }

    function getHotDanceRoom()
    {
        return [];
        // TODO: Implement getHotNumArr() method.
    }

}