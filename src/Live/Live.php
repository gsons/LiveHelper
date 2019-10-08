<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/4
 * Time: 2:28
 */

namespace Gsons\Live;

abstract class Live implements Api
{

    abstract function getLiveUrl($roomId);
    abstract function getDancingRoomId();

    /**
     * 录制直播视频
     * @param $liveUrl
     * @param $path
     * @param $fileName
     * @param $time
     * @param $isGBK
     * @return resource
     */
    public static function record($liveUrl, $path, $fileName, $time, $isGBK = true)
    {
        if ($isGBK) {
            $path = iconv('utf-8', 'gbk', $path);
            $fileName = iconv('utf-8', 'gbk', $fileName);
        }
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $file = "{$path}/{$fileName}";
        $cmd = "ffmpeg -i \"{$liveUrl}\" -t {$time} -c:v copy -c:a copy {$file} -loglevel quiet";
        // $cmd='start "" cmd /k "chcp 65001 & ffmpeg -i "'.$liveUrl.'" -t '.$time.' -c:v copy -c:a copy  "'.$file.'" "';
        $process=proc_open($cmd,[['pipe','r']],$pipes);
        return $process;
    }

/*    private  function exec($cmd)
    {
        if (substr(php_uname(), 0, 7) == "Windows") {
            $cmd="start /B " . $cmd;
            pclose(popen($cmd, "r"));
        } else {
            exec($cmd . " > /dev/null &");
        }
    }*/
}

