<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/5
 * Time: 3:02
 */

namespace Gsons\Live;

use Curl\Curl;

class InkeLive
{

    const SITE_NAME = "映客直播";
    const BASE_ROOM_URL = "http://inke.cn/live.html?uid=%s";
    const BASE_LIVE_URL = "http://inke.cn/live.html?uid=%s";
    const DANCE_ROOM_API_URL = "https://service.inke.cn/api/live/theme_card_recommend?cc=TG36008&source_info=eyJhcHBpZCI6IjEwMDAwIiwidWlkIjoiNzI1OTY4MDcyIiwicGFnZSI6ImNvbS5tZWVsaXZlLmlu%0AZ2tlZS5idXNpbmVzcy5yb29tLnVpLmFjdGl2aXR5LlJvb21BY3Rpdml0eSIsInRpbWUiOiIxNTYy%0AMjY2NTQ0MTY2In0%3D%0A&lc=36058bb8fca98d7e&mtxid=bc5ff6cfcd78&cpu=%5BAdreno_%28TM%29_506%5D%5BAArch64_638_Qualcomm_Technologies%2C_Inc_MSM8953%5D&devi=868415033406092&sid=209oi0Tei5PS8Ci0koni2Jt19TdxgvHRJo2DnTFQi0CdYXMMA8MOuzvV0i3&osversion=android_25&cv=IK7.1.10_Android&ndid=201904140605550c541b38f60a44e112cf2ee267416bb70114c9259f7112bb&imei=868415033406092&proto=8&conn=wifi&ram=3771211776&ua=XiaomiMIMAX2&logid=271%2C281%2C282%2C197%2C198%2C213%2C236%2C244%2C10002%2C10204%2C20101%2C30203%2C40201%2C50105%2C50209%2C50304%2C50404%2C60201%2C70008%2C80007%2C80107%2C80206%2C80310%2C90003%2C100008%2C100105%2C100210&icc=89860117851069322524&uid=725968072&ast=1&vv=1.0.3-201610121413.android&aid=f7d5f8308ac27601&smid=DuXcoNPtEgd9A80Ax0SIoQl7YNr9CenJ2pvGzV4YgraIWFO1Hh4PbmO49AK5XNiCmQYmEWqjwU2K13tQ2VQhqqcA&imsi=460015446106852&mtid=55d014445dd4faf6e6a1f36be5dabb18&card_pos=11&longitude=113.2661583&refurbish_mode=0&live_uid=725470987&crv=1.0&gender=1&user_level=4&slide_pos=0&tab_key=411F2448D92E4317&interest=0&location=CN%2C%E5%B9%BF%E4%B8%9C%E7%9C%81%2C%E5%B9%BF%E5%B7%9E%E5%B8%82&latitude=23.2203111&channel_id=4&stay_time=0&type=0&r_c=o2087268035&s_sg=vv1747ead009242c67d97bfbfff0b90e4b&s_sc=101&s_st=1562266141";

    public static function getLiveUrl($roomId)
    {

    }


    /**
     * @return array
     * @throws \ErrorException
     */
    public static function getDancingRoomId()
    {
        $curl = new Curl();
        $curl->setUserAgent('user-agent", "Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.22 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1');
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setReferrer('http://www.yy.com/');
        $curl->setHeader('X-Requested-With', 'XMLHttpRequest');
        $curl->get(self::DANCE_ROOM_API_URL);
        $data = json_decode($curl->response, true);
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $arr = [];
        if (isset($data['cards']) && !empty($data['cards'])) {
            $list = $data['cards'];
            foreach ($list as $vo) {//print_r($vo);exit;
                if (isset($vo['cover']['tags']['posa']['text']) && $vo['cover']['tags']['posa']['text'] == '正在热舞') {
                    $arr[] = ['roomId' => $vo['data']['live_info']['creator']['id'], 'nickName' => $vo['data']['live_info']['creator']['nick']];
                }
            }
            $arr = array_column($arr, 'nickName', 'roomId');
        }
        unset($data);
        return $arr;

    }
}