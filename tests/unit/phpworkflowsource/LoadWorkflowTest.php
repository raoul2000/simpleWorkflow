<?php
class LoadWorkflowTest extends CDbTestCase {

	/**
	 *
	 * @see test/CDbTestCase::setUp()
	 */
	protected function setUp()
	{
		parent::setUp();
	}
	public function testInit_01() {
	
		$c = Yii::createComponent(array(
			'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource'
		));
		$c->init();
		$this->assertEquals($c->basePath,'application.models.workflows');
		$this->assertEquals($c->definitionType,'array');
		$this->assertEquals($c->workflowNamePrefix,'sw');
		$this->assertTrue( is_array($c->preload));
		$this->assertTrue( count($c->preload) == 0);

	}
	public function testLoad_01() {
	
		$c = Yii::createComponent(array(
		'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource'
		));
		$c->init();
		$this->assertFalse($c->isWorkflowLoaded('workflow1'));
		
		try{
			$c->loadWorkflow('workflow1');
			$this->fail();
		}catch(SWException $e){
			$this->assertEquals($e->getCode(),SWException::SW_ERR_WORKFLOW_NOT_FOUND);
		}
	}
	public function testLoad_02() {
	
		$c = Yii::createComponent(array(
			'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
			'basePath' => 'application.tests.unit.phpworkflowsource.workflows'
		));
		$c->init();

		$this->assertFalse($c->isWorkflowLoaded('SWWorkflowAsArray'));
		$this->assertTrue($c->loadWorkflow('SWWorkflowAsArray'));
		$this->assertTrue($c->isWorkflowLoaded('SWWorkflowAsArray'));
		//echo CVarDumper::dumpAsString($c);
	}
	public function testLoad_03() {
	
		$c = Yii::createComponent(array(
			'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
			'basePath' => 'application.tests.unit.phpworkflowsource.workflows',
			'preload' => array('SWWorkflowAsArray')
		));
		$c->init();
	
		$this->assertTrue($c->isWorkflowLoaded('SWWorkflowAsArray'));
	}
	public function testLoad_04() {
	
		$c = Yii::createComponent(array(
			'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
			'basePath' => 'application.tests.unit.phpworkflowsource.workflows',
			'definitionType' => 'class'
		));
		$c->init();
	
		$this->assertFalse($c->isWorkflowLoaded('SWWorkflowAsClass'));
		$this->assertTrue($c->loadWorkflow('SWWorkflowAsClass'));
		$this->assertTrue($c->isWorkflowLoaded('SWWorkflowAsClass'));
	}
	public function testLoad_05() {
	
		$c = Yii::createComponent(array(
			'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
			'basePath' => 'application.tests.unit.phpworkflowsource.workflows',
			'definitionType' => 'class'
		));
		$c->init();
		$this->assertFalse($c->isWorkflowLoaded('SWWorkflowAsClass'));
	
		try{
			$c->loadWorkflow('NotFound');
			$this->fail();
		}catch(Exception $e){
		}
	}
	public function testLoad_06() {
	
		$c = Yii::createComponent(array(
			'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
			'basePath' => 'application.tests.unit.phpworkflowsource.workflows',
			'definitionType' => 'class'
		));
		$c->init();
	
		$this->assertFalse($c->isWorkflowLoaded('SWWorkflow2'));
		$this->assertTrue($c->loadWorkflow('SWWorkflow2'));
		$this->assertTrue($c->isWorkflowLoaded('SWWorkflow2'));
	}
}