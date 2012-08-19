<?php
/**
 *
 * @author Raoul
 * @copyright Copyright 2010
 * @created 3 sept. 2010 - 22:09:44
 */			
class Reentrant03SWBehavior extends SWActiveRecordBehavior {
	public function processTransition($event){
		$this->swNextStatus('S5');
	}	
}
?>