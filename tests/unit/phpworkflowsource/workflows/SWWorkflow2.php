<?php
class SWWorkflow2 {
	
	// node name definition
	const S1 = 'SWWorkflow2/S1';
	const S2 = 'SWWorkflow2/S2';
	const S3 = 'SWWorkflow2/S3';
	const S4 = 'SWWorkflow2/S4';
	const S5 = 'SWWorkflow2/S5';
	
	public function getDefinition(){
		
		return array(
			'initial' => self::S1,
			'node' => array(
		 		array(
		 			'id' 		 => self::S1,
		 			'label' 	 => 'status S1',
		 			'transition' => array(
		 				self::S1,
		 				self::S2 => '$this->task1();',	// ok
		 				self::S3 => array($this, 'taskA'),
		 				self::S4
		 			),
		 			'metadata'=> array(
		 				'color' => 'red(s1)',
		 			)
		 		),
		 		array(
		 			'id' 		 => self::S2,
		 			'transition' => self::S4,
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
		 				self::S3,
		 				self::S5
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
}