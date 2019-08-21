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
    const DANCE_ROOM_API_URL = "https://www.douyu.com/gapi/rkc/directory/2_1008/1";
    const ENC_URL = "https://www.douyu.com/swf_api/homeH5Enc?rids=%s";
    const GET_LIVE_URL="https://www.douyu.com/lapi/live/getH5Play/%s";


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

    private static function getRandomName($len = 6)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
        return $str;
    }


    private static function replaceTpl($names_dict, $tpl)
    {
        return str_replace(array_keys($names_dict), array_values($names_dict), $tpl);
    }


    /**
     * @param $roomId
     * @throws \ErrorException
     * @return string
     */
    public static function getLiveUrl($roomId)
    {
        $names_dict = [
            '{debugMessages}' => self::getRandomName(8),
            '{decryptedCodes}' => self::getRandomName(8),
            '{resoult}' => self::getRandomName(8),
            '{_ub98484234}' => self::getRandomName(8),
            '{workflow}' => 'str',
        ];


        $js_dom = <<<EOF
{debugMessages} = {{decryptedCodes}: []};
if (!this.window) {window = {};}
if (!this.document) {document = {};}
EOF;
        $js_patch = <<<EOF
        {debugMessages}.{decryptedCodes}.push({workflow});
        var patchCode = function(workflow) {
            var testVari = /(\w+)=(\w+)\([\w\+]+\);.*?(\w+)="\w+";/.exec(workflow);
            if (testVari && testVari[1] == testVari[2]) {
                {workflow} += testVari[1] + "[" + testVari[3] + "] = function() {return true;};";
            }
        };
        patchCode({workflow});
        var subWorkflow = /(?:\w+=)?eval\((\w+)\)/.exec({workflow});
        if (subWorkflow) {
            var subPatch = (
                "{debugMessages}.{decryptedCodes}.push('sub workflow: ' + subWorkflow);" +
                "patchCode(subWorkflow);"
            ).replace(/subWorkflow/g, subWorkflow[1]) + subWorkflow[0];
            {workflow} = {workflow}.replace(subWorkflow[0], subPatch);
        }
        eval({workflow});
EOF;
        $js_debug = <<<EOF
        var {_ub98484234} = ub98484234;
        ub98484234 = function(p1, p2, p3) {
            try {
                var resoult = {_ub98484234}(p1, p2, p3);
                {debugMessages}.{resoult} = resoult;
            } catch(e) {
                {debugMessages}.{resoult} = e.message;
            }
            return {debugMessages};
        };
EOF;
        $did=self::getRandomName(10);
        $tt=time();
        $js_call ="var res=ub98484234({$roomId},'{$did}',{$tt});res=JSON.stringify(res);console.log(res);";
        $js_md5 =file_get_contents("./js/crypto-js-md5.min.js");
        $search = "eval({workflow});";
        $js_dom = self::replaceTpl($names_dict, $js_dom);
        $js_patch = self::replaceTpl($names_dict, $js_patch);
        $js_debug = self::replaceTpl($names_dict, $js_debug);
        $search = self::replaceTpl($names_dict, $search);

        $curl = new HttpCurl();
        $curl->setReferrer('https://www.douyu.com');
        $apiUrl = sprintf(self::ENC_URL, $roomId);
        $curl->get($apiUrl);
        $data = json_decode($curl->response, true);

        if ($curl->error) {
            throw new \ErrorException($apiUrl.'=>'.$curl->error_message);
        }
        if (isset($data['data']["room{$roomId}"])) {
            $js_enc = str_replace($search, $js_patch, $data['data']["room{$roomId}"]);
            file_put_contents('js/temp.js', $js_md5.PHP_EOL.$js_dom . PHP_EOL . $js_enc . PHP_EOL . $js_debug . PHP_EOL . $js_call);
            exec('node js/temp.js', $var);
            $jsonStr=join('',$var);
            $arr=json_decode($jsonStr,true);
            $key=$names_dict['{resoult}'];

            if(isset($arr[$key])){
                /**
                 * @var $v
                 * @var $sign
                 */
                parse_str($arr[$key]);
                $param=[
                    'v'=>$v,
                    'did'=>$did,
                    'tt'=>$tt,
                    'sign'=>$sign,
                    'cdn'=>'',
                    'iar'=>0,
                    'ive'=>0
                ];
                $liveUrl=sprintf(self::GET_LIVE_URL,$roomId);
                $res=$curl->post($liveUrl,$param);
                if ($curl->error) {
                    throw new \ErrorException($liveUrl.'=>'.$curl->error_message);
                }
                $arr=json_decode($res->response,true);
                return $arr['data']['rtmp_url'].'/'.$arr['data']['rtmp_live'];
            }
        }
        throw new \ErrorException("API error");
    }
}