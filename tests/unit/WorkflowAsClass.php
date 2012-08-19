<?php


Yii::import("application.tests.workflows.SWWorkflow1");


class WorkflowAsClass extends CDbTestCase {

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
	public function test1() {
		
		$m=new Model0F();
		$m->status = SWWorkflow1::S1;
		$this->assertTrue($m->swNextStatus());
		$this->assertTrue($m->taskCallCount == 0);
		
		$this->assertTrue($m->swGetStatus()->equals(SWWorkflow1::S1));
		$this->assertTrue($m->swStatusEquals(SWWorkflow1::S1));
		
		$this->assertTrue($m->save());
		
		$this->assertTrue($m->taskCallCount == 0);
		
		$m->status = SWWorkflow1::S2;
		
		$m->update(array('status'));
		$this->assertTrue($m->taskCallCount == 1);
		
		
		$this->assertTrue($m->swGetStatus()->equals(SWWorkflow1::S2));
		$this->assertTrue($m->swStatusEquals(SWWorkflow1::S2));
		
		$m=Model01::model()->findByPk('1');
		
		$this->assertEquals($m->swGetWorkflowId(),'workflow1');
		$this->assertEquals($m->swGetDefaultWorkflowId(),'workflow1');
		$this->assertTrue($m->swHasStatus());
		
		$this->assertTrue($m->swIsInitialStatus($m->swGetStatus()));
		$this->assertTrue($m->swIsInitialStatus('workflow1/S1'));
		$this->assertTrue($m->swIsInitialStatus('S1'));
		$this->assertTrue($m->swIsInitialStatus());
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S1');
		$this->assertEquals(count($m->swGetAllStatus()),5);
		$this->assertEquals($m->swIsEventEnabled(),false);
	}
	
	public function test2() {
	
		$m=new Model0F();
		$m->status = SWWorkflow1::S1;
		$this->assertTrue($m->swNextStatus());
		$this->assertTrue($m->taskCallCount == 0);
	
		$this->assertTrue($m->swGetStatus()->equals(SWWorkflow1::S1));
		$this->assertTrue($m->swStatusEquals(SWWorkflow1::S1));
	
		$this->assertTrue($m->save());
	
		$this->assertTrue($m->taskCallCount == 0);

		$this->assertTrue($m->swNextStatus(SWWorkflow1::S3));
	
		$m->save();
		$this->assertTrue($m->taskCallCount == 1);
	
	}
}
?>