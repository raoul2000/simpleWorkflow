<?php
class Component1 extends CComponent {}
class Create01Test extends CDbTestCase {
	/**
	 *
	 * @see test/CDbTestCase::setUp()
	 */
	protected function setUp()
	{
		parent::setUp();
	}
	/**
	 */
	public function testInitialisation1() {
		$a=new Component1();
		$a->attachBehaviors(array(
			'swBehavior' => array(
				'class'     => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'autoInsert'=> false
			)
		));
		$this->assertFalse($a->autoInsert);
		$this->assertFalse($a->swHasStatus());
		$this->assertFalse($a->swIsEventEnabled());
		$this->assertNull($a->swGetStatus());
		$this->assertInstanceOf('SWActiveRecordBehavior',$a->asa('swBehavior'));
		$this->assertEquals($a->swGetDefaultWorkflowId(),'swComponent1');
		$this->assertNull($a->swGetWorkflowId());
		$this->assertEquals($a->statusAttribute,'status');
	}
	/**
	 *
	 */
	public function testInitialisation2() {
		$a=new Component1();
		$a->attachBehaviors(array(
			'swBehavior' => array(
				'class'     => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'autoInsert'=> false,
				'defaultWorkflow'	=> 'myWorkflow',
				'statusAttribute'	=> 'myStatus',
			)
		));
		$this->assertFalse($a->autoInsert);
		$this->assertFalse($a->swHasStatus());
		$this->assertFalse($a->swIsEventEnabled());
		$this->assertNull($a->swGetStatus());
		$this->assertInstanceOf('SWActiveRecordBehavior',$a->asa('swBehavior'));
		$this->assertEquals($a->swGetDefaultWorkflowId(),'myWorkflow');
		$this->assertNull($a->swGetWorkflowId());
		$this->assertEquals($a->statusAttribute,'myStatus');
	}
}
?>