<?php

/**
 * Redis 相关操作类
 */
class RedisAction extends RedisBase
{
    public function __construct()
    {
        try {
            parent::__construct();
        } catch (Exception $e) {
            throw new Exception('连接Reis服务器失败！');
        }
    }

    public function __destruct()
    {
        $this->redis->close();
    }

    /**
     * 原子性递增一个key的值
     * @param string $key
     * @param number $value
     */
    public function incr($key, $value = 0)
    {
        if (empty($value)) {
            $result = $this->redis->incr($key);
        } else {
            $result = $this->redis->incrBy($key, $value);
        }

        return $result;
    }

    /**
     * 原子性递减一个key的值
     * @param string $key
     * @param number $value
     */
    public function decr($key, $value = 0)
    {
        if (empty($value)) {
            $result = $this->redis->decr($key);
        } else {
            $result = $this->redis->decrBy($key, $value);
        }

        return $result;
    }

    /**
     * 存储值到指定的key中
     * @param string $key
     * @param string $value
     */
    public function setKey($key, $value)
    {
        return $this->redis->set($key, $value);
    }

    /**
     * 获取指定key中存储的值
     * @param string $key
     */
    public function getKey($key)
    {
        return $this->redis->get($key);
    }

    /**
     * 从Redis中删除指定的key
     * @param string $key Redis key
     */
    public function delKey($key)
    {
        return $this->redis->delete($key);
    }

    /**
     * 向指定的有序集合中添加成员
     * @param string $key 有序集合的key
     * @param string $member 成员的名称
     * @param unknown $score 成员的score
     */
    public function addMember($key, $member, $score)
    {
        return $this->redis->zAdd($key, $score, $member);
    }


    /**
     * 获取指定名称的成员的score值
     * @param unknown $key 有序集合key
     * @param unknown $member 要获取soce的成员名称
     */
    public function getMemScore($key, $member)
    {
        return $this->redis->zScore($key, $member);
    }

    /**
     * 从指定的有序集合中删除成员
     * @param string $key 集合的key
     * @param string $member 要删除的成员名称
     */
    public function delMember($key, $member)
    {
        return $this->redis->zDelete($key, $member);
    }

    /**
     * 从有序集合中获取指定范围成员的值
     * @param string $key 要获取的有序集合的key
     * @param int $start 获取成员的起始位置
     * @param int $end 获取成员的结束位置
     * @param int $order 1按照score 倒序排列结果， 0 按score 正序排列结果
     * @param bool $withScore 是否返回score
     */
    public function getRange($key, $start, $end, $withScore = TRUE, $order = 1)
    {
        if ($order == 1) {
            return $this->redis->zRevRange($key, $start, $end, $withScore);
        } else {
            return $this->redis->zRange($key, $start, $end, $withScore);
        }
    }

    /**
     * 以原子性增加成员的score值
     * @param string $key 有序集合的key
     * @param string $member 要增加score的成员名称
     * @param int $increment 要增加的score值（可以转换成双精度的值，可以为负数，负数就减去相应的值）
     */
    public function zIncrBy($key, $member, $increment = 1)
    {
        return $this->redis->zIncrBy($key, $increment, $member);
    }

    /**
     * 设置key过期时间，以秒为单位
     * @param string $key 要设置过期时间的key
     * @param int $expireTime 过期时间,秒为单位，例如设置有效期为1小时，$expireTime = 3600
     * @return bool TRUE in case of success, FALSE in case of failure.
     */
    public function setExpireTime($key, $expireTime)
    {
        $nowTime = time();
        return $this->redis->expireAt($key, $nowTime + $expireTime);
    }



#======================================================List==========================
    /**
     * 从左侧存储值到指定的List中
     * @param string $key
     * @param string $value
     */
    public function lpushList($key, $value)
    {
        return $this->redis->lPush($key, $value);
    }

    /**
     * 从右侧存储值到指定的List中
     * @param string $key
     * @param string $value
     */
    public function rpush($key, $value)
    {
        return $this->redis->rpush($key, $value);
    }

    /**
     * 从List中获取指定范围成员的值
     * @param string $key
     * @param int $start 获取成员的起始位置
     * @param int $end 获取成员的结束位置
     */
    public function lrangeList($key, $start, $end)
    {
        return $this->redis->lRange($key, $start, $end);
    }

    /**
     * 获取List的长度中
     * @param string $key
     */
    public function lLen($key)
    {
        return $this->redis->lLen($key);
    }

    /**
     * 根据参数 count 的值，移除列表中与参数 value 相等的元素
     * @param string $key
     * @param string $value
     * @param string $count
     * count > 0 : 从表头开始向表尾搜索，移除与 value 相等的元素，数量为 count
     * count < 0 : 从表尾开始向表头搜索，移除与 value 相等的元素，数量为 count 的绝对值
     * count = 0 : 移除表中所有与 value 相等的值
     */
    public function lRem($key, $value, $count = 0)
    {
        return $this->redis->lRem($key, $value, $count);
    }

    /**
     * 从List的右边删除一个成员
     * @param string $key
     */
    public function rPop($key)
    {
        return $this->redis->rPop($key);
    }



#####################################Hash######################################
    /**
     * 获取Hash表key中给定域field的值
     * @param string $hash
     * @param string $key
     */
    public function hGet($hash, $key)
    {
        return $this->redis->hGet($hash, $key);
    }

    /**
     * 删除一个或者多个Hash表字段
     * @param string $hash
     * @param string $key
     */
    public function hDel($hash, $key)
    {
        return $this->redis->hDel($hash, $key);
    }

    /**
     * 将Hash表key中的域field的值设为value
     * @param string $hash
     * @param string $key
     * @param string $value
     */
    public function hSet($hash, $key, $value)
    {
        return $this->redis->hSet($hash, $key, $value);
    }

    /**
     * 将多个 field-value (字段-值)对设置到哈希表key中
     * @param string $hash
     * @param array  $array
     */
    public function hMset($hash, $array)
    {
        return $this->redis->hMset($hash, $array);

    }

    /**
     * 获取 key 指定的哈希表中所有的字段和值
     * @param string $key
     */
    public function hGetAll($key)
    {
        return $this->redis->hGetAll($key);
    }

    /**
     * 获取Hash表中字段数量
     * @param string $hash
     *
     */
    public function hLen($hash)
    {
        return $this->redis->hLen($hash);
    }

#================================set==================================
    /**
     * 将一个或多个 value 值加入到集合 key 当中，已经存在于集合的 value 值将被忽略。
     * @param string $key
     *
     */
    public function sAdd($key, $value)
    {
        return $this->redis->sAdd($key, $value);
    }

    /**
     * 获取与该Key关联的Set中所有的成员。
     * @param string $key
     *
     */
    public function sMembers($key)
    {
        return $this->redis->sMembers($key);
    }
}

?>