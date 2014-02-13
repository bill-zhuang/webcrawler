<?php

class Application_Model_CrawlerRegex
{
    const YHD_SITE_COLUMN_REGEX = '/data\-ref\s*=\s*"CatMenu_Site[^"]+"\s+target\s*=\s*"_blank"\s+href\s*=\s*"([^"]+)"/';
    const YHD_SITE_COLUMN_FIX_REGEX = '/(?>data\-ref\s*=\s*"CatMenu_Site[^"]+"\s+target\s*=\s*"_blank"\s+href\s*=\s*")([^"]+)"/';
    const YHD_SITE_COLUMN_CATEGORY_REGEX = '/<(?:dd>|\/a>)\s*<a\s+href\s*=\s*"([^"]+)"\s+target\s*=\s*"_blank"\s+tk\s*=\s*"CatMenu_Site/';
    /*
     * <dd><a href
    * </a><a href
    * <dd><span><a href
    * </a></span><span><a href
    * <div class="menu_links clearfix"><a href
    * </a><a href
    * <div class="cate_child"><a href
    * </a><a href
    * <li class="fl_dl_item"><a href
    * .....
    */
    //const YHD_SITE_COLUMN_CATEGORY_REGEX = '/<(?:dd><span>|\/a><\/span><span>)\s*<a\s+href\s*=\s*"([^"]+)"\s+target\s*=\s*"_blank"\s+tk\s*=\s*"CatMenu_Site/';
    //const YHD_SITE_COLUMN_CATEGORY_REGEX = '/<a\s+href\s*=\s*"([^"]+)"\s+target\s*=\s*"_blank"\s+tk\s*=\s*"CatMenu_Site/';
    const YHD_REAL_URL_REGEX = '/<input\s+id="searchUrl"\s+type\s*=\s*"hidden"\s+value\s*=\s*"([^"]+)"/';
    const YHD_COMMODITIES_URLS_REGEX = '/<a\s+class\s*=\s*"search_prod_img"\s+[^h]+href="([^"]+)"\s+target\s*=\s*"_blank"\s+onClick="addTrackPositionToCookie/';
    const YHD_COMMODITIES_TOTAL_PAGES_REGEX = '/<a\s+id\s*=\s*"lastPage"[^>]+>(\d+)<\/a>/';
    const YHD_COMMODITIES_TOTAL_COUNT = '/"productCount"\s*:\s*"\s*(\d+)\s*",/';
    const YHD_GET_MORE_PRODUCTS = '/<div\s+id\s*=\s*"getMoreProducts"\s+style\s*=\s*"clear:both;"\s*>([^<]*)<\/div>/';
    
    const YHD_IMG_200_REGEX = '/<img\s+width\s*=\s*"200"\s+height\s*=\s*"200"\s+src="([^"]+)"/';
    
    const YHD_IMG_600_REGEX = '/<div\s+class\s*=\s*"zoom"\s+id\s*=\s*"J_zoom"><img\s+alt\s*=\s*""\s+src\s*="([^"]+)"/';
    const YHD_IMG_450_REGEX = '/<img\s+id=\s*"J_prodImg"\s+src\s*=\s*"([^"]+)"/';
    const YHD_IMG_60_REGEX = '//';
    
    const FILENAME_REGEX = '!([^/]+)$!';

}

