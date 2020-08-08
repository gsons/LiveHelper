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
    const SITE_NAME = "企鹅电竞";const SITE_CODE="Egame";

    const AV_ROOM_URL = "https://share.egame.qq.com/cgi-bin/pgg_async_fcgi";

    const API_DANCE_ROOM_URL = "https://share.egame.qq.com/cgi-bin/pgg_async_fcgi";

    /**
     * @param array $arr
     * @param int $page
     * @throws \ErrorException
     * @return array
     */
    private function getTvRoomList($arr = [], $page = 1)
    {
        $curl = new HttpCurl();
        $curl->setReferrer('https://egame.qq.com/livelist?layoutid=2000000110&tagId=0&tagIdStr=');
        $param = [
            'param' => '{"key":{"module":"pgg_live_read_ifc_mt_svr","method":"get_pc_live_list","param":{"appid":"2000000110","page_num":' . $page . ',"page_size":40,"tag_id":0,"tag_id_str":""}}}',
            'app_info' => '{"platform":4,"terminal_type":2,"egame_id":"egame_official","imei":"","version_code":"9.9.9.9","version_name":"9.9.9.9","ext_info":{"_qedj_t":"","ALG-flag_type":"30","ALG-flag_pos":"1"},"pvid":"914586521620071222"}',
            'g_tk' => 373648261,
            'pgg_tk' => 1869772085,
            'tt' => 1,
            '_t' => 1596384025292
        ];
        $curl->setOpt(CURLOPT_TIMEOUT, 10);
        $curl->get(self::AV_ROOM_URL, $param);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $data = json_decode($curl->response, true);

        $data = $data['data']['key']['retBody']['data'];
        $dataArr = array_column($data['live_data']['live_list'], 'title', 'anchor_id');

        if (isset($data['total'])) {
            $pageNum = ceil($data['total'] / 40);
            if ($page < $pageNum) {
                return $arr + $this->getTvRoomList($dataArr, $page + 1);
            } else {
                return $dataArr;
            }

        } else {
            return [];
        }
    }


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

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getDancingRoom()
    {
        $live_list = $this->getDanceRoom();
        $list = [];
        foreach ($live_list as $vo) {
            if ($vo['tag'] == '热舞中') {
                $list[] = $vo;
            }
        }
        return array_column($list, 'anchor_name', 'anchor_id');
    }


    /**
     * @return array
     * @throws \ErrorException
     */
    private function getDanceRoom()
    {
        $curl = new HttpCurl();
        $curl->setReferrer('https://egame.qq.com/livelist?layoutid=2000000110&tagId=0&tagIdStr=');
        $param = [
            'param' => '{"key":{"module":"pgg_live_read_ifc_mt_svr","method":"get_pc_live_list","param":{"appid":"2000000157","page_num":1,"page_size":40,"tag_id":1735,"tag_id_str":""}}}',
            'app_info' => '{"platform":4,"terminal_type":2,"egame_id":"egame_official","imei":"","version_code":"9.9.9.9","version_name":"9.9.9.9","ext_info":{"_qedj_t":"","ALG-flag_type":"","ALG-flag_pos":""},"pvid":"654678323219092113"}',
            'g_tk' => '',
            'pgg_tk' => '',
            'tt' => 1,
            '_t' => 1596384025292
        ];
        $curl->setOpt(CURLOPT_TIMEOUT, 10);
        $curl->get(self::API_DANCE_ROOM_URL, $param);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $data = json_decode($curl->response, true);

        if (isset($data['data']['key']['retBody']['data']['live_data']['live_list'])) {
            return $data['data']['key']['retBody']['data']['live_data']['live_list'];
        } else {
            return [];
        }
    }

    /**
     * @throws \ErrorException
     */
    public function getTvRoom()
    {
        return $this->getTvRoomList();
    }

    /**
     * @return array|mixed
     * @throws \ErrorException
     */
    function getHotDanceRoom()
    {
        $live_list = $this->getDanceRoom();
        $list = [];
        foreach ($live_list as $vo) {
            $time = time();
            $list[] = [
                'site_name' => self::SITE_NAME,
                'site_code' => self::SITE_CODE,
                'nick_name' => $vo['anchor_name'],
                'room_id' => $vo['anchor_id'],
                'room_url' => sprintf(self::BASE_ROOM_URL, $vo['anchor_id']),
                'record_date' => date('Y-m-d H:i:s', $time),
                'record_time' => $time,
                'hot_num' => $vo['online']
            ];
        }
        return $list;
    }
}