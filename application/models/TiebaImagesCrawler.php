<?php

class Application_Model_TiebaImagesCrawler
{
    private $_list_url;
    private $_post_type;
    private $_gallery_url_tpl;
    private $_image_url_tpl;
    private $_file_dir_tpl;
    private $_filename_tpl;
    private $_tieba_name;
    
    public function __construct($tieba_name, $save_dir)
    {
        Application_Model_File::checkDirExist($save_dir);
        //抓取类型 0-按照帖子顺序 1-按照贴图顺序
        $this->_post_type = 0;
        //列表页url
        $list_url_tpl = "http://tieba.baidu.com/f?kw=%s" . ($this->_post_type ? "&tp=1" : "&pn=");
        //图册页url
        $this->_gallery_url_tpl = "http://tieba.baidu.com/photo/bw/picture/guide?kw=%s&tid=%s&next=9999";
        //图片url
        $this->_image_url_tpl = "http://imgsrc.baidu.com/forum/pic/item/%s.jpg";
        //帖子子文件夹
        $this->_file_dir_tpl = $save_dir . "%s/";
        //图片文件
        $this->_filename_tpl = $save_dir . "%s/%s.jpg";
        $this->_tieba_name = $tieba_name;
        
        $this->_list_url = sprintf($list_url_tpl, $tieba_name);
    }
    
    //reference url:http://www.oschina.net/code/snippet_1023084_19836
    //just copy and paste and reconstruct
    public function crawlerImages()
    {
    	$pn = 0;
    	while(1)
    	{
    		if (!$this->_post_type)
    		{
    		    $this->_list_url .= $pn;
    		}
    		
    		$list_content = file_get_contents($this->_list_url);
    		if($list_content === false || $list_content == '')
    		{
    			continue;
    		}
    		$id_regex = $this->_post_type ? Application_Model_CrawlerRegex::TIEBA_POST_ID_PICTURE_ORDER : Application_Model_CrawlerRegex::TIEBA_POST_ID_POST_ORDER;
			$match_id_count = preg_match_all($id_regex, $list_content, $id_list_matches);
			
			if($match_id_count !== false && $match_id_count !== 0)
			{
        		foreach($id_list_matches[1] as $tid)
        		{
        			$gallery_url = sprintf($this->_gallery_url_tpl, $this->_tieba_name, $tid);
        			$gallery_content = file_get_contents($gallery_url);
        			if($gallery_content === false || $gallery_content == '')
        			{
        				continue;
        			}
        			$match_id_count = preg_match_all(Application_Model_CrawlerRegex::TIEBA_PICTURE_TID, $gallery_content, $pid_list_matches);
        			
        			if($match_id_count !== false && $match_id_count !== 0)
        			{
                        foreach($pid_list_matches[1] as $pid)
                        {
                            $filedir = sprintf($this->_file_dir_tpl, $tid);
                        	$filename = sprintf($this->_filename_tpl, $tid, $pid);
                        	
                        	if(!is_file($filename))
                        	{
                            	$imageurl = sprintf($this->_image_url_tpl, $pid);
                            	$image_content = file_get_contents($imageurl);
                            	if($image_content != '')
                            	{
                                	Application_Model_File::checkDirExist($filedir);
                                	file_put_contents($filename, $image_content);
                            	}
                        	}
                        }
        			}
                }
			}
            
            //翻到下一页
            if (!$this->_post_type)
            {
            	$pn += 50;
            }
        }
    }
}

