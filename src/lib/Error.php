<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2020/2/15
 * Time: 19:14
 */

namespace Gsons\lib;
use Gsons\Console;

class Error
{
    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'errHandle']);
        set_exception_handler([__CLASS__, 'exceptionHandle']);
    }

    /**
     * @param $errorNo
     * @param $errorStr
     * @param $errorFile
     * @param $errorLine
     * @throws SystemException
     */
    public static function errHandle($errorNo, $errorStr, $errorFile, $errorLine)
    {
        $errorNoMap = [E_WARNING => "WARNING", E_NOTICE => "NOTICE", E_STRICT => "STRICT", E_DEPRECATED => "DEPRECATED"];
        $msg = isset($errorNoMap[$errorNo]) ? $errorNoMap[$errorNo] : "Unknown Error Type errorNo:$errorNo";
        $err = "$msg: $errorStr in $errorFile on line $errorLine";
        throw new SystemException($err);
    }

    public static function exceptionHandle(\Exception $e)
    {
        Console::error($e);
    }
}