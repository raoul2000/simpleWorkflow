<?php
		
class SWBehavior_1 extends SWActiveRecordBehavior {
	private $_refCount=1;
	
	
	public $enterWorkflow=0;
	public $beforeTransition=0;
	public $processTransition=0;
	public $afterTransition=0;
	public $finalStatus=0;
	
	public function enterWorkflow($event){
		$this->enterWorkflow=$this->_refCount;
		$this->_refCount++;
				
	}
	public function beforeTransition($event){
		$this->beforeTransition=$this->_refCount;
		$this->_refCount++;
	}
	public function processTransition($event){
		$this->processTransition=$this->_refCount;
		$this->_refCount++;
		
	}
	public function afterTransition($event){
		$this->afterTransition=$this->_refCount;
		$this->_refCount++;
		
	}
	public function finalStatus($event){
		$this->finalStatus=$this->_refCount;
		$this->_refCount++;
	}
	public function getRefCount(){
		return $this->_refCount;
	}
	public function toString(){
		return ' enterWorkflow:'.$this->enterWorkflow.
			' beforeTransition:'.$this->beforeTransition.
			' processTransition:'.$this->processTransition.
			' afterTransition:'.$this->afterTransition.
			' finalStatus:'.$this->finalStatus.
			' REF_COUNT:'.$this->getRefCount();
	}
}
?>