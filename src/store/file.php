<?php
namespace bloomfilter\store;

/**
 * 用文件实现的bit存取类
 */
class file implements contract
{
    private $fp;
    
    public function __construct($file)
    {
        if (!file_exists($file)) {
            file_put_contents($file, '');
        }
        $this->fp = fopen($file, 'rb+');
        if (!$this->fp) {
            throw new \Exception("文件{$file}打开失败", 1);
        }
        if (!flock($this->fp, LOCK_EX | LOCK_NB)) {
            throw new Exception("获取独占锁失败", 1);
        }
    }

    public function __destruct()
    {
        flock($this->fp, LOCK_UN);
        fclose($this->fp);
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
        $seek = (int) ($position / 8);
        fseek($this->fp, $seek);
        $char = fread($this->fp, 1);
        $num = $char ? unpack('c', $char) : array(1=>0);
        $bit = (8 - $position % 8) % 8;
        $mask = 0x1 << $bit;
        $char = pack('c', $mask | $num[1]);
        fseek($this->fp, $seek);
        return fwrite($this->fp, $char);
    }

    /**
     * 获取 bit 位
     * @return int
     */
    public function getBit($position)
    {
        $seek = (int) ($position / 8);
        fseek($this->fp, $seek);
        $char = fread($this->fp, 1);
        $num = $char ? unpack('c', $char) : array(1=>0);
        $bit = (8 - $position % 8) % 8;
        $mask = 0x1 << $bit;
        return ($mask & $num[1]) >> $bit;
    }
}