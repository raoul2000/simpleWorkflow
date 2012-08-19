<?php
return array(
	'initial' => 'P1',
	'node' => array( 		
 		array(
 			'id' => 'P1',
 			'label' => 'status P1',
 			'transition'=> 'P2,P3'
 		),
 		array(
 			'id' => 'P2',
 			'transition' => 'workflowPartial1/S1'
 		),
 		array(
 			'id' => 'P3',
 			'label' => 'status P3',
 		),
 	)
 );
?>
