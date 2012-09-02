<?php
/*
assertArrayHasKey()
assertClassHasAttribute()
assertClassHasStaticAttribute()
assertContains()
assertContainsOnly()
assertCount()
assertEmpty()
assertEqualXMLStructure()
assertEquals()
assertFalse()
assertFileEquals()
assertFileExists()
assertGreaterThan()
assertGreaterThanOrEqual()
assertInstanceOf()
assertInternalType()
assertLessThan()
assertLessThanOrEqual()
assertNull()
assertObjectHasAttribute()
assertRegExp()
assertStringMatchesFormat()
assertStringMatchesFormatFile()
assertSame()
assertSelectCount()
assertSelectEquals()
assertSelectRegExp()
assertStringEndsWith()
assertStringEqualsFile()
assertStringStartsWith()
assertTag()
assertThat()
assertTrue()
assertXmlFileEqualsXmlFile()
assertXmlStringEqualsXmlFile()
assertXmlStringEqualsXmlString()
*/
class AR1 extends CActiveRecord {
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'item';
	}
	public function behaviors()
	{
		return array(
				'swBehavior' => array(
				'class'      => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'autoInsert' => true,
			)
		);
	}
	public function workflow(){
		return array(
			'initial' => 1,
	 		'node' => array(
	 			array(
	 				'id' => '1',
	 				'label' => 'label1',
	 				'transition'=> '2'
	 			),
		 		array(
			 		'id' => '2',
			 		'label' => 'label2'
		 		)
	 		)
		);
	}
}
class CreationTest2 extends CDbTestCase {
	/**
	 *
	 * @see test/CDbTestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
	}
	/**
	 */
	public function test1() {
		$r = new AR1();
		
		// autoInsert is TRUE
		$this->assertTrue($r->swHasStatus());
		$this->assertFalse($r->swStatusEquals());
		
		$this->assertTrue($r->swHasStatus());
		
		$this->assertTrue($r->swStatusEquals('1'));
		$this->assertFalse($r->swStatusEquals('2'));
		$this->assertTrue($r->swStatusEquals('swAR1/1'));
		$this->assertFalse($r->swStatusEquals('swAR1/2'));
		
		$this->assertEquals($r->swGetWorkflowId(), 'swAR1');
		
		$this->assertTrue($r->swIsNextStatus('2'));
		$this->assertTrue($r->swIsNextStatus('swAR1/2'));
		$this->assertFalse($r->swIsNextStatus('1'));
		$this->assertFalse($r->swIsNextStatus('swAR1/1'));
		
		
		$this->assertTrue($r->swIsInitialStatus());
		$this->assertTrue($r->swIsInitialStatus('1'));
		$this->assertFalse($r->swIsInitialStatus('2'));
		$this->assertTrue($r->swIsInitialStatus('swAR1/1'));
		$this->assertFalse($r->swIsInitialStatus('swAR1/2'));
		
		
		$this->assertTrue($r->swIsStatus('1'));
		$this->assertFalse($r->swIsStatus('2'));
		$this->assertTrue($r->swIsStatus('swAR1/1'));
		$this->assertFalse($r->swIsStatus('swAR2/2'));
		
		$this->assertTrue($r->swStatusEquals('1'));
		$this->assertFalse($r->swStatusEquals('2'));
		$this->assertTrue($r->swStatusEquals('swAR1/1'));
		$this->assertFalse($r->swStatusEquals('swAR2/2'));
		$this->assertFalse($r->swStatusEquals());
		
		$this->assertFalse($r->swIsfinalStatus());
		$this->assertFalse($r->swIsfinalStatus('1'));
		$this->assertFalse($r->swIsfinalStatus('swAR1/1'));
		$this->assertTrue($r->swIsfinalStatus('2'));
		$this->assertTrue($r->swIsfinalStatus('swAR1/2'));
		
		$this->assertTrue($r->swNextStatus('2'));
		$this->assertTrue($r->swIsStatus('2'));
		$this->assertFalse($r->swIsStatus('1'));
		$this->assertTrue($r->swIsfinalStatus());
		$this->assertTrue($r->swStatusEquals('2'));
	}
	/**
	 * Remove from workflow
	 */
	public function test2() {
		$r = new AR1();
		
		// autoInsert is TRUE
		$this->assertTrue($r->swHasStatus());
		$this->assertTrue($r->swIsStatus('1'));

		// remove from workflow but current status is not final
		try{
			$r->swNextStatus();
			$this->fails();
		}catch(CException $e){
		}
		
		$this->assertTrue($r->swHasStatus());
		$this->assertTrue($r->swNextStatus('2'));
		$this->assertTrue($r->swIsfinalStatus('2'));
		
		$this->assertTrue($r->swNextStatus());
		
		$this->assertFalse($r->swHasStatus());
	}
}
?>