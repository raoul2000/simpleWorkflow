<?php
class Model0E extends SWActiveRecord{

	public $username;
	public $password;
	
	// control attribute
	public $enableSwValidation=false;
	public $ruleSet=1;
	
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'item';
	}
	public function rules()
	{
		$ruleSet=1;
		switch ($this->ruleSet){
			case 1:
					$ruleSet=array(
						array('status','SWValidator','enableSwValidation'=>true,'match'=>true),	// mandatory
						array('username','required','on'=> 'sw:/S4-S3/'),
						array('password','required','on' => 'sw:/S2-workflowPartial2\/P1/')
					);
					break;
			case 2:
					$ruleSet=array(
						array('status','SWValidator','enableSwValidation'=>true,'match'=>true),	// mandatory
						array('username','required','on'=> 'sw:/-workflowPartial1\/S1/'),
					);
					break;
				
		}
		return $ruleSet;
	}
	public function behaviors()
	{
		return array(
			'swBehavior' => array(
				'class'      => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'autoInsert' => false,
				'defaultWorkflow' => 'workflowPartial1',
			)
		);
	}
}

?>