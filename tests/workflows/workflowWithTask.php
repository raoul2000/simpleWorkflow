<?php
return array(
	'initial' => 'S1',
	'node' => array( 		
 		array(
 			'id' => 'S1',
 			'label' => 'status S1',
 			'transition'=> array(
 				'S1' => '$this->selfTransition()',
 				'S2' => '$this->trS1_S2()',
 				'S3' => '$this->trS1_S3()'
 			)
 		),
 		array(
 			'id' => 'S2',
 			'transition' => array('S4'=>'$this->taskWithArg(\'S2_S4\')'),
 		),
 		array(
 			'id' => 'S3',
 			'label' => 'status S3',
 			'transition' => array('S4'=>'$this->taskWithArg(\'S3_S4\')'), 			
 		),
 		array(
 			'id' => 'S4',
 			'label' => 'status S4',
 			'transition' => 'S3,S5'
 		),
 		array(
 			'id' => 'S5',
 			'label' => 'status S5',
 		),
 	)
 );
?>
