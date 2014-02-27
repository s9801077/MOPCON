<?php

$release_branch= 'deploy';                 // 這個變數應該寫死，不可以從外部參數帶進來，否則會有安全問題！！
$release_ref = "refs/heads/{$release_branch}";

//////////////////////////////////////////////////////////////////////////////////

// 處理送來的資料，如果無法正常解析就不處理
$payload = json_decode($_POST['payload'], true);
if (!is_array($payload)) {
    return;
}


// 只處理 release branch 的 push
if (!$release_ref == $payload['ref']) {
    return;
}


// pull 最新的 code 下來
exec("git reset --hard");
exec("git checkout {$release_branch}");
exec("git pull origin {$release_branch}");


// 如果有 memcache，把最新的 deploy 狀況寫入 memcache
$memcache_ok = function_exists("memcache_connect");
if ($memcache_ok) {
    $current_commit_id = $payload['after'];
    $date = new Date();
    $deploy_status = [
        'commit_id'    => $current_commit_id,
        'deploy_time'  => $date
    ];

    $mc = memcache_connect('localhost', 11211);

    memcache_set($mc, 'deploy_status', $deploy_status);
}

