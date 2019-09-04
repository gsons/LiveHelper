<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/7
 * Time: 3:49
 */

namespace Gsons;

use think\Cache;
use Gsons\Live\Live;

class App
{
    public static function run($config, $record = false)
    {
        printf("\007");
        $pid = getmypid();

        Cache::init([
            'type' => 'FileLock',
            'path' => './cache/',
            'prefix' => '',
            'expire' => 0,
        ]);
  
        $lock=Cache::get("cache_lock");
        if(!$lock){
            Cache::clear();
            Cache::set("cache_lock",1,10);
        }

        Console::init($pid);
        Console::logEOL();
        Console::log('start recording' . PHP_EOL);

        $pidArr = Cache::get("pidArr");
        $pidArr = $pidArr ? $pidArr : [];
        $pidArr[] = $pid;
        Cache::set("pidArr", $pidArr);
        while (1) {
            $fetchPidNum = 0;
            $pidArr = Cache::get("pidArr");
            $pidArr = $pidArr ? $pidArr : [];
            foreach ($pidArr as $_pid) {
                $fetching = Cache::get('fetching_pid_' . $_pid);
                if ($fetching && $pid != $_pid) {
                    $fetchPidNum++;
                }
            }
            if ($fetchPidNum>0) {
                continue;
            }
            Console::record("{$pid}:{$fetchPidNum}");
            
            Cache::set('fetching_pid_' . $pid, true);
            foreach ($config as $liveName => $roomIdArr) {

                /**
                 * @var $class \Gsons\Live\HuyaLive;
                 */
                $class = "\Gsons\Live\\{$liveName}Live";
                if (!class_exists($class)) {
                    Console::error("ERROR:cant not find class $class");
                    continue;
                }
                try {
                    $arr = $class::getDancingRoomId();
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
                    printf("\007");
                    $roomUrl = sprintf($class::BASE_ROOM_URL, $roomId);
                    $logInfo = "{$siteName}-{$nick}-{$roomUrl}";
                    Console::log($logInfo);
                    Console::record($logInfo);
                    Cache::set($room_key, $roomId, 230);
                    if ($record) {
                        Cache::set('fetching_pid_' . $pid,false);
                        try {
                            $liveUrl = $class::getLiveUrl($roomId);
                            $fileName = "{$siteName}-{$nick}-" . date('Ymd_His') . '.mp4';
                            Live::record($liveUrl, 'video', $fileName, 240);
                        } catch (\ErrorException $e) {
                            Console::error($e);
                        }
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                            break;
                        }
                    }
                }
                unset($arr);
            }
            Console::logEOL();
            sleep(7);
        }
    }

}