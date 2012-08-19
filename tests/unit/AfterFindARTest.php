<?php
class AfterFindARTest extends CDbTestCase {
    public $fixtures=array(
        'items'=>'Model01',
    );
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
	public function testInitialisation1() {
		
		$m=Model01::model()->findByPk('1');
		
		$this->assertEquals($m->swGetWorkflowId(),'workflow1');
		$this->assertEquals($m->swGetDefaultWorkflowId(),'workflow1');
		$this->assertTrue($m->swHasStatus());
		
		$this->assertTrue($m->swIsInitialStatus($m->swGetStatus()));
		$this->assertTrue($m->swIsInitialStatus('workflow1/S1'));
		$this->assertTrue($m->swIsInitialStatus('S1'));
		$this->assertTrue($m->swIsInitialStatus());
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S1');
		$this->assertEquals(count($m->swGetAllStatus()),5);
		$this->assertEquals($m->swIsEventEnabled(),false);
	}
	/**
	 *
	 *
	 */
	public function testInitialisation2() {
		
		$m=Model01::model()->findByPk('2');
		
		$this->assertEquals($m->swGetWorkflowId(),'workflow1');
		$this->assertEquals($m->swGetDefaultWorkflowId(),'workflow1');
		$this->assertTrue($m->swHasStatus());
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S2');
		
		$this->assertFalse($m->swIsInitialStatus($m->swGetStatus()));
		$this->assertFalse($m->swIsInitialStatus('workflow1/S2'));
		$this->assertTrue($m->swIsInitialStatus('S1'));
		$this->assertFalse($m->swIsInitialStatus());
				
		$this->assertEquals(count($m->swGetAllStatus()),5);
		$this->assertEquals($m->swIsEventEnabled(),false);
	}
	/**
	 *
	 */
 	public function testInitialisation3() {
		$continue=true;
		try{
			$m= new Model02();
			$this->fails();
		}catch(Exception $e){
			
		}
		
		
	}
	/**
	 *
	 *
	 */
	public function testInitialisation4() {
		
		$m=Model03::model()->findByPk('2');
		
		$this->assertEquals($m->swGetWorkflowId(),'workflow1');
		$this->assertEquals($m->swGetDefaultWorkflowId(),'workflow1');
		$this->assertTrue($m->swHasStatus());
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S2');
		
		$this->assertFalse($m->swIsInitialStatus($m->swGetStatus()));
		$this->assertFalse($m->swIsInitialStatus('workflow1/S2'));
		$this->assertTrue($m->swIsInitialStatus('S1'));
		$this->assertFalse($m->swIsInitialStatus());
				
		$this->assertEquals(count($m->swGetAllStatus()),5);
		$this->assertEquals($m->swIsEventEnabled(),false);
	}
}
?>