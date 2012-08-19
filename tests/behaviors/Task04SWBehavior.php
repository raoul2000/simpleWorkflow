<?php
/**
 *
 * @author Raoul
 * @copyright Copyright 2010
 * @created 3 sept. 2010 - 22:09:44
 */
class Task04SWBehavior extends SWActiveRecordBehavior {
	
	private $_refCount=1;
	public $selfTransition=0;
	public $trS1_S2=0;
	public $trS1_S3=0;
	public $taskWithArg=0;
	public $taskWithArg_arg=null;
	
	private function getRef(){
		return $this->_refCount++;
	}
		
	public function selfTransition(){
		$this->selfTransition=$this->getRef();
	}
	public function trS1_S2(){
		$this->trS1_S2=$this->getRef();
	}
	public function trS1_S3(){
		$this->trS1_S3=$this->getRef();
	}
	public function taskWithArg($arg){
		$this->taskWithArg=$this->getRef();
		$this->taskWithArg_arg=$arg;
	}
	public function getRefCount(){
		return $this->_refCount;
	}
	public function toString(){
		return 'selfTransition = '.$this->selfTransition.
		' trS1_S2 = '.$this->trS1_S2.
		' trS1_S3 = '.$this->trS1_S3.
		' taskWithArg = '.$this->taskWithArg.
		' _refCount = '.$this->_refCount;
	}
}
?>