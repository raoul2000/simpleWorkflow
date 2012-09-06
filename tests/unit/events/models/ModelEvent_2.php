<?php
/**
 * defaultWorkflow='' (swModel05 will be used) autoInsert=false statusAttribute='notFound'
 *
 * @author Raoul
 * @copyright Copyright 2010
 * @created 29 ao�t 2010 - 14:22:58
 */
class ModelEvent_2 extends SWActiveRecord{

	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'item';
	}

	public function rules()
	{
		return array(
			array('status','SWValidator'),	// mandatory
		);
	}
	public function behaviors()
	{
		return array(
			'swBehavior' => array(
				'class'      		=> 'application.tests.unit.events.behaviors.SWBehavior_1',
				'autoInsert' 		=> true,
				'defaultWorkflow' 	=> 'workflowEvent'
			)
		);
	}
}

?>