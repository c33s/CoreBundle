<?php
namespace C33s\CoreBundle\Tests\Tools;

use C33s\CoreBundle\Tools\Tools;

class CropFileByLineTest extends \PHPUnit_Framework_TestCase
{
    protected $data;
    
    protected function setUp()
    {
        $this->data = array(
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
    

//    
//    //cropFileByLine($file, $startLinePattern = false, $endLinePattern = false, $includeStart = false, $includeEnd = false, $invert = false)
//    
    public function testCropNothing()
    {
        $result   = Tools::cropFileByLine($this->data, false, false, 0, 0);
        $expected = $this->data;
        
        $this->assertEquals($expected, $result);
    }
    
    public function testCropAllUntilSix()
    {
        $expected = array(
            'My Line Number One',
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
        );       
        $result = Tools::cropFileByLine($this->data, false, "Six", 0, 0);
        $this->assertEquals($expected, $result);
    }
    
    public function testCropAllStartingWithOne()
    {
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
        $result = Tools::cropFileByLine($this->data, "One", false, 1, 0);
        $this->assertEquals($expected, $result);
    }
    
    
    
    public function testCropAllStartingWithOneIncludingOne()
    {
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
	
        $result = Tools::cropFileByLine($this->data, "One", false, 0, 0);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage End beyond line count.
     */
    public function testCropAllStartingWithOneIncludingOneAndEndOffsetBeyond()
    {
        $result = Tools::cropFileByLine($this->data, "One", false, 0, 1);
    }
    
    public function testCropAllStartingWithOneStartOffsetOne()
    { 

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
        $result = Tools::cropFileByLine($this->data, "One", false, 1, 0);
        $this->assertEquals($expected, $result);
    }
    
    public function testCropAllStartWithFour()
    {
        $expected = array(
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($this->data, "Four", false, 0, 0);
        $this->assertEquals($expected, $result);
    }
    
    public function testCropAllStartWithFourOffsetOne()
    {
        $expected = array(
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($this->data, "Four", false, 1, 0);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage End beyond line count.
     */
    public function testCropAllStartWithFourOffsetOneOne()
    {
        $result = Tools::cropFileByLine($this->data, "Four", false, 1, 1);
    }
    
    public function testCropAllStartWithFourOffsetOneMinusOne()
    {
        $expected = array(
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
        );
        $result = Tools::cropFileByLine($this->data, "Four", false, 0, -1);
        $this->assertEquals($expected, $result);
    }
    public function testCropStartWithFourOffsetMinusTwoMinusTwo()
    {
        $expected = array(
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
        );
        $result = Tools::cropFileByLine($this->data, "Four", false, -2, -2);
        $this->assertEquals($expected, $result);
    }
    
    public function testCropStartWithFourOffsetMinusTwoMinusSix()
    {
        $expected = array(
            'My Line Number Four',
            //'My Line Number Five',
            //'My Line Number Six',
            //'My Line Number Seven',
            //'My Line Number Eight',
        );
        $result = Tools::cropFileByLine($this->data, "Four", false, 0, -6);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Length cannot be negative or zero (length: 0, start: 3, end: 3).
     */
    public function testCropStartWithFourOffsetMinusTwoMinusSeven()
    {
        $expected = array(
            //'My Line Number Four',
            //'My Line Number Five',
            //'My Line Number Six',
            //'My Line Number Seven',
            //'My Line Number Eight',
        );
        $result = Tools::cropFileByLine($this->data, "Four", false, 0, -7);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Length cannot be negative or zero (length: -1, start: 3, end: 2).
     */
    public function testCropStartWithFourOffsetMinusTwoMinusEight()
    {
        $result = Tools::cropFileByLine($this->data, "Four", false, 0, -8);
    }
    
    
    public function testCropAllStartWithFourOffsetOneMinusTwo()
    {
        $expected = array(
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
        );
        $result = Tools::cropFileByLine($this->data, "Four", false, 0, -2);
        $this->assertEquals($expected, $result);
    }
    public function testCropAllStartWithFourOffsetOneMinusThree()
    {
        $expected = array(
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
        );
        $result = Tools::cropFileByLine($this->data, "Four", false, 0, -3);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Length cannot be negative or zero (length: 0, start: 10, end: 10).
     */
    public function testStartPatternEndOffsetOne()
    {
        $result = Tools::cropFileByLine($this->data, "Ten", false, 1, 0);
    }
    
    public function testStartPatternEndNoOffset()
    {
        $expected = array(
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($this->data, "Ten", false, 0, 0);
        $this->assertEquals($expected, $result);
    }
	

    
    
    public function testStartEndPatternBeginningToEndCropped()
    {
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
        $result = Tools::cropFileByLine($this->data, "One", "Ten", 1, -1);
        $this->assertEquals($expected, $result);
    }   
        
    public function testStartEndPatternBeginningEndNegativeOffset()
    {
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
        $result = Tools::cropFileByLine($this->data, "One", "Ten", 0, -1);
        $this->assertEquals($expected, $result);
    }   

    public function testStartEndPatternBeginningToEnd()
    {
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
        $result = Tools::cropFileByLine($this->data, "One", "Ten", 0, 0);
        $this->assertEquals($expected, $result);
    }   

    public function testStartEndPatternMiddle()
    {
        $expected = array(
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',

        );
        $result = Tools::cropFileByLine($this->data, "Four", "Eight", 1, -1);
        $this->assertEquals($expected, $result);
    }
    
    public function testStartEndPatternOffsetOneEndOffsetZero()
    {    
        $expected = array(
            //'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
        );
        $result = Tools::cropFileByLine($this->data, "Four", "Eight", 1, 0);
        $this->assertEquals($expected, $result);
    }
    
    public function testStartEndPatternOffsetOneEndOffsetMinusOne()
    {    
        $expected = array(
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
        );
        $result = Tools::cropFileByLine($this->data, "Four", "Eight", 1, -1);
        $this->assertEquals($expected, $result);
    }
    
    public function testStartEndPatternOffsetZeroEndOffsetOne()
    {    
        $expected = array(
            'My Line Number Four',
            'My Line Number Five',
            'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
        );
        $result = Tools::cropFileByLine($this->data, "Four", "Eight", 0, 1);
        $this->assertEquals($expected, $result);
    }
    
    
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage End line pattern "Ten" not found.
     */  
    public function testStartEndPatternEnd()
    {
        $expected = array(
            'My Line Number Ten',
        );
	
        $result = Tools::cropFileByLine($this->data, "Ten", "Ten", 0, -1);
        $this->assertEquals($expected, $result);

    }
    
   
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage End line pattern "Ten" not found.
     */
    public function testStartEndPatternEndException5()
    {
        $result = Tools::cropFileByLine($this->data, "Ten", "Ten", 0, 1);
        
    }
    
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage End line pattern "Ten" not found.
     */
    public function testStartEndPatternEndException4()
    {
        $result = Tools::cropFileByLine($this->data, "Ten", "Ten", 0, 0);
    }
    
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage End line pattern "Ten" not found.
     */
    public function testStartEndPatternEndException()
    {
        $result = Tools::cropFileByLine($this->data, "Ten", "Ten", 1, 0);
    }
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage End line pattern "Ten" not found.
     */
    public function testStartEndPatternEndException2()
    {
        $result = Tools::cropFileByLine($this->data, "Ten", "Ten", 1, 1);
    }
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage End line pattern "Ten" not found.
     */
    public function testStartEndPatternStartException()
    {
        $result = Tools::cropFileByLine($this->data, "Ten", "Ten", 1, 0);
    }
   
    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Start line pattern "Eleven" not found.
     */
    public function testStartEndPatternStartException3()
    {
        $result = Tools::cropFileByLine($this->data, "Eleven", false, 0, 0);
	//var_dump($result);
    }
    
    public function testInvertFiveToEight()
    {
        $expected = array(
            'My Line Number One',
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            //'My Line Number Five',
            //'My Line Number Six',
            //'My Line Number Seven',
            //'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($this->data, "Five", "Eight", 0, 0, true);
        $this->assertEquals($expected, $result);
    }
    
    public function testInvertOneToTen()
    {
        $expected = array(
            //'My Line Number One',
            //'My Line Number Two',
            //'My Line Number Three',
            //'My Line Number Four',
            //'My Line Number Five',
            //'My Line Number Six',
            //'My Line Number Seven',
            //'My Line Number Eight',
            //'My Line Number Nine',
            //'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($this->data, "One", "Ten", 0, 0, true);
        $this->assertEquals($expected, $result);
    }
    public function testInvertOneToNoneOffsetOneZero()
    {
        $expected = array(
            'My Line Number One',
            //'My Line Number Two',
            //'My Line Number Three',
            //'My Line Number Four',
            //'My Line Number Five',
            //'My Line Number Six',
            //'My Line Number Seven',
            //'My Line Number Eight',
            //'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($this->data, "One", "Nine", 1, 0, true);
        $this->assertEquals($expected, $result);
    }
    
    public function testInvertOneToNoneOffsetOneOne()
    {
        $expected = array(
            'My Line Number One',
            //'My Line Number Two',
            //'My Line Number Three',
            //'My Line Number Four',
            //'My Line Number Five',
            //'My Line Number Six',
            //'My Line Number Seven',
            //'My Line Number Eight',
            //'My Line Number Nine',
            //'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($this->data, "One", "Nine", 1, 1, true);
        $this->assertEquals($expected, $result);
    }
    
    public function testInvertFiveToSixOffsetOneOne()
    {
        $expected = array(
            'My Line Number One',
            'My Line Number Two',
            'My Line Number Three',
            'My Line Number Four',
            //'My Line Number Five',
            //'My Line Number Six',
            'My Line Number Seven',
            'My Line Number Eight',
            'My Line Number Nine',
            'My Line Number Ten',
        );
        $result = Tools::cropFileByLine($this->data, "Five", "Six", 0, 0, true);
        $this->assertEquals($expected, $result);
    }
    
    public function testInvertFiveToSixOffsetOneMinusOne()
    {
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
        $result = Tools::cropFileByLine($this->data, "Five", "Six", 1, -1, true);
        $this->assertEquals($expected, $result);
    }
}
