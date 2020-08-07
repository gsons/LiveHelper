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

    const AV_ROOM_URL="https://share.egame.qq.com/cgi-bin/pgg_async_fcgi";

    /**
     * @param array $arr
     * @param int $page
     * @throws \ErrorException
     * @return array
     */
    private function getTvRoomList($arr=[],$page=1){
        $curl = new HttpCurl();
        $curl->setReferrer('https://egame.qq.com/livelist?layoutid=2000000110&tagId=0&tagIdStr=');
        $param=[
            'param'=>'{"key":{"module":"pgg_live_read_ifc_mt_svr","method":"get_pc_live_list","param":{"appid":"2000000110","page_num":'.$page.',"page_size":40,"tag_id":0,"tag_id_str":""}}}',
            'app_info'=>'{"platform":4,"terminal_type":2,"egame_id":"egame_official","imei":"","version_code":"9.9.9.9","version_name":"9.9.9.9","ext_info":{"_qedj_t":"","ALG-flag_type":"30","ALG-flag_pos":"1"},"pvid":"914586521620071222"}',
            'g_tk'=>373648261,
            'pgg_tk'=>1869772085,
            'tt'=> 1,
            '_t'=>1596384025292
        ];
        $curl->setOpt(CURLOPT_TIMEOUT, 10);
        $curl->get(self::AV_ROOM_URL,$param);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $data = json_decode($curl->response, true);

        $data=$data['data']['key']['retBody']['data'];
        $dataArr=array_column($data['live_data']['live_list'],'title','anchor_id');

        if(isset($data['total'])){
            $pageNum=ceil($data['total']/40);
            if($page<$pageNum){
                return $arr+$this->getTvRoomList($dataArr,$page+1);
            }else{
                return $dataArr;
            }

        }else{
            return  [];
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

    //todo 企鹅电竞暂未正在跳舞的接口
    public function getDancingRoom()
    {
        return [];
    }

    /**
     * @throws \ErrorException
     */
    public function getTvRoom(){
        return $this->getTvRoomList();
    }

    function getHotDanceRoom()
    {
        return [];
        // TODO: Implement getHotNumArr() method.
    }

}