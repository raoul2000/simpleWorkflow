<?php
return array(
	'initial' => 'S1',
	'node' => array( 		
 		array(
 			'id' => 'S1',
 			'label' => 'status S1',
 			'transition'=> 'S1,S2,S3'
 		),
 		array(
 			'id' => 'S2',
 			'transition' => array('S4','workflowPartial2/P1')
 		),
 		array(
 			'id' => 'S3',
 			'label' => 'status S3',
 			'transition' => 'S4', 			
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
