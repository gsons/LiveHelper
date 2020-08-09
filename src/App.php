<?php

namespace Gsons;

use Gsons\lib\Error;
use Gsons\Live\Live;
use think\Cache;
use think\Db;

class App
{
    //记录所有录制进程
    static $recordProcessArr = [];

    //上次爬虫时间
    static $lastSpiderTime = 0;

    //默认截图最低热度
    const HOT_NUM_LIMIT = 140000;

    //截图周期 三天一次
    const CAPTURE_TIME = 30 * 24 * 60 * 60;

    //截图最低热度
    static $hot_num_limit_arr = [
        'HuYa' => 140000,
        'DouYu' => 100000,
        'CC' => 100000,
        'YY' => 2000,
        'Huajiao' => 5000,
        'Egame' => 10000,
        'Inke' => 50000
    ];

    public static function run($config, $record = false, $isGBK = false, $record_path = "./video")
    {

//        Cache::clear();
        Console::logEOL();

        printf("\007");
        Console::log('start recording' . PHP_EOL);

        while (1) {
            foreach ($config as $liveName => $roomIdArr) {
                //$roomIdArr=$roomIdArr+self::getHotConfig($liveName,2*24*24,10);
                /**
                 * @var $class \Gsons\Live\HuyaLive;
                 */
                $ClassName = "\Gsons\Live\\{$liveName}Live";
                if (!class_exists($ClassName)) {
                    Console::error("ERROR:cant not find class $ClassName");
                    continue;
                }
                $class = new $ClassName();
                try {
                    $arr = $class->getDancingRoom();
                } catch (\ErrorException $e) {
                    Console::error($e);
                    continue;
                }
                $siteName = $class::SITE_NAME;
                Console::log("{$siteName}:" . json_encode($arr, JSON_UNESCAPED_UNICODE));
                foreach ($arr as $roomId => $nick) {
                    if (!in_array($roomId, array_keys($roomIdArr))) {
                        continue;
                    }
                    $room_key = $liveName . '_room_id_' . $roomId;
                    $isSet = Cache::get($room_key);
                    if ($isSet) {
                        Console::log("exist:{$siteName}-{$nick}-{$roomId}");
                        continue;
                    }

                    $isSetCache = Cache::set($room_key, $roomId, 230);
                    if (!$isSetCache) {
                        Console::error("ERROR:cant not set cache " . $room_key);
                        continue;
                    }

                    printf("\007");
                    $roomUrl = sprintf($class::BASE_ROOM_URL, $roomId);
                    $logInfo = "{$siteName}-{$nick}-{$roomUrl}";
                    Console::log($logInfo);
                    Console::record($logInfo);
                    if ($record) {
                        try {
                            $liveUrl = $class->getLiveUrl($roomId);
                            //防止昵称出现特殊字符导致ffmpeg无法识别文件路径
                            $nick = self::filterNick($nick);
                            $fileName = "{$siteName}-{$nick}_" . date('YmdHis') . '.mp4';
                            $path = "{$record_path}/{$siteName}/{$nick}/" . date('Y-m-d');
                            $process = Live::record($liveUrl, $path, $fileName, 240, $isGBK);
                            self::$recordProcessArr[$room_key] = $process;
                            $res = proc_get_status($process);
                            Console::log("录制进程ID({$res['pid']})已开启:{$room_key}");
                        } catch (\ErrorException $e) {
                            Console::error($e);
                        }
                    }
                }
                unset($arr);
                unset($class);
            }
            $t_start = time();
            $exec_time = 0;
            if ($t_start - self::$lastSpiderTime > 30 * 60) {
                $hot_config = ['Huajiao' => '花椒直播', 'HuYa' => '虎牙直播', 'DouYu' => '斗鱼直播', 'CC' => 'CC直播', 'YY' => 'YY直播', 'Egame' => '企鹅电竞', 'Inke' => '映客直播'];
                self::spiderHot($hot_config, $isGBK, $record_path);
                $t_end = time();
                self::$lastSpiderTime = time();
                $exec_time = $t_end - $t_start;
            }
            Console::logEOL();
            if ($exec_time < 7) sleep(7 - $exec_time);//先休眠再检测 保障录制进程的状态正确
            self::checkRecordProcess();
        }
    }


    public static function checkRecordProcess()
    {
        if (!empty(self::$recordProcessArr)) {
            foreach (self::$recordProcessArr as $roomKey => &$process) {
                if (is_resource($process)) {
                    $res = proc_get_status($process);
                    if (isset($res['running']) && !$res['running']) {
                        Cache::rm($roomKey);
                        Console::log("录制进程ID({$res['pid']})已关闭:{$roomKey}");
                        proc_close($process);
                        unset($process);
                    }
                }
            }
        }
    }

