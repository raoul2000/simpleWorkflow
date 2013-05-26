<?php
return array(
	'initial' => 'S1',
	'node' => array(
 		array(
 			'id' => 'S1',
 			'label' => 'status S1',
 			'transition'=> array(
 				'S1',
 				'S2' => array(new TaskClass(), 'runTask2'),
 				'S3' => '$this->task2($params);',		// $this refers to the owner model,
 			),
 		),
 		array(
 			'id' => 'S2',
 		),
 		array(
 			'id' => 'S3',
 		),
 	)
 );
?>