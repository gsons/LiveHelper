<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/4
 * Time: 2:28
 */

namespace Gsons\Live;


namespace Gsons\Live;
use Gsons\HttpCurl;

abstract class Live
{
    /**
     * 录制直播视频
     * @param $liveUrl
     * @param $path
     * @param $fileName
     * @param string $endTime
     */
    public static function record($liveUrl, $path, $fileName, $endTime)
    {
        if (!is_dir($path)) {
            $path=iconv('utf-8', 'gbk',$path);
            mkdir($path, 0777, true);
        }
        $fileName=iconv('utf-8', 'gbk',$fileName);
        //$cmd = "ffmpeg -ss 0:0 -t {$endTime} -i \"{$liveUrl}\" -max_muxing_queue_size 1024 {$path}/{$fileName}";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
          $cmd="chcp 65001 && ffmpeg -i \"{$liveUrl}\" -t {$endTime} -c:v copy -c:a copy  {$path}/{$fileName}";
          exec($cmd);
        }else{ 
          $cmd="ffmpeg -i \"{$liveUrl}\" -t {$endTime} -c:v copy -c:a copy  {$path}/{$fileName}"." > /dev/null &";
          shell_exec($cmd);
        }
    }
}

