<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020-08-04
 * Time: 9:53
 */

namespace Gsons\Live;


use Gsons\HttpCurl;

class HuajiaoLive extends Live
{
    const SITE_NAME="花椒直播";const  SITE_CODE="Huajiao";
    const BASE_ROOM_URL="https://www.huajiao.com/l/%s";
    const API_LIVE_URL = "https://h.huajiao.com/api/getFeedInfo?_rate=xd&stype=m3u8&sid=1596505786539.0925&liveid=%s&_=1596505786543&callback=Zepto1596505786534";
    const DANCE_ROOM_URL = "https://webh.huajiao.com/live/listcategory";

    /**
     * 直播ID
     * @param $roomId
     * @return mixed
     * @throws \ErrorException
     */
    function getLiveUrl($roomId)
    {
        $curl = new HttpCurl();
        $curl->get(sprintf(self::API_LIVE_URL, $roomId));
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        } else {
            preg_match("/Zepto1596505786534\((.*)?\)/", $curl->response, $match);
            if (isset($match[1]) && $arr = json_decode($match[1], true)) {
                if (isset($arr['data']['live'])) {
                    return $arr['data']['live']['main'];
                }
            }
            throw new \ErrorException("failed to get live url {$roomId}");
        }

    }

    function getDancingRoom()
    {
        // TODO: 花椒正在跳舞
        return [];
    }

    function getTvRoom()
    {
        // TODO: 花椒没有一起看
        return [];
    }

    /**\
     * @return array|mixed
     * @throws \ErrorException
     */
    function getHotDanceRoom()
    {
        $live_list=$this->getDanceRoomArr();
        $list=[];
        foreach ($live_list as $vo) {
            $time = time();
            $list[] = [
                'site_name' => self::SITE_NAME,
                'site_code' => self::SITE_CODE,
                'nick_name' => $vo['author']['nickname'],
                'room_id' => $vo['feed']['relateid'],
                'room_url' => sprintf(self::BASE_ROOM_URL,  $vo['feed']['relateid']),
                'record_date' => date('Y-m-d H:i:s', $time),
                'record_time' => $time,
                'hot_num' => $vo['feed']['watches']
            ];
        }
        return $list;
    }

    /**
     * @param $page
     * @throws \ErrorException
     * @return array
     */
    private function getDanceRoomArr($page=1)
    {
        $curl = new HttpCurl();
        $curl->setReferrer('https://www.huajiao.com/category/801');
        $param = [
            'cateid' => 801,
            'offset' => ($page - 1) * 99,
            'nums' => 99,
            '_' => time()
        ];
        $curl->setOpt(CURLOPT_TIMEOUT, 10);
        $curl->get(self::DANCE_ROOM_URL, $param);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $data = json_decode($curl->response, true);
        $data = $data['data'];
        if ($data['more'] == 1&&$page<2) {
            return array_merge($data['feeds'] ,$this->getDanceRoomArr($page + 1));
        } else {
            return $data['feeds'];
        }
    }
}