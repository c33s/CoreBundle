<?php
namespace C33s\CoreBundle\Tests\Tools;

use C33s\CoreBundle\Tools\Tools;

class stringPosInArrayTest extends \PHPUnit_Framework_TestCase
{
    protected $data;

    protected function setUp()
    {
        $this->data = array(
            'My Line Number One', //0
            'My Line Number Two', //1
            'My Line Number Three', //2
            'My Line Number Four', //3
            'My Line Number Five', //4
            'My Line Number Six', //5
            'My Line Number Seven', //6
            'My Line Number Eight', //7
            'My Line Number Nine', //8
            'My Line Number Ten', //9
            'My Line Number One Again', //10
            'My Line Number Two Again', //11
            'My Line Number Three Again', //12
            'My Line Number Four Again', //13
            'My Line Number Five Again', //14
        );
    }

    //Tools::stringPosInArray($array,$string,$offset = 0, $trim = true)
    public function testEmptyData()
    {
	$result = Tools::stringPosInArray(array(), "Not Relevant");
	$this->assertEquals(false, $result);
    }
    public function testNullData()
    {
	$result = Tools::stringPosInArray(null, "Not Relevant");
	$this->assertEquals(false, $result);
    }
    public function testFalseData()
    {
	$result = Tools::stringPosInArray(false, "Not Relevant");
	$this->assertEquals(false, $result);
    }



    public function testEmptyString()
    {
	$result = Tools::stringPosInArray($this->data, "");
	$this->assertEquals(false, $result);
    }
    public function testNullString()
    {
	$result = Tools::stringPosInArray($this->data, null);
	$this->assertEquals(false, $result);
    }
    public function testFalseString()
    {
	$result = Tools::stringPosInArray($this->data, false);
	$this->assertEquals(false, $result);
    }



    public function testTrimFalse()
    {
	$result = Tools::stringPosInArray($this->data, "\n\n\t\t Two \n\n\n\n\t", 0, false);
	$this->assertEquals(false, $result);
    }   
    public function testTrimTrue()
    {
	$result = Tools::stringPosInArray($this->data, "\n\n\t\t Two \n\n\n\n\t", 0, true);
	$this->assertEquals(true, $result);
    }   



    public function testMissing()
    {
	$result = Tools::stringPosInArray($this->data, "Missing");
	$this->assertEquals(false, $result);
    }
    public function testFirstByNumber()
    {
	$result = Tools::stringPosInArray($this->data, "Number");
	$this->assertEquals(0, $result);
    }
    public function testSecond()
    {
	$result = Tools::stringPosInArray($this->data, "Two");
	$this->assertEquals(1, $result);
    }
    public function testFirst()
    {
	$result = Tools::stringPosInArray($this->data, "One");
	$this->assertEquals(0, $result);
    }
    public function testTenth()
    {
	$result = Tools::stringPosInArray($this->data, "Ten");
	$this->assertEquals(9, $result);
    }



    public function testFifthOfsettTwo()
    {
	$result = Tools::stringPosInArray($this->data, "Number", 2);
	$this->assertEquals(2, $result);
    }
    public function testFifthOfsettNine()
    {
	$result = Tools::stringPosInArray($this->data, "Three", 9);
	$this->assertEquals(12, $result);
    }
    public function testFifthOfsettEleven()
    {
	$result = Tools::stringPosInArray($this->data, "Three", 11);
	$this->assertEquals(12, $result);
    }
    public function testFifthOfsettTwelve()
    {
	$result = Tools::stringPosInArray($this->data, "Three", 12);
	$this->assertEquals(12, $result);
    }
    public function testFifthOfsettThirteen()
    {
	$result = Tools::stringPosInArray($this->data, "Three", 13);
	$this->assertEquals(false, $result);
    }


}
