<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/1
 * Time: 0:50
 */

namespace Gsons\Live;

use Curl\Curl;

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
    public static function getDancingRoomId()
    {
        $curl = new Curl();
        $curl->setUserAgent('user-agent", "Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.22 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1');
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setReferrer('https://www.huya.com/g/xingxiu');
        $curl->setHeader('X-Requested-With', 'XMLHttpRequest');
        $curl->get(self::DANCE_ROOM_API_URL);
        $data = json_decode($curl->response, true);
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
    public static function getLiveUrl($roomId)
    {
        $curl = new Curl();
        $curl->setUserAgent('user-agent", "Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.22 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1');
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setReferrer('https://m.huya.com');
        $roomUrl = sprintf(self::BASE_LIVE_URL, $roomId);
        $curl->get($roomUrl);
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