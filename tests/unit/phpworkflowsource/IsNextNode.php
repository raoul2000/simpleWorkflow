<?php
class IsNextNode extends CDbTestCase {

	/**
	 *
	 * @see test/CDbTestCase::setUp()
	 */
	protected function setUp()
	{
		parent::setUp();
	}
	public function test_01() {
		$c = Yii::createComponent(array(
			'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
			'basePath' => 'application.tests.unit.phpworkflowsource.workflows'
		));
		$c->init();

		$this->assertTrue($c->loadWorkflow('SWWorkflowAsArray'));
		
		$this->assertTrue($c->isNextNode('SWWorkflowAsArray/S1','SWWorkflowAsArray/S2'));
		$this->assertTrue($c->isNextNode('SWWorkflowAsArray/S1','SWWorkflowAsArray/S2','SWWorkflowAsArray'));
		$this->assertTrue($c->isNextNode('SWWorkflowAsArray/S1','S2'));
		$this->assertTrue($c->isNextNode('SWWorkflowAsArray/S1','S2','SWWorkflowAsArray'));
		$this->assertTrue($c->isNextNode('S1','S2','SWWorkflowAsArray'));
		
		$this->assertTrue($c->isNextNode('S1','SWWorkflowAsArray/S2','SWWorkflowAsArray'));
		$this->assertTrue($c->isNextNode('S1','S2','SWWorkflowAsArray'));
		
	}

	public function test_02() {
		$c = Yii::createComponent(array(
			'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
			'basePath' => 'application.tests.unit.phpworkflowsource.workflows'
		));
		$c->init();
	
		$this->assertTrue($c->loadWorkflow('SWWorkflowAsArray'));
		$this->assertFalse($c->isNextNode('S1','S5','SWWorkflowAsArray'));
		$this->assertFalse($c->isNextNode('SWWorkflowAsArray/S1','S5'));
		$this->assertFalse($c->isNextNode('SWWorkflowAsArray/S1','S5','SWWorkflowAsArray'));
		
		$this->assertFalse($c->isNextNode('SWWorkflowAsArray/S1','w1/S2'));
		$this->assertFalse($c->isNextNode('SWWorkflowAsArray/S1','w1/S2','SWWorkflowAsArray'));
		
		try{
			$this->assertTrue($c->isNextNode('S1','SWWorkflowAsArray/S2'));
			$this->fail();
		}catch(Exception $e){}
		
	}
}