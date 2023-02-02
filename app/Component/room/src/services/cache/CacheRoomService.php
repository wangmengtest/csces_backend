<?php

namespace App\Component\room\src\services\cache;

use Vss\Common\Services\WebBaseService;
use Vss\Traits\CacheTrait;
use Vss\Traits\SingletonTrait;

/**
 * CacheRoomService
 */
class CacheRoomService extends WebBaseService
{
    use CacheTrait;
    use SingletonTrait;

    /**
     * 获取room hash对象的相关信息，代替redis的hgetall
     */
    public function hGetAll($key)
    {
        $keys   = vss_redis()->hkeys($key);
        $result = [];
        foreach ($keys as $field) {
            $result[] = vss_redis()->hget($key, $field);
        }
        return $result;
    }
}
