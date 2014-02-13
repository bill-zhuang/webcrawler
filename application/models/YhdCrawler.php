<?php

class Application_Model_YhdCrawler
{
    private $web_url = '';
    private $save_dir = '';
    
    private $images_url = array();
    private $commodities_url = array();
    
    public function __construct($url, $save_images_dir)
    {
    	$this->web_url = $url;
    	$this->save_dir = $save_images_dir;
    }
    
    public function runCrawler()
    {
    	if ($this->web_url == '')
    	{
    		Application_Model_CrawlerLog::writeUrlLog($this->web_url, " crawler url {$this->web_url} is empty!" . "\r\n");
    		return;
    	}
    
    	$this->_initCrawler();
    	$this->_crawler($this->web_url);
    }
    
    private function _initCrawler()
    {
    	Application_Model_File::checkDirExist($this->save_dir);
    	$this->images_url = array();
    	$this->commodities_url = array();
    }
    
    private function _crawler($url, $is_ctg_chars_exist_in_url = true)
    {
    	$column_content = file_get_contents($url);
    
    	if($column_content === false || $column_content == null)
    	{
    		Application_Model_CrawlerLog::writeUrlLog($this->web_url, " Fail to get {$url} content or {$url} content is empty" . "\r\n");
    	}
    	else
    	{
    		$choose_regex = $is_ctg_chars_exist_in_url ? Application_Model_CrawlerRegex::YHD_SITE_COLUMN_REGEX : Application_Model_CrawlerRegex::YHD_SITE_COLUMN_CATEGORY_REGEX;
    		$match_count = preg_match_all($choose_regex, $column_content, $page_url_matches);
    		//echo '<pre>';print_r($page_url_matches[1]); exit;
    
    		if($match_count)
    		{
    			//remove duplicate array values.
    			$page_url_matches[1] = array_unique($page_url_matches[1]);
    
    			foreach($page_url_matches[1] as $page_url)
    			{
    				$page_url = str_replace(array("\r\n", "\r", "\n", ' ', "\t"), '', $page_url);
    				if(strpos($page_url, 'ctg') !== false)
    				{
    					$this->_writeCategoryUrl($is_ctg_chars_exist_in_url ? "\r\n" . $page_url : 'Recursive: ' . $page_url);
    
    					$start_page_content = file_get_contents($page_url);
    
    					$total_pages_count = preg_match(Application_Model_CrawlerRegex::YHD_COMMODITIES_TOTAL_PAGES_REGEX, $start_page_content, $total_pages_matches);
    					$start_page_count = preg_match(Application_Model_CrawlerRegex::YHD_REAL_URL_REGEX, $start_page_content, $start_page_url_matches);
    					$total_commodities_count = preg_match(Application_Model_CrawlerRegex::YHD_COMMODITIES_TOTAL_COUNT, $start_page_content, $total_commodities_matches);
    					/* echo '<br> total commodities count: ' . $total_commodities_matches[1] . '<br>';
    					echo '<br> total pages:' . $total_pages_matches[1]; 
    					echo '<br> start page: ' . $start_page_url_matches[1] . '<br>';
    					exit; */
    
    					if($total_pages_count && $start_page_count)
    					{
    						$this->_generateCommoditiesInOneKindsUrls($start_page_url_matches[1], $total_pages_matches[1]);
    						//echo '<pre>'; print_r($this->images_url);exit;
    						Application_Model_Download::byCurlMultiple($this->images_url, $this->save_dir);
    						//exit;
    					}
    					else
    					{
    						$error_msg = '';
    						$error_msg .= ($total_pages_count === false) ? ' 1.total pages match failed; ' : ' 1.total pages match is 0; ';
    						$error_msg .= ($start_page_count === false) ? ' 2.real url match failed; ' : ' 2.real url match is 0; ';
    						Application_Model_CrawlerLog::writeUrlLog($this->web_url, " {$page_url} error message: {$error_msg}" . "\r\n");
    					}
    				}
    				else
    				{
    					//bug here, comment
    					/* if($page_url != '')
    					{
    						$this->_crawler($page_url, false);
    					} */
    				}
    			}
    		}
    	}
    }
    
    private function _generateCommoditiesInOneKindsUrls($start_page_url, $total_pages)
    {
    	$url_with_page_num = '';
    
    	for($i = 1; $i <= $total_pages; $i++)
    	{
        	$url_with_page_num = (($i == 1) ? $start_page_url : str_replace('-v0-p1-price-', "-v0-p{$i}-price-", $start_page_url));
        	//echo $url_with_page_num . '<br>';exit;
        	$this->_crawlerCommoditiesUrlInOneWebPage($url_with_page_num);
        
        	list($more_products_url, $json_key) = $this->_getMoreProductsUrl($url_with_page_num);
        	$this->_crawlerCommoditiesUrlInOneWebPage($more_products_url, true, $json_key);
    	}
    
    	/* echo 'total count(remove duplicate urls): ' . count($this->commodities_url) . '<br>';
    	echo '<pre>';print_r($this->commodities_url);
    	exit; */
	}
	
