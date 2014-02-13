<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        set_time_limit(0);
    }

    public function indexAction()
    {
        // action body
        $crawler_init_url = 'http://www.yhd.com';
        $crawler_save_dir = 'D:/crawler_yhd/';
        $yhd_crawler = new Application_Model_YhdCrawler($crawler_init_url, $crawler_save_dir);
        $yhd_crawler->runCrawler();
    }


}

