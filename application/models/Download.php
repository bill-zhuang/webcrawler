<?php

class Application_Model_Download
{
    private function sendRequest($url, array $postData, $method = 'GET')
    {
    	$postData = http_build_query($postData);
    
    	$options = array(
    			'http' => array(
    					'method' => $method,
    					'header' => 'Content-type:application/x-www-form-urlencoded',
    					'content' => $postData,
    					'timeout' => 15 * 60, ) );
    
    	$context = stream_context_create($options);
    	return file_get_contents($url, false, $context);
    }
    
    public static function curlGetContents($url)
    {
    	$timeout = 5;
    	//$user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:26.0) Gecko/20100101 Firefox/26.0';
    
    	$ch = curl_init($url);
    	curl_setopt($ch, CURLOPT_URL, $url);
    	//curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    	$data = curl_exec($ch);
    	curl_close($ch);
    
    	return $data;
    }
    
    public static function byCurlMultiple(array $filenameUrl, $dir, $downloadNum = 100)
    {
    	if(!file_exists($dir))
    	{
    		mkdir($dir, 0777, true);
    	}
    
    	$mh=curl_multi_init();
    	$urlhundred = array_chunk($filenameUrl, $downloadNum, true);
    
    	foreach($urlhundred as $nameurls)
    	{
    		foreach($nameurls as $filename=>$url)
    		{
    			if(!is_file($dir . $filename))
    			{
    				$conn[$filename] = curl_init($url);
    				$fp[$filename] = fopen($dir . $filename, "w+");
    
    				curl_setopt($conn[$filename], CURLOPT_FILE, $fp[$filename]);
    				curl_setopt($conn[$filename], CURLOPT_HEADER, 0);
    				curl_setopt($conn[$filename], CURLOPT_CONNECTTIMEOUT, 60);
    				curl_multi_add_handle($mh, $conn[$filename]);
    			}
    		}
    
    		do
    		{
    			$n = curl_multi_exec($mh, $active);
    		}while($active);
    
    		foreach($nameurls as $filename=>$url)
    		{
    			curl_multi_remove_handle($mh, $conn[$filename]);
    			curl_close($conn[$filename]);
    			fclose($fp[$filename]);
    		}
    	}
    	curl_multi_close($mh);exit;
    }

}

