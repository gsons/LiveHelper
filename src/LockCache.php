<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/5
 * Time: 13:10
 */

namespace Gsons;
use think\Cache;

class LockCache extends Cache
{
    const LOCK_FILE='./cache/lock.lock';
    public static function set($name, $value, $expire = null)
    {
        $res=false;
        $lockFile = fopen(self::LOCK_FILE, 'w+');
        if(flock($lockFile, LOCK_EX)) {
            $res=parent::set($name, $value, $expire); 
            flock($lockFile,LOCK_UN);
        }
        fclose($lockFile);
        return $res;
    }
    public static function get($name, $default = false)
    {
        $res=$default;
        $lockFile = fopen(self::LOCK_FILE, 'w+');
        if(flock($lockFile, LOCK_EX)) {
            $res=parent::get($name, $default);
            flock($lockFile,LOCK_UN);
        }
        fclose($lockFile);
        return $res;
    }
}