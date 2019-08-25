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
        Cache::init([
            'type' => 'File',
            'path' => './cache/',
            'prefix' => '',
            'expire' => 0,
        ]);
        Cache::clear();
        Console::log('start recording' . PHP_EOL);
        while (1) {
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
                    Cache::set($room_key, $roomId, 3 * 60);
                    if ($record) {
                        try {
                            $liveUrl = $class::getLiveUrl($roomId);
                            $fileName = date('Ymd_His') . '.mp4';
                            Live::record($liveUrl, 'video', $fileName, '4:00');
                        } catch (\ErrorException $e) {
                            Console::error($e);
                        }
                        break;
                    }
                }
                unset($arr);
            }
            Console::logEOL();
            sleep(7);
        }
    }

}