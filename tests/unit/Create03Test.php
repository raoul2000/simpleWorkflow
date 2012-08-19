<?php
class Component3 extends SWActiveRecord {
	public function tableName()
	{
		return 'item';
	}
	public function workflow(){
		return array(
			'initial' => 'A',
			'node' => array(
		 		array(
		 			'id' => 'A',
		 			'label' => 'status A',
		 		),
		 	)
		 );
	}
}
class Create03Test extends CDbTestCase {
	/**
	 *
	 * @see test/CDbTestCase::setUp()
	 */
	protected function setUp()
	{
		parent::setUp();
	}
	/**
	 * Component provide the workflow definition.
	 * The behavior is initialized with default settings.
	 */
	public function testInitialisation1() {
		$a=new Component3();
		$a->attachBehaviors(array(
			'swBehavior' => array(
				'class'     => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
			)
		));
		
		$this->assertTrue($a->autoInsert);
		
		$this->assertTrue($a->swHasStatus());
		$this->assertTrue($a->swIsEventEnabled());
		$this->assertInstanceOf('SWActiveRecordBehavior',$a->asa('swBehavior'));
		$this->assertEquals($a->swGetDefaultWorkflowId(),'swComponent3');
		$this->assertEquals($a->swGetWorkflowId(),'swComponent3');
		$this->assertEquals($a->swGetStatus()->toString(),'swComponent3/A');
		try {
			$a->swInsertToWorkflow();
			$this->fail('error');
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(),'object already in a workflow : swComponent3/A');
		}
	}
}
?>