    // 过滤掉emoji表情
    private static function filterNick($str)
    {
        $str = str_replace(' ', '', $str);
        $str = preg_replace_callback('/./u', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $str);
        return $str;
    }

    public static function init()
    {
        date_default_timezone_set("PRC");
        //Error::register();

        // 数据库配置信息设置（全局有效）
        Db::setConfig([
            // 数据库类型
            'type' => 'sqlite',
            // 主机地址
            'dsn' => 'sqlite:live.db',
            // 数据库编码默认采用utf8
            'charset' => 'utf8',
            // 数据库调试模式
            'debug' => true,
        ]);

        Cache::init([
            'type' => 'File',
            'path' => './cache/',
            'prefix' => '',
            'expire' => 0,
        ]);
    }


    public static function spiderHot($config, $isGBK = false, $record_path = "./video")
    {
        Console::logEOL();
        Console::log("程序开始执行爬取热门直播。。。");
        foreach ($config as $liveCode => $liveName) {
            /**
             * @var $class  \Gsons\Live\HuyaLive;
             */
            $ClassName = "\Gsons\Live\\{$liveCode}Live";
            if (!class_exists($ClassName)) {
                Console::error("ERROR:cant not find class $ClassName");
                continue;
            }
            $class = new $ClassName();
            try {
                $arrList = $class->getHotDanceRoom();
            } catch (\Exception $e) {
                continue;
            }
            foreach ($arrList as $vo) {
                $roomId = $vo['room_id'];
                $nick = $vo['nick_name'];
                try {
                    $room = Db::table('cn_live_room')
                        ->where(['site_code' => $vo['site_code'], 'room_id' => $vo['room_id']])
                        ->order('record_date desc')
                        ->find();
                } catch (\Exception $e) {
                    Console::error($e);
                }
                $record_date = isset($room['record_date']) ? strtotime($room['record_date']) : 0;
                $hot_num_limit = isset(self::$hot_num_limit_arr[$liveCode]) ? self::$hot_num_limit_arr[$liveCode] : self::HOT_NUM_LIMIT;
                if ((time() - $record_date) > self::CAPTURE_TIME && $vo['hot_num'] > $hot_num_limit) {
                    try {
                        /**
                         * @var $class \Gsons\Live\HuyaLive;
                         */
                        $liveUrl = $class->getLiveUrl($roomId);
                        $siteName = $class::SITE_NAME;
                        //防止昵称出现特殊字符导致ffmpeg无法识别文件路径
                        $nick = self::filterNick($nick);
                        $fileName = "{$siteName}-{$nick}-{$roomId}_" . date('YmdHis') . '.png';
                        $path = "{$record_path}/{$siteName}/快照/";
                        Console::log("获取 {$siteName}-{$nick} 关键帧: " . $liveUrl);
                        $process = Live::capture($liveUrl, $path, $fileName, $isGBK);
                        $res = proc_get_status($process);
                        Console::log("截图进程ID({$res['pid']})已开启:{$siteName}-{$nick}-$roomId");
                    } catch (\ErrorException $e) {
                        Console::error($e);
                    }
                }
            }


            try {
                $res_add = Db::table('cn_live_room')->insertAll($arrList);
            } catch (\Exception $e) {
                $res_add = false;
                Console::error($e);
            }
            Console::log($res_add ? "新增{$liveName}{$res_add}条数据成功" : "新增{$liveName}数据失败");

        }
        Console::log("程序结束执行爬取热门直播。。。");
        Console::logEOL();
    }

    /**
     * 获取直播平台过去几个小时热度最高的主播
     * @param $siteCode
     * @param int $hour
     * @param int $num
     * @return array
     */
    public static function getHotConfig($siteCode, $hour = 2, $num = 10)
    {
        $time = time() - 60 * 60 * $hour;
        $field = 'room_id,max(nick_name) AS nick_name,max(room_url) AS room_url,max(site_name) AS site_name,max(hot_num) AS max_hot_num,avg(hot_num) AS avg_hot_num';

        try {
            $subQuery = Db::table("cn_live_room")
                ->field($field)
                ->where(['site_code' => $siteCode])
                ->where("record_time", '>', $time)
                ->group('room_id')
                ->buildSql();
            $arr = Db::table("{$subQuery} tb")
                ->order('avg_hot_num desc')
                ->limit(0, $num)
                ->select();
            return array_column($arr, 'nick_name', 'room_id');
        } catch (\Exception $e) {
            Console::error($e);
            return [];
        }
    }
}