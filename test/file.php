<?php
require __DIR__ . '/store.php';
require __DIR__ . '/file.php';

$file = new bloomfilter\file(__DIR__ . '/test');

$file->setBit(688781);

echo $file->getBit(688781);