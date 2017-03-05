<?php

/**
 * 通过redis实现互斥锁功能
 */
class RedisLock extends RedisBase
{
    private $timeout;      //锁的有效期，秒为单位

    private $expire;       //锁的过期时间，unix时间戳

    private $sleepTime;    //获取lock失败之后程序睡眠时间，微妙为单位

    private $maxTryNum;    //尝试获取锁的次数

    public function __construct()
    {
        try {
            parent::__construct();
            $this->settimeout();
            $this->setSleepTime();
            $this->setMaxTryNum();
        } catch (Exception $e) {
            throw new Exception('连接Reis服务器失败！');
        }
    }

    /**
     * 设置锁过期时间，秒为单位
     * @param int $value
     */
    public function setTimeOut($value)
    {
        if (preg_match('/^[1-9]\d*$/', $value)) {
            $this->timeout = $value;
        } else {
            $this->timeout = 3;   //默认3秒
        }
    }

    /**
     * 设置程序休眠时间，微妙单位
     * @param int $value
     */
    public function setSleepTime($value)
    {
        if (preg_match('/^[1-9]\d*$/', $value)) {
            $this->sleepTime = $value;
        } else {
            $this->sleepTime = 1500000; //默认1.5秒
        }
    }

    /**
     * 设置尝试获取锁的次数
     * @param int $value 尝试次数
     */
    public function setMaxTryNum($value)
    {
        if (preg_match('/^[1-9]\d*$/', $value)) {
            $this->maxTryNum = $value;
        } else {
            $this->maxTryNum = 3; //默认3次
        }
    }

    /**
     * 获取lock
     * @param string 要获取锁定的key
     * @return bool true:成功获取锁，可以进行后续操作， false-获取锁失败，终止后续操作
     */
    public function getLock($key)
    {

        $this->expire = time() + $this->timeout + 1;

        $result = $this->redis->setnx($key, $this->expire);

        if ($result) {
            return true;
        } else {
            $value = $this->redis->get($key);

            if ($value === false) {
                return false;
            } else {
                //还没有过期，等待一定时间继续重头开始
                if ($value > time()) {
                    //超过最大尝试获取锁的次数，直接返回false
                    if (($this->maxTryNum--) > 0) {
                        usleep($this->sleepTime);
                        return $this->getLock($key);
                    } else {
                        return false;
                    }
                } else {
                    $this->expire = time() + $this->timeout + 1;
                    $ioldValue = $this->redis->getSet($key, $this->expire);

                    /*
                       设置成功之后，返回之前的值，如果之前的值已经过期，说明获取锁。
                       如果之前的值没有过期，说明有其他客户端获取了锁，当前客户端需要重新等待
                    */
                    if ($ioldValue <= time()) {
                        return true;   //成功获得锁
                    } else {
                        //超过最大尝试获取锁的次数，直接返回false
                        if (($this->maxTryNum--) > 0) {
                            usleep($this->sleepTime);
                            return $this->getLock($key);
                        } else {
                            return false;
                        }
                    }
                }
            }
        }
    }

    /**
     * 释放lock
     * @param string $key 要释放的lock在redis中对应的key
     * @return bool
     */
    public function delLock($key)
    {
        $value = $this->redis->get($key);

        /*
			1. 一段时间内只有1个客户端来获取锁，客户端处理完相应操作之后，无论是否过期，都应该删除锁；
			2. 由于某些原因导致获取锁的客户端在之后的其他操作上消耗时间过长（超过获取锁时设置的有效期），锁已经被其他客户端获取，
			并且更新了锁的过期时间，当前客户端不需要删除锁（由获取锁的客户端来删除）
		*/
        if ($value == $this->expire || $this->expire >= time()) {
            $this->redis->del($key);
        }

        return true;
    }

    public function __destruct()
    {
        $this->redis->close();
    }
}

?>