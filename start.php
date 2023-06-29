<?php

// 定义要访问的URL数组
$urls = [
    'https://baidu.com/admin/bonus',
    'https://baidu.com/shazi.html',
    'https://baidu.com/admin/yuebao'
];

$pid = pcntl_fork();

if ($pid === -1) {
    echo '由于系统限制导致无法创建子进程';
    die();
} elseif ($pid) {
    exit();
} else {
    if (posix_setsid() === -1) {
        die('进程已经是一个会话');
    }
    fclose(STDIN);
    fclose(STDOUT);
    fclose(STDERR);
    $lofoFile =  'Log.txt';
    if(!file_exists($lofoFile)){
        touch($lofoFile);
        chmod($lofoFile,0755);
    }
    while (true) {
        foreach ($urls as $url) {
            // 使用CURL执行GET请求
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // 执行请求并获取响应
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // 检查请求是否成功
            if ($httpCode == 200) {
                $message= "Success---".date('Y-m-d H:i:s').'--url--'.$url;
                // 在这里处理成功的情况，例如记录日志或其他操作
            } else {
                $message = "access---".date('Y-m-d H:i:s').'--url--'.$url;
                // 在这里处理失败的情况，例如记录日志或其他操作
            }
            file_put_contents($lofoFile,$message.PHP_EOL,FILE_APPEND);
            // 关闭CURL会话
            curl_close($ch);
        }
        sleep(60); // 休眠60秒
    }
}
