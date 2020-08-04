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
    const BASE_ROOM_URL = "https://www.huya.com/%s";
    const BASE_LIVE_URL = "https://m.huya.com/%s";
    const DANCE_ROOM_API_URL = "https://www.huya.com/cache.php?m=LiveList&do=getTmpLiveByPage&gameId=1663&tmpId=116&page=1";
    const AV_ROOM_URL="https://www.huya.com/cache.php?m=LiveList&do=getLiveListByPage&gameId=2135&tagAll=0&page=%s";
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
     * @return string
     * @throws \ErrorException
     */
    public function getLiveUrl($roomId){

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
           // $url= 'https:'.str_replace('_1200', '', $match[1]);
            $url='https:'.$match[1];
            exec('node js/hy_decode.js'.' "'.$url.'"', $var);
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
        $curl = new HttpCurl([],false);
        $curl->setReferrer('https://www.huya.com');
        $roomUrl = sprintf(self::BASE_ROOM_URL, $roomId);
        $curl->get($roomUrl);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $html = $curl->response;
        preg_match("/hyPlayerConfig = ([^;]+);/", $html, $match);
        if (isset($match[1]) && $match[1]) {
            $arr=json_decode($match[1],true);
            $stream=json_decode(base64_decode($arr['stream']),true);
            $urlList = $stream['data'][0]['gameStreamInfoList'];
            $rateList = $stream['vMultiStreamInfo'];
            $liveList = [];
            for ($i = 0; $i < count($urlList); $i++) {
                for ($j = 0; $j < count($rateList); $j++) {
                    $iBitRate = $rateList[$j]['iBitRate'];
                    $iBitRate = $iBitRate ? "_{$iBitRate}" : '';
//                    print_r($urlList);exit;//sFlvAntiCode
                    $url = $urlList[$i]['sFlvUrl'] .'/' .$urlList[$i]['sStreamName'] . $iBitRate . '.' . $urlList[$i]['sFlvUrlSuffix'] . '?' . $urlList[$i]['newCFlvAntiCode'];
                    $item = [
                        'quality'=>$rateList[$j]['sDisplayName'],
                        'lineIndex'=> $urlList[$i]['iLineIndex'],
                        'liveUrl'=> htmlspecialchars_decode($url)
                    ];
                    $liveList[]=$item;
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
     * @throws \ErrorException
     * @return array
     */
    private function getAvRoomIdList($arr=[],$page=1){
        $curl = new HttpCurl();
        $curl->setReferrer('https://m.huya.com');
        $roomUrl = sprintf(self::AV_ROOM_URL, $page);
        $curl->setOpt(CURLOPT_TIMEOUT, 10);
        $curl->get($roomUrl);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $data = json_decode($curl->response, true);
        $dataArr=$data['data']['datas'];
        $dataArr=array_column($dataArr,'introduction','profileRoom');
        if(isset($data['data']['totalPage'])){
            if($page<$data['data']['totalPage']){
                return $arr+$this->getAvRoomIdList($dataArr,$page+1);
            }else{
                return $dataArr;
            }

        }else{
            return  [];
        }
    }

    /**
     * @throws \ErrorException
     * @return array
     */
    public function getAvRoomId(){
        return $this->getAvRoomIdList();
    }
}