<?php
/**
 *
 * @author Raoul
 * @copyright Copyright 2010
 * @created 3 sept. 2010 - 22:09:44
 */			
class Custom02SWBehavior extends SWActiveRecordBehavior {
	private $_refCount=1;
	
	
	public $enterWorkflow=0;
	public $beforeTransition=0;
	public $processTransition=0;
	public $afterTransition=0;
	public $finalStatus=0;
	public $beforeSave=0;
	public $afterSave=0;

	private function getRef(){
		return $this->_refCount++;
	}
	public function enterWorkflow($event){
		$this->enterWorkflow=$this->getRef();				
	}
	public function beforeTransition($event){
		$this->beforeTransition=$this->getRef();				
	}
	public function processTransition($event){
		$this->processTransition=$this->getRef();				
	}
	public function afterTransition($event){	
		$this->afterTransition=$this->getRef();				
	}
	public function finalStatus($event){		
		$this->finalStatus=$this->getRef();				
	}
	/////////////////////////////////////////////////////////
	// overloaded AR events
	
	public function beforeSave($event){		
		$this->beforeSave=$this->getRef();	
		parent::beforeSave($event);			
	}
	public function afterSave($event){		
		$this->afterSave=$this->getRef();
		parent::afterSave($event);				
	}
				
	//	
	//////////////////////////////////////////////////////////
	public function getRefCount(){
		return $this->_refCount;
	}	
	public function toString(){
		return ' enterWorkflow:'.$this->enterWorkflow.
			' beforeTransition:'.$this->beforeTransition.
			' processTransition:'.$this->processTransition.
			' afterTransition:'.$this->afterTransition.
			' finalStatus:'.$this->finalStatus.
			' (ar)beforeSave:'.$this->beforeSave.
			' (ar)afterSave:'.$this->afterSave.
			' REF_COUNT:'.$this->getRefCount();
	}
}
?>