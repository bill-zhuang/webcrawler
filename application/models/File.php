<?php

class Application_Model_File
{
    public static function checkDirExist($dir)
    {
    	if (!file_exists($dir))
    	{
    		mkdir($dir, 0777, true);
    	}
    }

}

