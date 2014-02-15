<?php

class IndexController extends Zend_Controller_Action
{
    private $_crawler_save_dir;
    public function init()
    {
        /* Initialize action controller here */
        set_time_limit(0);
    }

    public function indexAction()
    {
        // action body
        /* $crawler_init_url = 'http://www.yhd.com';
        $this->_crawler_save_dir = 'D:/crawler_yhd/';
        $yhd_crawler = new Application_Model_YhdCrawler($crawler_init_url, $this->_crawler_save_dir);
        $yhd_crawler->runCrawler(); */
        
        //tieba name
        $crawler_tieba_name = "%CD%BC%C6%AC";
        $this->_crawler_save_dir = 'D:/crawler_tieba/';
        $tieba_crawler = new Application_Model_TiebaImagesCrawler($crawler_tieba_name, $this->_crawler_save_dir);
        $tieba_crawler->crawlerImages();
    }


}

