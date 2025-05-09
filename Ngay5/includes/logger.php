<?php
function writeLog($actionDesc, $fileName = null) {
    $date = date('Y-m-d');
    $time = date('H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $logFile = "logs/log_$date.txt";

    $entry = "[$time] - $ip - $actionDesc";
    if ($fileName) {
        $entry .= " - File: $fileName";
    }
    $entry .= PHP_EOL;

    file_put_contents($logFile, $entry, FILE_APPEND);
}
?>
