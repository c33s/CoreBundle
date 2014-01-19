<?php
namespace c33s\CoreBundle\Tests\Tools;

use c33s\CoreBundle\Tools\Tools;

class CropFileByLineTest extends \PHPUnit_Framework_TestCase
{
    protected function getData()
    {
        return $data = array(
            'My Line Number One',
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
    }
    
    public function testAllData()
    {
        $data = $this->getData();
        
        $result = Tools::cropFileByLine($data, false, false, false, false);
        $expected = $data;
        
        $this->assertEquals($expected, $result);
    }
    
    public function testTrueEndPattern()
    {
        $data = $this->getData();
        $expected = array(
            'My Line Number One',
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            //'My Line Number Six',
        );       
        $result = Tools::cropFileByLine($data,true, "Six", false, false);
        $this->assertEquals($expected, $result);
    }
    
    public function testStartPatternBeginning()
    {
        $data = $this->getData();
        
        $expected = array(
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"One", false, false, false);
        $this->assertEquals($expected, $result);
        
        $expected = array(
            'My Line Number One',
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"One", false, true, false);
        $this->assertEquals($expected, $result);

        $expected = array(
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"One", false, false, true);
        $this->assertEquals($expected, $result);

        $expected = array(
            'My Line Number One',
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"One", false, true, true);
        $this->assertEquals($expected, $result);
    }
    
    public function testStartPatternMiddle()
    {
        $data = $this->getData();
        
        $expected = array(
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"Four", false, false, false);
        $this->assertEquals($expected, $result);
        
        $expected = array(
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"Four", false, true, false);
        $this->assertEquals($expected, $result);

        $expected = array(
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"Four", false, true, true);
        $this->assertEquals($expected, $result);
    }
    
    public function testStartPatternEnd()
    {
        $data = $this->getData();
        
        $expected = array(
        );
        $result = Tools::cropFileByLine($data,"Ten", false, false, false);
        $this->assertEquals($expected, $result);
        
        $expected = array(
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"Ten", false, true, false);
        $this->assertEquals($expected, $result);

        $expected = array(
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"Ten", false, true, true);
        $this->assertEquals($expected, $result);
    }
    
    public function testStartEndPatternBeginning()
    {
        $data = $this->getData();
        
        $expected = array(
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
        );
        $result = Tools::cropFileByLine($data,"One", "Ten", false, false);
        $this->assertEquals($expected, $result);
        
        $expected = array(
            'My Line Number One',
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
        );
        $result = Tools::cropFileByLine($data,"One", "Ten", true, false);
        $this->assertEquals($expected, $result);

        $expected = array(
            'My Line Number One',
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"One", "Ten", true, true);
        $this->assertEquals($expected, $result);
    }   

    public function testStartEndPatternMiddle()
    {
        $data = $this->getData();
        
        $expected = array(
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',

        );
        $result = Tools::cropFileByLine($data,"Four", "Eight", false, false);
        $this->assertEquals($expected, $result);
        
        $expected = array(
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
        );
        $result = Tools::cropFileByLine($data,"Four", "Eight", true, false);
        $this->assertEquals($expected, $result);

        $expected = array(
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
        );
        $result = Tools::cropFileByLine($data,"Four", "Eight", false, true);
        $this->assertEquals($expected, $result);

        $expected = array(
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
        );
        $result = Tools::cropFileByLine($data,"Four", "Eight", true, true);
        $this->assertEquals($expected, $result);
    }
        
    public function testStartEndPatternEnd()
    {
        $data = $this->getData();
        
        $expected = array(
        );
        $result = Tools::cropFileByLine($data,"Ten", "Ten", false, false);
        $this->assertEquals($expected, $result);
        
        $expected = array(
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"Ten", "Ten", true, false);
        $this->assertEquals($expected, $result);

        $expected = array(
            //'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"Ten", "Ten", false, true);
        $this->assertEquals($expected, $result);
        
        $expected = array(
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($data,"Ten", "Ten", true, true);
        $this->assertEquals($expected, $result);
    }
    
    public function testInvert()
    {
        $data = $this->getData();
        
        $expected = array(
            'My Line Number One',
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            //'My Line Number Six',
            //'My Line Number Seven',
            //'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        
        $result = Tools::cropFileByLine($data, "Five", "Eight", false, false,true);
        $expected = $data;
        
        $this->assertEquals($expected, $result);
    }
}