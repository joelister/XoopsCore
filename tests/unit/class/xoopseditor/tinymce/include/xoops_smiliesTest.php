<?php
require_once(dirname(__FILE__).'/../../../../init_new.php');

/**
* PHPUnit special settings :
* @backupGlobals disabled
* @backupStaticAttributes disabled
*/
class Xoops_smiliesTest extends \PHPUnit\Framework\TestCase
{

    public function test_100()
    {
        $xoops_root_path = \XoopsBaseConfig::get('root-path');
		ob_start();
		require_once ($xoops_root_path.'/class/xoopseditor/tinymce/include/xoops_smilies.php');
		$x = ob_end_clean();
		$this->assertTrue((bool)$x);
		
    }
}
