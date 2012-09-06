<?php
class SWworkflowTask {
	
	// node name definition
	const S1 = 'S1';
	const S2 = 'S2';
	const S3 = 'S3';
	const S4 = 'S4';
	const S5 = 'S5';
	
	public function getDefinition(){
		
		return array(
			'initial' => self::S1,
			'node' => array(
		 		array(
		 			'id' 		 => self::S1,
		 			'label' 	 => 'status S1',
		 			'transition' => array(
		 				self::S1,
		 				self::S2 => '$this->task1();',		// $this refers to the owner model
		 				self::S3 => array($this, 'taskA'),	// $this refers to ... this workflow class
		 				self::S4
		 			),
		 			'metadata'=> array(
		 				'color' => 'red(s1)',
		 			)
		 		),
		 		array(
		 			'id' 		 => self::S2,
		 			'transition' => array(
		 				self::S4 => '$this->task2();',	// $this refers to the owner model,
		 			),
		 			'metadata'=> array(
		 				'color' => 'green(s2)',
		 			)
		 		
		 		),
		 		array(
		 			'id' 		 => self::S3,
		 			'label' 	 => 'status S3',
		 			'transition' => self::S4,
		 		),
		 		array(
		 			'id' 		 => self::S4,
		 			'label' 	 => 'status S4',
		 			'transition' => array(
		 				self::S3 => array($this,'taskB'),
		 				self::S5 => array($this,'taskC'),
		 			)
		 		),
		 		array(
		 			'id' 		 => self::S5,
		 			'label'   	 => 'status S5',
		 		),
		 	)
		 );
	}
	public function taskA($model, $srcStatus, $destStatus){
		$model->task1();
		return true;
	}
	public function taskB($model, $srcStatus, $destStatus){
		$model->task2();
		return true;
	}
	public function taskC($model, $srcStatus, $destStatus){
		$model->task3($srcStatus,$destStatus);
		return true;
	}
	
}