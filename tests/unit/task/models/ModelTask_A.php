<?php
class ModelTask_A extends CActiveRecord{
	
	public $callTrace=array();
	public $src,$trg;
	
	public function tableName(){
		return 'item';
	}
	public function behaviors()
	{
		return array(
			'swBehavior' => array(
				'class'           => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'autoInsert' => true,
				'defaultWorkflow' => 'SWworkflowTask',
				'workflowSourceComponent'=> 'swSourceClass'
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
	public function task3($src,$trg){
		$this->_traceFunctionCall(__FUNCTION__);
		$this->src = $src;
		$this->trg = $trg;
	}
}

?>