<?php
/**
 * hash bit存储接口
 */
namespace bloomfilter\store;

interface contract
{
    public function add($hashArray);

    public function get($hashArray);
}