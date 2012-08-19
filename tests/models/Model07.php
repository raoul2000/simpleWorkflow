<?php
/**
 * defaultWorkflow='' (swModel05 will be used) autoInsert=false statusAttribute='notFound'
 *
 * @author Raoul
 * @copyright Copyright 2010
 * @created 29 août 2010 - 14:22:58
 */			
class Model07 extends SWActiveRecord{

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
				'class'      => 'application.tests.behaviors.Custom01SWBehavior',
				'autoInsert' => true,
				'defaultWorkflow' => 'workflow1'
			)	
		);
	}	
}

?>