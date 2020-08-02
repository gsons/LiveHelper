<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2020-08-01
 * Time: 0:51
 */

namespace Gsons\lib;
use Gsons\HttpCurl;

class TvRoom
{
    const TV_URL="http://192.168.101.13:2020/savediy";

    /**
     * @param $title
     * @param $list
     * @return bool
     * @throws \ErrorException
     */
    public static function sendLiveUrl($title,$list){
        if(empty($title)||empty($list)){
            return false;
        }
        $curl = new HttpCurl();
        $urlArrStr="";
        foreach ($list as $name=>$live_url){
            $urlArrStr.="{$name},$live_url\r\n";
        }
        $param="{$title}\r\n{$urlArrStr} \r\n\\r\\n\r\n: ";
        $curl->setOpt(CURLOPT_URL, self::TV_URL);
        $curl->setOpt(CURLOPT_POSTFIELDS, $param);
        $curl->response = curl_exec($curl->curl);
        $curl->close();
        $data = json_decode($curl->response, true);
        if(isset($data['suc'])&&$data['suc']==='0'){
            return true;
        }else{
            return false;
        }
    }
}