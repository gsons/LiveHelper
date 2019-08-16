<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/4
 * Time: 4:02
 */

namespace Gsons\Live;

use Gsons\HttpCurl;

class DouYuLive extends Live implements Api
{
    const SITE_NAME = "斗鱼直播";
    const BASE_ROOM_URL = "https://www.douyu.com/%s";
    //https://www.douyu.com/gapi/rkc/directory/3_1122/1
    const DANCE_ROOM_API_URL = "https://www.douyu.com/gapi/rknc/directory/yzRec/1";

    //
    public static function getLiveUrl($roomId)
    {

    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public static function getDancingRoomId()
    {
        $curl = new HttpCurl();
        $curl->setReferrer('https://www.douyu.com/g_yz');
        $curl->get(self::DANCE_ROOM_API_URL);
        $data = json_decode($curl->response, true);
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $arr = [];
        if (isset($data['data']['rl']) && !empty($data['data']['rl'])) {
            $list = $data['data']['rl'];
            foreach ($list as $vo) {
                if (isset($vo['icv1'][1][0]['id']) && $vo['icv1'][1][0]['id'] == '656' || isset($vo['icv1'][1][0]['id']) && $vo['icv1'][1][0]['id'] == '655') {
                    $arr[] = ['roomId' => $vo['rid'], 'nickName' => $vo['nn']];
                }
            }
            $arr = array_column($arr, 'nickName', 'roomId');
        }
        unset($data);
        return $arr;
    }

}