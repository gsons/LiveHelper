<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/1
 * Time: 0:50
 */

namespace Gsons\Live;

use Gsons\HttpCurl;

class HuYaLive extends Live implements Api
{
    const SITE_NAME = "虎牙直播";
    const BASE_ROOM_URL = "https://www.huya.com/%s";
    const BASE_LIVE_URL = "https://m.huya.com/%s";
    const DANCE_ROOM_API_URL = "https://www.huya.com/cache.php?m=LiveList&do=getTmpLiveByPage&gameId=1663&tmpId=116&page=1";

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getDancingRoomId()
    {
        $curl = new HttpCurl();
        $curl->setReferrer('https://www.huya.com/g/xingxiu');
        $curl->get(self::DANCE_ROOM_API_URL);
        $data = json_decode($curl->response, true);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $arr = [];
        if (isset($data['data']['datas']) && !empty($data['data']['datas'])) {
            $list = $data['data']['datas'];
            foreach ($list as $vo) {
                if (isset($vo['imgRecInfo']['type']) && $vo['imgRecInfo']['type'] == 'isDancing') {
                    $arr[] = ['roomId' => $vo['profileRoom'], 'nickName' => $vo['nick']];
                }
            }
            $arr = array_column($arr, 'nickName', 'roomId');
        }
        unset($data);
        return $arr;

    }


    /**
     * @param $roomId
     * @return mixed
     * @throws \ErrorException
     */
    public function getLiveUrl($roomId)
    {
        $curl = new HttpCurl();
        $curl->setReferrer('https://m.huya.com');
        $roomUrl = sprintf(self::BASE_LIVE_URL, $roomId);
        $curl->get($roomUrl);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $html = $curl->response;
        preg_match("/hasvedio: \'(.*?)\'/", $html, $match);
        if (isset($match[1]) && $match[1]) {
            return str_replace('_1200', '', $match[1]);
        } else {
            throw new \ErrorException("maybe not exist the roomId {$roomId}");
        }
    }

}