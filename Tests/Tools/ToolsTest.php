<?php
namespace c33s\CoreBundle\Tests\Tools;

use c33s\CoreBundle\Tools\Tools;

class ToolsTest extends \PHPUnit_Framework_TestCase
{
    public function testFilesizeToBytes()
    {
        $result = Tools::filesizeToBytes('10 MB');
        $this->assertEquals(1024 * 1024 *10, $result);

        $result = Tools::filesizeToBytes('200.20 GB');
        $this->assertEquals(1024 * 1024 * 1024 * 200.20, $result);
    }
}