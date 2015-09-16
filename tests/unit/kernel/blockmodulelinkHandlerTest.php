<?php
require_once(dirname(__FILE__).'/../init_new.php');

require_once(XOOPS_TU_ROOT_PATH . '/kernel/blockmodulelink.php');

/**
* PHPUnit special settings :
* @backupGlobals disabled
* @backupStaticAttributes disabled
*/
class legacy_blockmodulelinkHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $conn = null;

    public function setUp()
    {
        $this->conn = Xoops::getInstance()->db();
    }

    public function test___construct()
    {
        $instance=new \XoopsBlockmodulelinkHandler($this->conn);
        $this->assertInstanceOf('\Xoops\Core\Kernel\Handlers\XoopsBlockmodulelinkHandler', $instance);
    }
}
