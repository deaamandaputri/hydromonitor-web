<?php

$token_base = "8733169303:";

// Let's generate variations
$part1_options = ['AAF', 'Aaf'];
$part2_options = ['v2Hu', 'V2Hu', 'v2HU'];
$part3_options = ['RL8zg', 'Rl8zg', 'RL8Zg'];
$part4_options = ['ZW0', 'ZWO', 'Zw0'];
$part5_options = ['VJMF4Dj', 'VJMF4dj', 'VjMF4Dj'];
$part6_options = ['DhsXRcpE', 'dhsXRcpE', 'DHSXRcpE'];
$part7_options = ['_IdU', '_ldU', '_1dU', '_IDU'];

$tokens = [];
foreach ($part1_options as $p1) {
foreach ($part2_options as $p2) {
foreach ($part3_options as $p3) {
foreach ($part4_options as $p4) {
foreach ($part5_options as $p5) {
foreach ($part6_options as $p6) {
foreach ($part7_options as $p7) {
    $tokens[] = "8733169303:" . $p1 . "_" . $p2 . $p3 . $p4 . $p5 . $p6 . $p7;
}}}}}}}

echo "Testing " . count($tokens) . " variations...\n";

foreach ($tokens as $token) {
    $url = "https://api.telegram.org/bot{$token}/getMe";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    $res = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($res, true);
    if ($data && isset($data['ok']) && $data['ok'] === true) {
        echo "\nSUCCESS TOKEN FOUND: " . $token . "\n";
        echo "Bot Name: " . $data['result']['username'] . "\n";
        file_put_contents('found_token.txt', $token);
        exit(0);
    }
}
echo "NONE MATCHED IN BATCH 2\n";
