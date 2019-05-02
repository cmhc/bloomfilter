<?php
namespace bloomfilter;

/**
 * hash 方法集合
 * 里面所有的hash函数都针对32位处理器做了调整，最高能生成的hash为32位
 * 但由于php integer类型不支持无符号数，所以最高只能生成31位
 * 参见 https://www.php.net/manual/zh/language.types.integer.php
 */
class hash
{
    /**
     * 当前的hash集合
     * @var array
     */
    protected $hashMethods = array(
        'JSHash', 'ELFHash','BKDRHash','SDBMHash','DJBHash','DEKHash','FNVHash'
    );

    protected $hashNums = 6;

    /**
     * 批量获取hash
     * @param  string $string 等待hash的字符串
     * @param  int $hashNums 需要hash的数量
     * @param  int $hashBits hash的位数
     */
    public function get($string, $hashNums, $hashBits)
    {
        if ($hashNums > $this->hashNums || $hashBits > 31) {
            throw new \Exception("hash函数最多为{$this->hashNums}个，hash位数最多为31位", 1);
        }
        $len = strlen($string);
        $hash = array();
        $mask = 0x7FFFFFFF >> (31 - $hashBits);
        for ($i=0; $i<$hashNums; $i++) {
            $hashMethod = $this->hashMethods[$i];
            $res = $this->$hashMethod($string, $len);
            $hash[$hashMethod] = $res & $mask;
        }
        return $hash;
    }

    /**
     * 由Justin Sobel编写的按位散列函数
     */
    public function JSHash($string, $len = null)
    {
        $hash = 1315423911;
        $len || $len = strlen($string);
        for ($i=0; $i<$len; $i++) {
            $hash ^= ((($hash << 5) & 0x7FFFFFFF) + ord($string[$i]) + ($hash >> 2));
            $hash &= 0x7FFFFFFF;
        }
        return $hash;
    }

    /**
     * 这个哈希函数来自Brian Kernighan和Dennis Ritchie的书“The C Programming Language”。
     * 它是一个简单的哈希函数，使用一组奇怪的可能种子，它们都构成了31 .... 31 ... 31等模式，它似乎与DJB哈希函数非常相似。
     */
    protected function BKDRHash($string, $len = null)
    {
        $seed = 131;  # 31 131 1313 13131 131313 etc..
        $hash = 0;
        $len || $len = strlen($string);
        for ($i=0; $i<$len; $i++) {
            $hash = (int) ((($hash * $seed) & 0x7FFFFFFF) + ord($string[$i]));
            $hash &= 0x7FFFFFFF;
        }
        return $hash;
    }

    /**
     * 这是在开源SDBM项目中使用的首选算法。
     * 哈希函数似乎对许多不同的数据集具有良好的总体分布。它似乎适用于数据集中元素的MSB存在高差异的情况。
     */
    protected function SDBMHash($string, $len = null)
    {
        $hash = 0;
        $len || $len = strlen($string);
        for ($i=0; $i<$len; $i++) {
            $hash = (int) (ord($string[$i]) + (($hash << 6) & 0x7FFFFFFF) + (($hash << 16) & 0x7FFFFFFF) - $hash);
            $hash &= 0x7FFFFFFF;
        }
        return $hash;
    }

    /**
     * 由Daniel J. Bernstein教授制作的算法，首先在usenet新闻组comp.lang.c上向世界展示。
     * 它是有史以来发布的最有效的哈希函数之一。
     */
    protected function DJBHash($string, $len = null)
    {
        $hash = 5381;
        $len || $len = strlen($string);
        for ($i=0; $i<$len; $i++) {
            $hash = (((($hash << 5) & 0x7FFFFFFF) + $hash) & 0x7FFFFFFF) + ord($string[$i]);
            $hash &= 0x7FFFFFFF;
        }
        return $hash;
    }

    /**
     * Donald E. Knuth在“计算机编程艺术第3卷”中提出的算法，主题是排序和搜索第6.4章。
     */
    protected function DEKHash($string, $len = null)
    {
        $len || $len = strlen($string);
        $hash = $len;
        for ($i=0; $i<$len; $i++) {
            $hash = ((($hash << 5) & 0x7FFFFFFF) ^ ($hash >> 27)) ^ ord($string[$i]);
        }
        return $hash;
    }

    /**
     * 类似于PJW Hash功能，但针对32位处理器进行了调整。它是基于UNIX的系统上的widley使用哈希函数。
     */
    public function ELFHash($string, $len = null)
    {
        $hash = 0;
        $len || $len = strlen($string);
        for ($i=0; $i<$len; $i++) {
            $hash = (($hash << 4) & 0x7FFFFFFF) + ord($string[$i]);
            $x = $hash & 0xF0000000;
            if ($x != 0) {
                $hash ^= ($x >> 24);
            }
            $hash &= ~$x;
        }
        return $hash;
    }
}