<?php
class Event02ARTest extends CDbTestCase {
    public $fixtures=array(
        'items'=>'Model06',
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
	public function testEvent1() {
		
		$m=new Model08();
		
		$this->assertEquals($m->getRefCount(),2);

		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S1');
		$this->assertEquals($m->enterWorkflow,1);
		$this->assertEquals($m->getRefCount(),2);
		
		$m->swNextStatus('S2');
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S2');
		$this->assertEquals($m->enterWorkflow,1);
		$this->assertEquals($m->beforeTransition,2);
		$this->assertEquals($m->processTransition,3);
		$this->assertEquals($m->afterTransition,4);
		$this->assertEquals($m->getRefCount(),5);
			
		$m->swNextStatus('S4');
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S4');
		$this->assertEquals($m->enterWorkflow,1);
		$this->assertEquals($m->beforeTransition,5);
		$this->assertEquals($m->processTransition,6);
		$this->assertEquals($m->afterTransition,7);
		$this->assertEquals($m->getRefCount(),8);
		
		$m->swNextStatus('S5');
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S5');
		$this->assertEquals($m->enterWorkflow,1);
		$this->assertEquals($m->beforeTransition,8);
		$this->assertEquals($m->processTransition,9);
		$this->assertEquals($m->afterTransition,10);
		$this->assertEquals($m->finalStatus,11);
		$this->assertEquals($m->getRefCount(),12);
	}
	
	public function testEvent2() {
		
		$m=Model08::model()->findByPk('2');

		$this->assertEquals($m->getRefCount(),1); // auto-insert is TRUE
		$this->assertEquals($m->enterWorkflow,0);
		
		$m->status = 'S4';
		$m->save();
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S4');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeTransition,1);
		$this->assertEquals($m->processTransition,2);
		$this->assertEquals($m->afterTransition,3);
		$this->assertEquals($m->getRefCount(),4);

		$m->status = 'S5';
		$m->save();

		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S5');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeTransition,4);
		$this->assertEquals($m->processTransition,5);
		$this->assertEquals($m->afterTransition,6);
		$this->assertEquals($m->finalStatus,7);
		$this->assertEquals($m->getRefCount(),8);
	}
	public function testEvent3() {
		
		$m=Model08::model()->findByPk('2');

		$this->assertEquals($m->getRefCount(),1);
		
		// autoInsert is TRUE, so when the model is attached to the sWbehavior, it
		// is automatically added to the initial status : enterWorkflow is fired.
		 
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S2');
		
		$m->swNextStatus('S4');
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S4');
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
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S5');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeTransition,4);
		$this->assertEquals($m->processTransition,5);
		$this->assertEquals($m->afterTransition,6);
		$this->assertEquals($m->finalStatus,7);
		$this->assertEquals($m->getRefCount(),8);
	}
}
?>