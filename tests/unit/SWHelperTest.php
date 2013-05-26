<?php
class SWHelperTest extends CDbTestCase {
	/**
	 *
	 * @see test/CDbTestCase::setUp()
	 */
	protected function setUp()
	{
		parent::setUp();
	}
	/**
	 *
	 */
	public function test_01() {
		$m= new Model01();
		
		$ar=SWHelper::nextStatuslistData($m);
		
		$this->assertTrue(is_array($ar));
		$this->assertEquals(count($ar),3);
		$this->assertTrue(isset($ar['workflow1/S1']));
		$this->assertTrue(isset($ar['workflow1/S2']));
		$this->assertTrue(isset($ar['workflow1/S3']));
		
		$this->assertEquals($ar['workflow1/S1'],'status S1');
		$this->assertEquals($ar['workflow1/S2'],'S2');
		$this->assertEquals($ar['workflow1/S3'],'status S3');
	}
	
	public function test_02() {
		$m=new Model01();
		// exclude status as array
		$ar=SWHelper::nextStatuslistData($m,array('exclude'=>array('workflow1/S2')));
		
		$this->assertTrue(is_array($ar));
		$this->assertEquals(count($ar),2);
		$this->assertTrue(isset($ar['workflow1/S1']));
		$this->assertTrue(isset($ar['workflow1/S3']));
		
		$this->assertEquals($ar['workflow1/S1'],'status S1');
		$this->assertEquals($ar['workflow1/S3'],'status S3');
		
		// exclude status as string
		$ar=SWHelper::nextStatuslistData($m,array('exclude'=>'workflow1/S2'));
		
		$this->assertTrue(is_array($ar));
		$this->assertEquals(count($ar),2);
		$this->assertTrue(isset($ar['workflow1/S1']));
		$this->assertTrue(isset($ar['workflow1/S3']));
		
		$this->assertEquals($ar['workflow1/S1'],'status S1');
		$this->assertEquals($ar['workflow1/S3'],'status S3');
		
		// exclude status as string with no workflow id
		$ar=SWHelper::nextStatuslistData($m,array('exclude'=>'S2'));
		
		$this->assertTrue(is_array($ar));
		$this->assertEquals(count($ar),2);
		$this->assertTrue(isset($ar['workflow1/S1']));
		$this->assertTrue(isset($ar['workflow1/S3']));
		
		$this->assertEquals($ar['workflow1/S1'],'status S1');
		$this->assertEquals($ar['workflow1/S3'],'status S3');
	}
	
	public function test_03() {
		$m=new Model01();
		$ar=SWHelper::nextStatuslistData($m,array('includeCurrent'=>false));
	
		$this->assertTrue(is_array($ar));
		$this->assertEquals(count($ar),2);
		$this->assertTrue(isset($ar['workflow1/S2']));
		$this->assertTrue(isset($ar['workflow1/S3']));
	
		$this->assertEquals($ar['workflow1/S2'],'S2');
		$this->assertEquals($ar['workflow1/S3'],'status S3');
	}
	
	public function test_04() {
		$m=new Model01();
		$ar=SWHelper::nextStatuslistData($m,array('exclude'=>'workflow1/S2,workflow1/S3'));
	
		$this->assertTrue(is_array($ar));
		$this->assertEquals(count($ar),1);
		$this->assertTrue(isset($ar['workflow1/S1']));
		$this->assertEquals($ar['workflow1/S1'],'status S1');
		
		// exclude status as comma separated list with no workflow id and additional spaces
		$ar=SWHelper::nextStatuslistData($m,array('exclude'=>'S2,    S3'));
		
		$this->assertTrue(is_array($ar));
		$this->assertEquals(count($ar),1);
		$this->assertTrue(isset($ar['workflow1/S1']));
		$this->assertEquals($ar['workflow1/S1'],'status S1');
		
		
		$ar=SWHelper::allStatuslistData($m);
		$this->assertEquals(count($ar),5);
		
		$ar=SWHelper::allStatuslistData($m,array('includeCurrent'=>false));
		$this->assertEquals(count($ar),4);
		
		$this->assertTrue(isset($ar['workflow1/S1']) == false);
		
		$ar=SWHelper::allStatuslistData($m,array('exclude'=>array('S1','S2','S3','S4','S5')));
		$this->assertEquals(count($ar),0);
	}
}