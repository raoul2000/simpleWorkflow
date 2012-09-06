<?php
Yii::import('application.tests.unit.events.models.*');
class Event_01 extends CDbTestCase {
    public $fixtures=array(
        'items'=>'ModelEvent_1',
    );
	/**
	 *
	 * @see test/CDbTestCase::setUp()
	 */
	protected function setUp()
	{
		parent::setUp();
		
		$component = Yii::createComponent(
			array(
			'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
			'basePath'=> 'application.tests.unit.events.workflows'
			)
		);
		Yii::app()->setComponent('swSource', $component);
	}
	/**
	 *
	 */
	public function testEvent1() {
		
		$m=new ModelEvent_1();
		
		$this->assertEquals($m->getRefCount(),1); // no event fired up to now
		$this->assertEquals($m->enterWorkflow,0);
		
		$this->assertTrue($m->asa('swBehavior') != null);
		
		$this->assertTrue($m->hasEventHandler('onEnterWorkflow'));
		
		$m->swNextStatus('S1');
		$this->assertEquals($m->swGetStatus()->toString(),'workflowEvent/S1');
		
		$this->assertTrue($m->swIsEventEnabled());
		$this->assertEquals($m->enterWorkflow,1);
		$this->assertEquals($m->getRefCount(),2);
		
		$m->swNextStatus('S2');
		$this->assertEquals($m->swGetStatus()->toString(),'workflowEvent/S2');
		$this->assertEquals($m->enterWorkflow,1);
		$this->assertEquals($m->beforeTransition,2);
		$this->assertEquals($m->processTransition,3);
		$this->assertEquals($m->afterTransition,4);
		$this->assertEquals($m->getRefCount(),5);
			
		$m->swNextStatus('S4');
		$this->assertEquals($m->swGetStatus()->toString(),'workflowEvent/S4');
		$this->assertEquals($m->enterWorkflow,1);
		$this->assertEquals($m->beforeTransition,5);
		$this->assertEquals($m->processTransition,6);
		$this->assertEquals($m->afterTransition,7);
		$this->assertEquals($m->getRefCount(),8);
		
		$m->swNextStatus('S5');
		$this->assertEquals($m->swGetStatus()->toString(),'workflowEvent/S5');
		$this->assertEquals($m->enterWorkflow,1);
		$this->assertEquals($m->beforeTransition,8);
		$this->assertEquals($m->processTransition,9);
		$this->assertEquals($m->afterTransition,10);
		$this->assertEquals($m->finalStatus,11);
		$this->assertEquals($m->getRefCount(),12);
	}
	/**
	 *
	 */
	public function testEvent2() {
		
		$m=ModelEvent_1::model()->findByPk('7');

		$this->assertEquals($m->getRefCount(),1); // no event fired up to now
		$this->assertEquals($m->enterWorkflow,0);

		$m->status = 'S4';
		$this->assertTrue($m->validate());
		$this->assertTrue($m->save());

		$this->assertEquals($m->swGetStatus()->toString(),'workflowEvent/S4');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeTransition,1);
		$this->assertEquals($m->processTransition,2);
		$this->assertEquals($m->afterTransition,3);
		$this->assertEquals($m->getRefCount(),4);

		$m->status = 'S5';
		$this->assertTrue($m->save());
		$this->assertEquals($m->swGetStatus()->toString(),'workflowEvent/S5');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeTransition,4);
		$this->assertEquals($m->processTransition,5);
		$this->assertEquals($m->afterTransition,6);
		$this->assertEquals($m->finalStatus,7);
		$this->assertEquals($m->getRefCount(),8);
	}
	public function testEvent3() {
		
		$m=ModelEvent_2::model()->findByPk('7');

		$this->assertEquals($m->getRefCount(),1);

		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->swGetStatus()->toString(),'workflowEvent/S2');
		
		$m->swNextStatus('S4');
		
		//echo $m->toString();
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflowEvent/S4');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeTransition,1);
		$this->assertEquals($m->processTransition,2);
		$this->assertEquals($m->afterTransition,3);
		$this->assertEquals($m->getRefCount(),4);
		
		$m->save();
		// no event are fired now; they were fired during the call to
		// swNextStatus
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeTransition,1);
		$this->assertEquals($m->processTransition,2);
		$this->assertEquals($m->afterTransition,3);
		$this->assertEquals($m->getRefCount(),4);

		$m->status = 'S5';
		$m->save();
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflowEvent/S5');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeTransition,4);
		$this->assertEquals($m->processTransition,5);
		$this->assertEquals($m->afterTransition,6);
		$this->assertEquals($m->finalStatus,7);
		$this->assertEquals($m->getRefCount(),8);
	}
}
?>