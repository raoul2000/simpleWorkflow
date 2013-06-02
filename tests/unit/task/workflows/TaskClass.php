<?php
class TaskClass {
	public function runTask($ourceStatus,$targetStatus){
		// echo 'do something';
	}
	public function runTask2($owner,$sourceStatus,$targetStatus, $params){
		Task_2_Test::$task2 = array(
			'owner'=> $owner,
			'sourceStatus' => $sourceStatus,
			'targetStatus' => $targetStatus,
			'params' => $params
		);
	}
}