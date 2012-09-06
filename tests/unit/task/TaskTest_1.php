<?php
Yii::import('application.tests.unit.task.models.*');
Yii::import('application.tests.unit.task.workflows.*');

class TaskTest_1 extends CDbTestCase {

	/**
	 *
	 * @see test/CDbTestCase::setUp()
	 */
	protected function setUp()
	{
		parent::setUp();
	}
	public function test_01() {
		$ar = new ModelTask_A();
		
		$this->assertTrue($ar->swHasStatus());
		$this->assertTrue($ar->swStatusEquals(SWworkflowTask::S1));
		$this->assertEquals($ar->getCall('task1'),0);
		$this->assertEquals($ar->getCall('task2'),0);
		$this->assertTrue($ar->swNextStatus(SWworkflowTask::S2));
		$this->assertEquals($ar->getCall('task1'),1);
		$this->assertEquals($ar->getCall('task2'),0);
		
		$this->assertTrue($ar->swNextStatus(SWworkflowTask::S4));
		$this->assertEquals($ar->getCall('task1'),1);
		$this->assertEquals($ar->getCall('task2'),1);
	}
	public function test_02() {
		$ar = new ModelTask_A();
		
		$this->assertTrue($ar->swHasStatus());
		$this->assertTrue($ar->swStatusEquals(SWworkflowTask::S1));
		$this->assertEquals($ar->getCall('task1'),0);
		$this->assertEquals($ar->getCall('task2'),0);
		$this->assertTrue($ar->swNextStatus(SWworkflowTask::S3));
 		$this->assertEquals($ar->getCall('task1'),1);
 		$this->assertEquals($ar->getCall('task2'),0);
		
		$this->assertTrue($ar->swNextStatus(SWworkflowTask::S4));
		$this->assertEquals($ar->getCall('task1'),1);
		$this->assertEquals($ar->getCall('task2'),0);
		
		$this->assertTrue($ar->swNextStatus(SWworkflowTask::S3));
		$this->assertEquals($ar->getCall('task1'),1);
		$this->assertEquals($ar->getCall('task2'),1);
		
		$this->assertTrue($ar->swNextStatus(SWworkflowTask::S4));
		$this->assertEquals($ar->getCall('task1'),1);
		$this->assertEquals($ar->getCall('task2'),1);
		
		$this->assertTrue($ar->swNextStatus(SWworkflowTask::S5));
		$this->assertEquals($ar->getCall('task1'),1);
		$this->assertEquals($ar->getCall('task2'),1);
		$this->assertEquals($ar->getCall('task3'),1);
		
		$this->assertEquals($ar->src,'SWworkflowTask/S4');
		$this->assertEquals($ar->trg,'SWworkflowTask/S5');
		
		$this->assertTrue($ar->swCreateNode($ar->src)->equals(SWworkflowTask::S4));
		$this->assertTrue($ar->swCreateNode($ar->trg)->equals(SWworkflowTask::S5));
	}
	
	public function test_03() {
		$component = Yii::createComponent(
			array(
				'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
				'basePath'=> 'application.tests.unit.task.workflows'
			)
		);
		Yii::app()->setComponent('swSource', $component);
		
		$ar = new ModelTask_B();
	
		$this->assertTrue($ar->swHasStatus());
		$this->assertTrue($ar->swStatusEquals('S1'));
		$this->assertEquals($ar->getCall('task1'),0);
		$this->assertEquals($ar->getCall('task2'),0);
		
		$this->assertTrue($ar->swNextStatus('S2'));
		$this->assertEquals($ar->getCall('task1'),1);
		$this->assertEquals($ar->getCall('task2'),0);
	
		$this->assertTrue($ar->swNextStatus('S4'));
		$this->assertEquals($ar->getCall('task1'),1);
		$this->assertEquals($ar->getCall('task2'),0);
		$this->assertEquals($ar->getCall('task3'),0);
	
	}
}