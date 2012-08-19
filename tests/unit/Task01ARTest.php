<?php
class Task01ARTest extends CDbTestCase {
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
	public function testTask1() {
		
		$m=new Model0C();	// autoInsert is false
		$this->assertEquals($m->getRefCount(),1); // no event fired up to now
		
		
		$m->swNextStatus('S1');
		$this->assertEquals($m->swGetStatus()->toString(),'workflowWithTask/S1');
		$this->assertEquals($m->getRefCount(),1);
		
		$m->swNextStatus('S1');
		
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflowWithTask/S1');
		$this->assertEquals($m->selfTransition,1);
		$this->assertEquals($m->getRefCount(),2);
		 
		$m->swNextStatus('S2');

		$this->assertEquals($m->selfTransition,1);
		$this->assertEquals($m->trS1_S2,2);
		$this->assertEquals($m->getRefCount(),3);
		
		$m->swNextStatus('S4');
		$this->assertEquals($m->selfTransition,1);
		$this->assertEquals($m->trS1_S2,2);
		$this->assertEquals($m->taskWithArg,3);
		$this->assertEquals($m->taskWithArg_arg,'S2_S4');
		$this->assertEquals($m->getRefCount(),4);
		
		$m->swNextStatus('S3');
		$this->assertEquals($m->getRefCount(),4);
				
		$m->swNextStatus('S4');
		$this->assertEquals($m->taskWithArg,4);
		$this->assertEquals($m->taskWithArg_arg,'S3_S4');
		$this->assertEquals($m->getRefCount(),5);

		$m->swNextStatus('S5');
		$this->assertEquals($m->getRefCount(),5);
	}
	public function testTask2() {
		
		$m=Model0C::model()->findByPk('3');
		$this->assertEquals($m->getRefCount(),1); // no event fired up to now
		 
		
		$m->swNextStatus('S1');
		$this->assertEquals($m->swGetStatus()->toString(),'workflowWithTask/S1');
		$this->assertEquals($m->selfTransition,1);
		$this->assertEquals($m->getRefCount(),2);
		 
		$m->swNextStatus('S2');
		$this->assertEquals($m->selfTransition,1);
		$this->assertEquals($m->trS1_S2,2);
		$this->assertEquals($m->getRefCount(),3);
		
		$m->swNextStatus('S4');
		$this->assertEquals($m->selfTransition,1);
		$this->assertEquals($m->trS1_S2,2);
		$this->assertEquals($m->taskWithArg,3);
		$this->assertEquals($m->taskWithArg_arg,'S2_S4');
		$this->assertEquals($m->getRefCount(),4);
		
		$m->swNextStatus('S3');
		$this->assertEquals($m->getRefCount(),4);
				
		$m->swNextStatus('S4');
		$this->assertEquals($m->taskWithArg,4);
		$this->assertEquals($m->taskWithArg_arg,'S3_S4');
		$this->assertEquals($m->getRefCount(),5);

		$m->swNextStatus('S5');
		$this->assertEquals($m->getRefCount(),5);
			
	}
}
?>