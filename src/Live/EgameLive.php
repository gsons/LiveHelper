<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/21
 * Time: 17:30
 */

namespace Gsons\Live;
use Gsons\HttpCurl;

class EGameLive extends Live implements Api
{
    const BASE_ROOM_URL = "https://www.huya.com/%s";

    /**
     * @param $roomId
     * @throws \ErrorException
     * @return string
     */
    public static function getLiveUrl($roomId){
        $curl = new HttpCurl([],false);
//        $curl->setReferrer('https://egame.qq.com');
        $roomUrl = sprintf(self::BASE_ROOM_URL, $roomId);
        $curl->get($roomUrl);
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $html=$curl->response;
        print_r([$roomUrl,$html]);exit;
        preg_match("/_playerInfo = ({.+?});/",$html,$match);

        if(isset($match[0])){
            $arr=json_decode($match[0],true);
            return $arr;
        }else{
            throw new \ErrorException('failed to get live url');
        }
    }

    //todo 企鹅电竞暂未这个接口
    public static function getDancingRoomId(){

    }


}