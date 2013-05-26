<?php
class Component4 extends SWActiveRecord {

	public $enterWorkflow=false;
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
	public function enterWorkflow($event) {
		$this->enterWorkflow = true;
	}
}
class Component4b extends SWActiveRecord {
	public $enterWorkflow=false;
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
class CustomSWBehavior extends SWActiveRecordBehavior {
	public function enterWorkflow($event)
	{
		$this->getOwner()->enterWorkflow = true;
	}
}
class Create04WithEventTest extends CDbTestCase {
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
	 * The component is inserted into the workflow and event onEnterWorkflow is fired. It is
	 * handled by method enterWorkflow defined in the component itself
	 */
	public function testInitialisation1() {
		$a=new Component4();
		$a->attachBehaviors(array(
			'swBehavior' => array(
				'class'     => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
			)
		));
		$this->assertTrue($a->enterWorkflow);
	}
	/**
	 * Same as before : onEnterWorkflow is fired but not caught (except by the component base
	 * class (default implementation does nothing)
	 */
	public function testInitialisation2() {
		$a=new Component4b();
		$a->attachBehaviors(array(
			'swBehavior' => array(
				'class'     => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
			)
		));
		$this->assertFalse($a->enterWorkflow);
	}
				
}
?>