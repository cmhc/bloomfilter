<?php
require dirname(__DIR__) . '/src/store/contract.php';
require dirname(__DIR__) . '/src/store/file.php';
require dirname(__DIR__) . '/src/bloomfilter.php';
require dirname(__DIR__) . '/src/hash.php';


$hash = new bloomfilter\hash();
$store = new bloomfilter\store\file(__DIR__ . '/urlfilter');

$bloomfilter = new bloomfilter\bloomfilter($hash, $store);

$bloomfilter->add('hello');
var_dump($bloomfilter->exists('hello'));

