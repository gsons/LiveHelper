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


    function exceptionHandler($exception) {
        $traceline = "#%s %s(%s): %s(%s)";
        $msg = "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s\n  thrown in %s on line %s";

        $trace = $exception->getTrace();
        foreach ($trace as $key => $stackPoint) {
            $trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
        }

        $result = array();
        foreach ($trace as $key => $stackPoint) {
            $result[] = sprintf(
                $traceline,
                $key,
                $stackPoint['file'],
                $stackPoint['line'],
                $stackPoint['function'],
                implode(', ', $stackPoint['args'])
            );
        }
        $result[] = '#' . ++$key . ' {main}';
        $msg = sprintf(
            $msg,
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            implode("\n", $result),
            $exception->getFile(),
            $exception->getLine()
        );
       return $msg;
    }
}