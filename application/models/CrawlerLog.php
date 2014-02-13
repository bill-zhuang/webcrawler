<?php

class Application_Model_CrawlerLog
{
    public static function writeUrlLog($url, $url_error_message)
    {
    	$filename = str_replace('http://', '', $url) . '_' . date('Y-m-d') . '_error_log.txt';
    	$fp = fopen($filename, 'a+');
    	fwrite($fp, date('H:i:s') . $url_error_message);
    	fclose($fp);
    }

}

