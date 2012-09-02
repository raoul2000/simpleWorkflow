<?php
class Component2a extends SWComponent {}
class Component2b extends CComponent {
	public function workflow(){
		return 'workflow';
	}
	
}
class Create02Test extends CDbTestCase {
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
	public function testInitialisation2a() {
		$a=new Component2a();
		try {
			$a->attachBehaviors(array(
				'swBehavior' => array(
					'class'     => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				)
			));
			$a->swNextStatus();
			$this->fail('error');
		} catch (Exception $e) {
			// exception because autoinsert is true (by default) but the workflow could
			// not be found
			$this->assertEquals($e->getMessage(),'Property "Component2a.status" is not defined.');

		}
	}
	/**
	 * Attachment to the component fails because, autoInsert is true and the owner
	 * provides its default workflow Id, but this worflow could not be found
	 *
	 */
	public function testInitialisation2b() {
		$a=new Component2b();
		try {
			$a->attachBehaviors(array(
				'swBehavior' => array(
					'class'     => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				)
			));
			$a->swNextStatus();
			$this->fail('error');
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(),'Property "Component2b.status" is not defined.');
		}
	}
}
?>