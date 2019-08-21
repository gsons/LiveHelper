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
    const FILE_ERROR = "./cache/error.log";
    const FILE_DEBUG = "./cache/log.log";
    const FILE_ROOM = "./cache/room.log";

    public static function log($msg, $isGBK = true)
    {
        if (is_array($msg)) {
            $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        $date = date('Y-m-d H:i:s');
        $msg = $date . ' ' . $msg . PHP_EOL;
        file_put_contents(self::FILE_DEBUG, $msg, FILE_APPEND);
        if ($isGBK) {
            $msg = iconv('UTF-8', 'gbk//IGNORE', $msg);
        }
        echo $msg;
    }

    public static function logEOL()
    {
        echo PHP_EOL;
        file_put_contents(self::FILE_DEBUG, PHP_EOL, FILE_APPEND);
    }

    public static function record($msg)
    {
        $date = date('Y-m-d H:i:s');
        $msg = $date . ' ' . $msg . PHP_EOL;
        file_put_contents(self::FILE_ROOM, $msg, FILE_APPEND);
    }


    /**
     * @param $param mixed
     * @return string
     */
    public static function error($param)
    {
        if ($param instanceof \Exception) {
            $traceline = "#%s %s(%s): %s(%s)";
            $msg = "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s\n  thrown in %s on line %s";

            $trace = $param->getTrace();
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
                get_class($param),
                $param->getMessage(),
                $param->getFile(),
                $param->getLine(),
                implode("\n", $result),
                $param->getFile(),
                $param->getLine()
            );
        } else {
            $msg = json_encode($param, true, JSON_UNESCAPED_UNICODE);
        }
        $date=date("Y-m-d H:i:s").":".PHP_EOL;
        file_put_contents(self::FILE_ERROR, $date.$msg.PHP_EOL, FILE_APPEND);
    }
}