<?php
class Event03ARTest extends CDbTestCase {
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
		
		$m=Model09::model()->findByPk('2');
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S2');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->getRefCount(),1);
		
		$m->status = 'S4';
		$m->save();
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S4');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeSave,1);
		$this->assertEquals($m->beforeTransition,2);
		$this->assertEquals($m->processTransition,3);
		$this->assertEquals($m->afterTransition,4);
		$this->assertEquals($m->afterSave,5);
		$this->assertEquals($m->getRefCount(),6);

		$m->status = 'S5';
		$m->save();
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S5');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeSave,6);
		$this->assertEquals($m->beforeTransition,7);
		$this->assertEquals($m->processTransition,8);
		$this->assertEquals($m->afterTransition,9);
		$this->assertEquals($m->finalStatus,10);
		$this->assertEquals($m->afterSave,11);
		$this->assertEquals($m->getRefCount(),12);
	}
	public function testEvent2() {
		
		$m=Model0A::model()->findByPk('2');
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S2');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->getRefCount(),1);
		
		$m->status = 'S4';
		$m->save();

		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S4');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeSave,1);
		$this->assertEquals($m->beforeTransition,2);
		$this->assertEquals($m->afterSave,3);
		$this->assertEquals($m->processTransition,4);
		$this->assertEquals($m->afterTransition,5);
		$this->assertEquals($m->getRefCount(),6);

		$m->status = 'S5';
		$m->save();
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S5');
		$this->assertEquals($m->enterWorkflow,0);
		$this->assertEquals($m->beforeSave,6);
		$this->assertEquals($m->beforeTransition,7);
		$this->assertEquals($m->afterSave,8);
		
		$this->assertEquals($m->processTransition,9);
		$this->assertEquals($m->afterTransition,10);
		$this->assertEquals($m->finalStatus,11);

		$this->assertEquals($m->getRefCount(),12);
	}
}
?>