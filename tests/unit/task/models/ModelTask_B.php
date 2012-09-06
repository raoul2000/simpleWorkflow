<?php
class ModelTask_B extends CActiveRecord{
	
	public $callTrace=array();
	
	public function tableName(){
		return 'item';
	}
	public function behaviors()
	{
		return array(
			'swBehavior' => array(
				'class'           => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'autoInsert' => true,
				'defaultWorkflow' => 'taskWorkflow1',
				'workflowSourceComponent'=> 'swSource'
			)
		);
	}
	
	private function _traceFunctionCall($functionName){
		if(!isset($this->callTrace[$functionName])){
			$this->callTrace[$functionName] = 0;
		}
		$this->callTrace[$functionName]++;
	}
	public function getCall($fname){
		if(!isset($this->callTrace[$fname])){
			return 0;
		}else
			return $this->callTrace[$fname];
	}
	
	public function task1(){
		$this->_traceFunctionCall(__FUNCTION__);
	}
	public function task2(){
		$this->_traceFunctionCall(__FUNCTION__);
	}
}

?>