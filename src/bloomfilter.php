<?php
namespace bloomfilter;

/**
 * bloom filter
 * 继承该方法来实现一个bloom filter
 */
class bloomfilter
{
    /**
     * @param object $Hash  产生hash字符串的对象
     * @param object $Store 存储器
     */
    public function __construct(\bloomfilter\hash $Hash, \bloomfilter\store\contract $Store)
    {
        $this->Hash = $Hash;
        $this->Store = $Store;
    }

    /**
     * 添加到集合中
     * @param  string $string 需要添加的内容
     * @return  boolean
     */
    public function add($string)
    {
        $hashArray = $this->Hash->get($string, 3, 24);
        return $this->Store->add($hashArray);
    }

    /**
     * 查询是否存在, 存在的一定会存在, 不存在有一定几率会误判
     * @param  string $string
     * @return  boolean 
     */
    public function exists($string)
    {
        $hashArray = $this->Hash->get($string, 3, 24);
        $res = $this->Store->get($hashArray);
        foreach ($res as $bit) {
            if ($bit == 0) {
                return false;
            }
        }
        return true;
    }

}