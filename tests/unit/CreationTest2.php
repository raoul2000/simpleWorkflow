<?php
Yii::import('application.tests.unit.creation.models.*');
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
		$r = new Model0I();
		
		// autoInsert is TRUE
		$this->assertTrue($r->swHasStatus());
		$this->assertFalse($r->swStatusEquals());
		
		$this->assertTrue($r->swHasStatus());
		
		$this->assertTrue($r->swStatusEquals('s1'));
		$this->assertFalse($r->swStatusEquals('s2'));
		$this->assertTrue($r->swStatusEquals('swModel0I/s1'));
		$this->assertFalse($r->swStatusEquals('swModel0I/s2'));
		
		$this->assertEquals($r->swGetWorkflowId(), 'swModel0I');
		
		$this->assertTrue($r->swIsNextStatus('s2'));
		$this->assertTrue($r->swIsNextStatus('swModel0I/s2'));
		$this->assertFalse($r->swIsNextStatus('s1'));
		$this->assertFalse($r->swIsNextStatus('swModel0I/s1'));
		
		
		$this->assertTrue($r->swIsInitialStatus());
		$this->assertTrue($r->swIsInitialStatus('s1'));
		$this->assertFalse($r->swIsInitialStatus('s2'));
		$this->assertTrue($r->swIsInitialStatus('swModel0I/s1'));
		$this->assertFalse($r->swIsInitialStatus('swModel0I/s2'));
		
		
		$this->assertTrue($r->swIsStatus('s1'));
		$this->assertFalse($r->swIsStatus('s2'));
		$this->assertTrue($r->swIsStatus('swModel0I/s1'));
		$this->assertFalse($r->swIsStatus('swModel0I/s2'));
		
		$this->assertTrue($r->swStatusEquals('s1'));
		$this->assertFalse($r->swStatusEquals('s2'));
		$this->assertTrue($r->swStatusEquals('swModel0I/s1'));
		$this->assertFalse($r->swStatusEquals('swModel0I/s2'));
		$this->assertFalse($r->swStatusEquals());
		
		$this->assertFalse($r->swIsfinalStatus());
		$this->assertFalse($r->swIsfinalStatus('s1'));
		$this->assertFalse($r->swIsfinalStatus('swModel0I/s1'));
		$this->assertTrue($r->swIsfinalStatus('s2'));
		$this->assertTrue($r->swIsfinalStatus('swModel0I/s2'));
		
		$this->assertTrue($r->swNextStatus('s2'));
		$this->assertTrue($r->swIsStatus('s2'));
		$this->assertFalse($r->swIsStatus('s1'));
		$this->assertTrue($r->swIsfinalStatus());
		$this->assertTrue($r->swStatusEquals('s2'));
	}
	/**
	 * Remove from workflow
	 */
	public function test2() {
		$r = new Model0I();
		
		// autoInsert is TRUE
		$this->assertTrue($r->swHasStatus());
		$this->assertTrue($r->swIsStatus('s1'));

		// remove from workflow but current status is not final
		try{
			$r->swNextStatus(null);
			$this->fail();
		}catch(SWException $e){
		}
		
		$this->assertTrue($r->swHasStatus());
		$this->assertTrue($r->swNextStatus('s2'));
		$this->assertTrue($r->swIsfinalStatus('s2'));
		try{
			$this->assertTrue($r->swNextStatus(null));
			$this->fail();
		}catch(SWException $e){
			
		}
		$r->swRemoveFromWorkflow();
		$this->assertFalse($r->swHasStatus());
	}
}
?>