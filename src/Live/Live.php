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
            mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
        }
        $cmd = "ffmpeg -ss 0:0 -t {$endTime} -i {$liveUrl} -max_muxing_queue_size 1024 {$path}/{$fileName}";
        exec($cmd);
    }
}