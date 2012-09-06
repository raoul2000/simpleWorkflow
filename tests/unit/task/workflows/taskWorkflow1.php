<?php
return array(
	'initial' => 'S1',
	'node' => array(
 		array(
 			'id' => 'S1',
 			'label' => 'status S1',
 			'transition'=> array(
 				'S1',
 				'S2' => '$this->task1();',
 				'S3'
 			),
 			'metadata' => array(
 				'attr1' => 'value1',
 				'arr1'  => array(1,2,3)
 			)
 		),
 		array(
 			'id' => 'S2',
 			'transition' => array(
 				'S4'  =>  array(new TaskClass(), 'runTask')
 			)
 		),
 		array(
 			'id' => 'S3',
 			'label' => 'status S3',
 			'transition' => 'S4',
 		),
 		array(
 			'id' => 'S4',
 			'label' => 'status S4',
 			'transition' => 'S3,S5',
 			'metadata' => array(
 				'attr4' => 'value4',
 				'arr4'  => array(4,5,'string6')
 			)
 		),
 		array(
 			'id' => 'S5',
 			'label' => 'status S5',
 		),
 	)
 );
?>