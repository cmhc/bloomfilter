<?php
require __DIR__ . '/hash.php';

$Hash = new bloomfilter\hash();
$res = $Hash->get('abcdefghijklmnopqrstuvwxtrgfsdfssyz1234567890', 6, 24);
print_r($res);


//---------------
// 测试hash的碰撞率
// 
function createwd($len = 32)
{
    $wd = '';
    for ($i=0; $i < $len; $i++) {
        $wd .= chr(mt_rand(97,122));
    }
    return $wd;
}

$hashResults = array();
$conflict = array();
$res = $Hash->get('1', 6, 24);
foreach ($res as $method => $hash) {
    $hashResults[$method] = array();
    $conflict[$method] = 0;
}

for ($i=0; $i<10000; $i++) {
    $res = $Hash->get(createwd(32), 6, 24);
    foreach ($res as $method=>$hash) {
        if (!isset($hashResults[$method][$hash])) {
            $hashResults[$method][$hash] = 1;
        } else {
            $conflict[$method] += 1;
        }
    }
}

print_r($conflict);