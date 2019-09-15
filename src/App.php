<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/7
 * Time: 3:49
 */

namespace Gsons;

use Gsons\Live\Live;
use think\Cache;

class App
{
    static $recordProcessArr = [];

    public static function run($config, $record = false, $isGBK = false, $record_path = "./video")
    {
        Cache::init([
            'type' => 'File',
            'path' => './cache/',
            'prefix' => '',
            'expire' => 0,
        ]);
        Cache::clear();

        $pid = getmypid();
        Console::init($pid);
        Console::logEOL();

        printf("\007");
        Console::log('start recording' . PHP_EOL);

        while (1) {
            foreach ($config as $liveName => $roomIdArr) {
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
                    $arr = $class->getDancingRoomId();
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
                            Console::record($liveUrl);
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
            self::checkRecordProcess();
            Console::logEOL();
            sleep(7);
        }
    }

    public static function checkRecordProcess()
    {
        if (!empty(self::$recordProcessArr)) {
            foreach (self::$recordProcessArr as $roomKey => &$process) {
                if (is_resource($process)) {
                    $res = proc_get_status($process);
                    if (isset($res['running']) && $res['running'] != 1) {
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
        $str=str_replace(' ','',$str);
        $str = preg_replace_callback('/./u', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $str);
        return $str;
    }
}