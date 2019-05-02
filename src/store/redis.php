<?php
namespace bloomfilter\store;

/**
 * 使用redis实现的bit存储类
 */
class redis implements contract
{
    private $redis;

    private $key;

    public function __construct($redisConfig)
    {
        $this->redis = pconnect($redisConfig['ip'], $redisConfig['port'], $redisConfig['timeout']);
        $this->key = $redisConfig['key'];
    }

    public function __destruct()
    {
        
    }

    /**
     * 添加比特位
     * @return  boolean
     */
    public function add($hashArray)
    {
        foreach($hashArray as $hash) {
            $this->setBit($hash);
        }
    }

    /**
     * 获取bit位
     * @return array
     */
    public function get($hashArray)
    {
        $bit = array();
        foreach($hashArray as $hash) {
            $bit[] = $this->getBit($hash);
        }
        return $bit;
    }

    /**
     * 设置bit位
     */
    public function setBit($position)
    {
        $this->redis->setBit($this->key, $position, 1);
    }

    /**
     * 获取 bit 位
     * @return int
     */
    public function getBit($position)
    {
        $this->redis->getBit($this->key, $position);
    }
}