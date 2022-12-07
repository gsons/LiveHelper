<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/1
 * Time: 0:50
 */

namespace Gsons\Live;

use Gsons\HttpCurl;

class HuYaLive extends Live
{
    const SITE_NAME = "虎牙直播";
    const SITE_CODE = "HuYa";
    const BASE_ROOM_URL = "https://www.huya.com/%s";
    const BASE_LIVE_URL = "https://m.huya.com/%s";
    const DANCE_ROOM_API_URL = "https://live.cdn.huya.com/livelist/game/tagLivelist?gameId=1663&tmpId=116&callback=getLiveListJsonpCallback&page=1";
    const AV_ROOM_URL = "https://www.huya.com/cache.php?m=LiveList&do=getLiveListByPage&gameId=2135&tagAll=0&page=%s";

    /**
     *
     * @return array
     * @throws \ErrorException
     */
    public function getDancingRoom()
    {
        $curl = new HttpCurl();
        $curl->setReferer('https://www.huya.com/g/xingxiu');
        $curl->setHeader('X-Requested-With','XMLHttpRequest');
        $curl->get(self::DANCE_ROOM_API_URL.'&_='.uniqid());
        preg_match("/getLiveListJsonpCallback\((.*?)\)/",$curl->response,$match);
        $curl->close();
        if(!isset($match[1])){
            return [];
        }
        $data = json_decode($match[1], true);
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
    public function getLiveUrl($roomId){
        $curl = new HttpCurl();
        $curl->setReferer('https://m.huya.com');
        $roomUrl = sprintf(self::BASE_LIVE_URL, $roomId);
        $curl->get($roomUrl);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $html = $curl->response;
        preg_match("/<script> window.HNF_GLOBAL_INIT = (.*?)<\/script>/",$html,$match);
//        file_put_contents('2.html',$match[1]);
        if (isset($match[1]) && $match[1]) {
            $arr=json_decode($match[1],true);
            $streamInfo=$arr["roomInfo"]["tLiveInfo"]["tLiveStreamInfo"]["vStreamInfo"]["value"];
            $real_url=[];
            foreach ($streamInfo as $info){
                $real_url[strtolower($info["sCdnType"]) . "_flv"] = $info["sFlvUrl"] . "/" . $info["sStreamName"] . "." . $info["sFlvUrlSuffix"] . "?" . $info["sFlvAntiCode"];
                $real_url[strtolower($info["sCdnType"]) . "_hls"] = $info["sHlsUrl"] . "/" . $info["sStreamName"] . "." . $info["sHlsUrlSuffix"] . "?" . $info["sHlsAntiCode"];
            }
            $arr=array_values($real_url);
            $str=$arr[rand(0,count($arr)-1)];
            return str_replace('&ctype=tars_mobile','',$str);
        }else{
            throw new \ErrorException("failed to get live url {$roomId}");
        }
    }

    /**
     * @param $roomId
     * @return string
     * @throws \ErrorException
     */
    public function fetchLiveUrl($roomId)
    {

        $curl = new HttpCurl();
        $curl->setReferer('https://m.huya.com');
        $roomUrl = sprintf(self::BASE_LIVE_URL, $roomId);
        $curl->get($roomUrl);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $html = $curl->response;
        preg_match("/hasvedio: '(.*?)'/", $html, $match);
        if (isset($match[1]) && $match[1]) {
            $url = 'https:' . str_replace('_1200', '', $match[1]);
//            $url='https:'.$match[1];
            exec('node js/hy_decode.js' . ' "' . $url . '"', $var);
            return $var[0];
        } else {
            throw new \ErrorException("failed to get live url {$roomId}");
        }
    }

    /**
     * @param $roomId
     * @return mixed
     * @throws \ErrorException
     */
    public function getLiveUrlArr($roomId)
    {
        $curl = new HttpCurl([], false);
        $curl->setReferer('https://www.huya.com');
        $roomUrl = sprintf(self::BASE_ROOM_URL, $roomId);
        $curl->get($roomUrl);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $html = $curl->response;
        preg_match("/hyPlayerConfig = ([^;]+);/", $html, $match);
        if (isset($match[1]) && $match[1]) {
            $arr = json_decode($match[1], true);
            $stream = json_decode(base64_decode($arr['stream']), true);
            $urlList = $stream['data'][0]['gameStreamInfoList'];
            $rateList = $stream['vMultiStreamInfo'];
            $liveList = [];
            for ($i = 0; $i < count($urlList); $i++) {
                for ($j = 0; $j < count($rateList); $j++) {
                    $iBitRate = $rateList[$j]['iBitRate'];
                    $iBitRate = $iBitRate ? "_{$iBitRate}" : '';
//                    print_r($urlList);exit;//sFlvAntiCode
                    $url = $urlList[$i]['sFlvUrl'] . '/' . $urlList[$i]['sStreamName'] . $iBitRate . '.' . $urlList[$i]['sFlvUrlSuffix'] . '?' . $urlList[$i]['newCFlvAntiCode'];
                    $item = [
                        'quality' => $rateList[$j]['sDisplayName'],
                        'lineIndex' => $urlList[$i]['iLineIndex'],
                        'liveUrl' => htmlspecialchars_decode($url)
                    ];
                    $liveList[] = $item;
                }
            }
            return $liveList;
        } else {
            throw new \ErrorException("failed to get live url {$roomId}");
        }
    }


    /**
     * @param array $arr
     * @param int $page
     * @return array
     * @throws \ErrorException
     */
    private function getTvRoomList($arr = [], $page = 1)
    {
        $curl = new HttpCurl();
        $curl->setReferer('https://m.huya.com');
        $roomUrl = sprintf(self::AV_ROOM_URL, $page);
        $curl->setOpt(CURLOPT_TIMEOUT, 10);
        $curl->get($roomUrl);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $data = json_decode($curl->response, true);
        $dataArr = $data['data']['datas'];
        $dataArr = array_column($dataArr, 'introduction', 'profileRoom');
        if (isset($data['data']['totalPage'])) {
            if ($page < $data['data']['totalPage']) {
                return $arr + $this->getTvRoomList($dataArr, $page + 1);
            } else {
                return $dataArr;
            }

        } else {
            return [];
        }
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getTvRoom()
    {
        return $this->getTvRoomList();
    }

    /**
     * @throws \ErrorException
     */
    public function getHotDanceRoom()
    {
        $curl = new HttpCurl();
        $curl->setReferer('https://www.huya.com/g/xingxiu');
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
                $time = time();
                $arr[] = [
                    'site_name' => self::SITE_NAME,
                    'site_code' => self::SITE_CODE,
                    'nick_name' => $vo['nick'],
                    'room_id' => $vo['profileRoom'],
                    'room_url' => sprintf(self::BASE_ROOM_URL, $vo['profileRoom']),
                    'record_date' => date('Y-m-d H:i:s', $time),
                    'record_time' => $time,
                    'hot_num' => $vo['totalCount']
                ];
            }

        }
        return $arr;
    }
}
