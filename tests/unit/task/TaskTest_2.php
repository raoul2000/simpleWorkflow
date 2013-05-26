<?php

Yii::import('application.tests.unit.task.models.*');
Yii::import('application.tests.unit.task.workflows.*');

class TaskTest_2 extends CDbTestCase {
	static public $task2=null;

	protected function setUp()
	{
		$component = Yii::createComponent(
			array(
				'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
				'basePath'=> 'application.tests.unit.task.workflows'
			)
		);
		Yii::app()->setComponent('swSource', $component);
		TaskTest_2::$task2 = null;
		parent::setUp();
	}
	
	public function test_01() {
		$ar = new ModelTask_C();
	
		$this->assertTrue($ar->swHasStatus());
		$this->assertTrue($ar->swStatusEquals('S1'));
		$this->assertEquals(TaskTest_2::$task2,null);
		
		$this->assertTrue($ar->swNextStatus('S2',array('var1'=>'value1')));
		
		$this->assertTrue(is_array(TaskTest_2::$task2));
		$this->assertEquals(TaskTest_2::$task2['sourceStatus'], 'taskWorkflow2/S1');
		$this->assertEquals(TaskTest_2::$task2['targetStatus'], 'taskWorkflow2/S2');
		
		$this->assertTrue(is_array(TaskTest_2::$task2['params']));
		
		$this->assertEquals(TaskTest_2::$task2['params']['var1'], 'value1');
	}
	
	public function test_02() {
		$ar = new ModelTask_C();
	
		$this->assertTrue($ar->swHasStatus());
		$this->assertTrue($ar->swStatusEquals('S1'));
		$this->assertEquals(TaskTest_2::$task2,null);
	
		$this->assertTrue($ar->swNextStatus('S3',array('var1'=>'value1')));
	
		$this->assertTrue(is_array(TaskTest_2::$task2));
		$this->assertTrue(is_array(TaskTest_2::$task2['params']));
	
		$this->assertEquals(TaskTest_2::$task2['params']['var1'], 'value1');
	}
}