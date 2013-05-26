<?php

class CreationTest1 extends CDbTestCase {
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
		$r = new Model0H();
		
		// autoInsert is FALSE
		$this->assertFalse($r->swHasStatus());
		$this->assertTrue($r->swStatusEquals());
		
		// insert into workflow
		$this->assertTrue($r->swInsertToWorkflow());
		
		$this->assertTrue($r->swHasStatus());
		
		$this->assertTrue($r->swStatusEquals('s1'));
		$this->assertFalse($r->swStatusEquals('s2'));
		$this->assertTrue($r->swStatusEquals('swModel0H/s1'));
		$this->assertFalse($r->swStatusEquals('swModel0H/s2'));
		
		$this->assertEquals($r->swGetWorkflowId(), 'swModel0H');
		
		$this->assertTrue($r->swIsNextStatus('s2'));
		$this->assertTrue($r->swIsNextStatus('swModel0H/s2'));
		$this->assertFalse($r->swIsNextStatus('s1'));
		$this->assertFalse($r->swIsNextStatus('swModel0H/s1'));
		
		
		$this->assertTrue($r->swIsInitialStatus());
		$this->assertTrue($r->swIsInitialStatus('s1'));
		$this->assertFalse($r->swIsInitialStatus('s2'));
		$this->assertTrue($r->swIsInitialStatus('swModel0H/s1'));
		$this->assertFalse($r->swIsInitialStatus('swModel0H/s2'));
		
		
		$this->assertTrue($r->swIsStatus('s1'));
		$this->assertFalse($r->swIsStatus('s2'));
		$this->assertTrue($r->swIsStatus('swModel0H/s1'));
		$this->assertFalse($r->swIsStatus('swAR2/s2'));
		
		$this->assertTrue($r->swStatusEquals('s1'));
		$this->assertFalse($r->swStatusEquals('s2'));
		$this->assertTrue($r->swStatusEquals('swModel0H/s1'));
		$this->assertFalse($r->swStatusEquals('swAR2/s2'));
		$this->assertFalse($r->swStatusEquals());
		
		$this->assertFalse($r->swIsfinalStatus());
		$this->assertFalse($r->swIsfinalStatus('s1'));
		$this->assertFalse($r->swIsfinalStatus('swModel0H/s1'));
		$this->assertTrue($r->swIsfinalStatus('s2'));
		$this->assertTrue($r->swIsfinalStatus('swModel0H/s2'));
		
		$this->assertTrue($r->swNextStatus('s2'));
		$this->assertTrue($r->swIsStatus('s2'));
		$this->assertFalse($r->swIsStatus('s1'));
		$this->assertTrue($r->swIsfinalStatus());
		$this->assertTrue($r->swStatusEquals('s2'));
		
	}
	/**
	 *
	 */
	public function test2() {
		$r = new Model0H();
		
		// autoInsert is FALSE
		$this->assertFalse($r->swHasStatus());
		$this->assertTrue($r->swStatusEquals());
		
		// insert into workflow : swNextStatus with no argument
		$this->assertTrue($r->swInsertToWorkflow());
		
		$this->assertTrue($r->swHasStatus());
		
		$this->assertTrue($r->swStatusEquals('s1'));
		$this->assertFalse($r->swStatusEquals('s2'));
	}
	/**
	 *
	 */
	public function test3() {
		$r = new Model0H();
	
		// autoInsert is FALSE
		$this->assertFalse($r->swHasStatus());
		$this->assertTrue($r->swStatusEquals());
	
		// insert into workflow : swNextStatus with initial status
		$this->assertTrue($r->swNextStatus('s1'));
	
		$this->assertTrue($r->swHasStatus());
	
		$this->assertTrue($r->swStatusEquals('s1'));
		$this->assertFalse($r->swStatusEquals('s2'));
	}
}
?>