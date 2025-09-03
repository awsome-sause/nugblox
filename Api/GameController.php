<?php
require_once("../main/config.php");

$port = $_GET['port'] ?? null;
$action = $_GET['action'] ?? null;
$gameid = $_GET['gameid'] ?? null;
$password = $_GET['password'] ?? null;

if ($password !== $apipassword) {
    http_response_code(403);
    exit("Invalid password");
}

if (!in_array($action, ['start', 'stop']) || !is_numeric($port)) {
    http_response_code(400);
    exit("Invalid parameters");
}

$exePath = "C:\\NUGLOX\\player\\NUGLOX Player.exe";
$command = null;

if ($action === 'start') {
    $args = [
        '--server',
        '--port', $port,
        '--gameid', $gameid,
        '--authkey', $_GET['authkey'],
        '--baseurl', 'https://nuglox.com/',
        '--password', $apipassword
    ];

    $escapedArgs = array_map('escapeshellarg', $args);
    $argString = implode(' ', $escapedArgs);

    $command = "cmd /c start \"\" " . escapeshellarg($exePath) . " $argString";
} elseif ($action === 'stop') {
    $command = "for /f \"tokens=5\" %a in ('netstat -aon ^| find \":$port \"') do taskkill /F /PID %a";
}

//file_put_contents("server_log.txt", "[" . date("Y-m-d H:i:s") . "] $command\n", FILE_APPEND);

exec($command, $output, $exitCode);
echo ($exitCode === 0) ? "OK" : "Failed: $exitCode";
?>