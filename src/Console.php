<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/7
 * Time: 3:29
 */

namespace Gsons;


class Console
{
    public static function log($msg, $isGBK = false)
    {
        if (is_array($msg)) {
            $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        $date = date('Y-m-d H:i:s');
        $msg = $date . ' ' . $msg . PHP_EOL;
        file_put_contents('log.txt', $msg, FILE_APPEND);
        if ($isGBK) {
            $msg = iconv('UTF-8', 'gbk//IGNORE', $msg);
        }
        echo $msg;
    }

    public static function logEOL()
    {
        echo PHP_EOL;
        file_put_contents('log.txt', PHP_EOL, FILE_APPEND);
    }

    public static function record($msg)
    {
        $date = date('Y-m-d H:i:s');
        $msg = $date . ' ' . $msg . PHP_EOL;
        file_put_contents('room.txt', $msg, FILE_APPEND);
    }
}