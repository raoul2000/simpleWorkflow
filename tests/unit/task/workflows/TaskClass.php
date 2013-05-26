<?php
class TaskClass {
	public function runTask($ourceStatus,$targetStatus){
		// echo 'do something';
	}
	public function runTask2($owner,$sourceStatus,$targetStatus, $params){
		TaskTest_2::$task2 = array(
			'owner'=> $owner,
			'sourceStatus' => $sourceStatus,
			'targetStatus' => $targetStatus,
			'params' => $params
		);
	}
}