	private function _crawlerCommoditiesUrlInOneWebPage($page_url, $is_more_products_url = false, $json_key = '')
	{
		$merchants_content = file_get_contents($page_url);
	
		if($merchants_content === false || $merchants_content == null)
		{
			Application_Model_CrawlerLog::writeUrlLog($this->web_url, " Fail to get {$page_url} content or {$page_url} content is empty" . "\r\n");
		}
		else
		{
			$commodities_urls_regex = '';
			if($is_more_products_url)
			{
				$commodities_urls_regex = Application_Model_CrawlerRegex::YHD_COMMODITIES_URLS_REGEX;
				$merchants_content = print_r(json_decode(substr($merchants_content, strlen($json_key) + 1, strlen($merchants_content) - strlen($json_key) - 2)), true);
				//print_r($merchants_content);
			}
			else
			{
				$commodities_urls_regex = Application_Model_CrawlerRegex::YHD_COMMODITIES_URLS_REGEX;
			}
	
			$match_count = preg_match_all($commodities_urls_regex, $merchants_content, $commodity_urls_matches);
			//var_dump($match_count);echo '<br>'; //echo '<pre>'; print_r($commodity_url_image_matches[1]); exit;
	
			//available for $is_more_products_url = false
			$get_more_products_count = preg_match_all(Application_Model_CrawlerRegex::YHD_GET_MORE_PRODUCTS, $merchants_content, $get_more_products_matches);
			//var_dump($get_more_products_count);var_dump($get_more_products_matches[1]);echo '<br>';
	
			if($match_count)
			{
				foreach($commodity_urls_matches[1] as $commodity_url)
				{
					if(array_key_exists($commodity_url, $this->commodities_url))
					{
						continue;
					}
					else
					{
						$this->commodities_url[] = $commodity_url;
						$this->_crawlerCommodityImagesUrls($commodity_url);
					}
				}
			}
			else
			{
				Application_Model_CrawlerLog::writeUrlLog($this->web_url, " {$page_url} content doesn't find commodity url." . "\r\n");
			}
		}
	}
	
	private function _crawlerCommodityImagesUrls($commodity_url)
	{
		$commodity_content = file_get_contents($commodity_url);
	
		if($commodity_content === false || $commodity_content == null)
		{
			Application_Model_CrawlerLog::writeUrlLog($this->web_url, " Fail to get {$commodity_url} content or {$commodity_url} content is empty" . "\r\n");
		}
		else
		{
			preg_match_all(Application_Model_CrawlerRegex::YHD_IMG_450_REGEX, $commodity_content, $img_450_matches);
			preg_match_all(Application_Model_CrawlerRegex::YHD_IMG_600_REGEX, $commodity_content, $img_600_matches);
	
			if($img_450_matches)
			{
				preg_match(Application_Model_CrawlerRegex::FILENAME_REGEX, $img_450_matches[1][0], $filename_match);
				$this->images_url[$filename_match[1]] = $img_450_matches[1][0];
			}
			else
			{
				Application_Model_CrawlerLog::writeUrlLog($this->web_url, " {$commodity_url} content doesn't find image 450 url." . "\r\n");
			}
	
			if($img_600_matches)
			{
				preg_match(Application_Model_CrawlerRegex::FILENAME_REGEX, $img_600_matches[1][0], $filename_match);
				$this->images_url[$filename_match[1]] = $img_600_matches[1][0];
			}
			else
			{
				Application_Model_CrawlerLog::writeUrlLog($this->web_url, " {$commodity_url} content doesn't find image 600 url." . "\r\n");
			}
		}
	}
	
	private function _getMoreProductsUrl($page_url)
	{
		$current_time = explode(" ", microtime());
		$current_time = $current_time[1] . ($current_time[0] * 1000);
		$current_time = explode(".", $current_time);
		$current_time = $current_time[0];
	
		//http://www.yhd.com/ctg/s2        /c22882-0/b/a-s1-v0-p1-price-d0-f0-m1-rt0-pid-mid0-k/
		//http://www.yhd.com/ctg/searchPage/c22882-0/b/a-s1-v0-p1-price-d0-f0-m1-rt0-pid-mid0-k/?callback=jsonp1390803213812&isGetMoreProducts=1&moreProductsDefaultTemplate=0
		//http://www.yhd.com/ctg/searchPage/c22882-0/b/a-s1-v0-p2-price-d0-f0-m1-rt0-pid-mid0-k/?callback=jsonp1390803610851&isGetMoreProducts=1&moreProductsDefaultTemplate=0
		$more_products_query = "?callback=jsonp{$current_time}&isGetMoreProducts=1&moreProductsDefaultTemplate=0";
	
		$more_products_url = preg_replace('!ctg/[^/]+/!', 'ctg/searchPage/', $page_url);
	
		return array($more_products_url . $more_products_query, 'jsonp' . $current_time);
	}

}

