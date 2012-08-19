<?php		
class Model0C extends SWActiveRecord{

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
				'class'      => 'application.tests.behaviors.Task04SWBehavior',
				'autoInsert' => false,
				'defaultWorkflow' => 'workflowWithTask',
			)	
		);
	}	
}

?>