<?php

$variations = [
    '8733169303:AAF_v2HuRL8zgZW0VJMF4DjDhsXRcpE_IdU',
    '8733169303:AAF_v2HuRL8zgZW0VJMF4DjDhsXRcpE_ldU', // l instead of I
    '8733169303:AAF_v2HuRL8zgZW0VJMF4DjDhsXRcpE_1dU', // 1 instead of I
    '8733169303:AAF_v2HuRl8zgZW0VJMF4DjDhsXRcpE_IdU', // l instead of L
    '8733169303:AAF_v2HuRl8zgZW0VJMF4DjDhsXRcpE_ldU', // l instead of L & l instead of I
    '8733169303:AAF_v2HuRL8zgZW0VJMF4DjDhsXRcpE_IDU',
    '8733169303:AAF_v2HuRL8zgZW0VJMF4djDhsXRcpE_IdU', // d instead of D
    '8733169303:AAF_v2HuRL8zgZW0VJMF4DjDHSXRcpE_IdU',
];

foreach ($variations as $token) {
    $url = "https://api.telegram.org/bot{$token}/getMe";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($res, true);
    if ($data && isset($data['ok']) && $data['ok'] === true) {
        echo "SUCCESS TOKEN FOUND: " . $token . "\n";
        echo "Bot Name: " . $data['result']['username'] . "\n";
        exit(0);
    }
}
echo "NONE MATCHED\n";
