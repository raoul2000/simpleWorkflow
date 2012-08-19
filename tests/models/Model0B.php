<?php		
class Model0B extends SWActiveRecord{

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
				'class'      => 'application.tests.behaviors.Reentrant03SWBehavior',
				'autoInsert' => true,
				'defaultWorkflow' => 'workflow1',
			)	
		);
	}	
}

?>