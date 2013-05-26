<?php
class Model0D extends SWActiveRecord{

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
						array('status','SWValidator','enableSwValidation'=>false),	// mandatory
						array('username','required','on'=> 'sw:S4->S3')
					);
					break;
			case 2:
					$ruleSet=array(
						array('status','SWValidator','enableSwValidation'=>true),	// mandatory
						// simpleWorkflow Validators
						array('username','required','on'=> array('sw:S1-S1','S1-S2','sw:S1-S3')),
						array('username','required','on'=> 'sw:S4-S3')
					);
					break;
			case 3:
					$ruleSet=array(
						array('status','SWValidator','enableSwValidation'=>true,'match'=>true),	// mandatory
						// simpleWorkflow Validators
						array('username','required','on'=>  array('sw:/^S1-S1$/','S1-S2','sw:/S1-S3/')),
					);
					break;
			case 4:
					$ruleSet=array(
						array('status','SWValidator','enableSwValidation'=>true,'match'=>true),	// mandatory
						// simpleWorkflow Validators
						array('username','required','on'=>  array('sw:/^S1-S1$/','S1-S2','sw:/S1-S3/')),
						array('password','required','on'=>  'sw:/S1-.*/'),
					);
					break;
				
		}
		return $ruleSet;
//		if($this->enableSwValidation==false){
//		return array(
//			array('status','SWValidator','enableSwValidation'=>false),	// mandatory
//			array('username','required','on'=> 'sw:S4_S3')
//		);
//
//		}else{
//			return array(
//				array('status','SWValidator','enableSwValidation'=>true),	// mandatory
//				// simpleWorkflow Validators
//				array('username','required','on'=> array('sw:S1_S1','S1_S2','sw:S1_S3')),
//				array('username','required','on'=> 'sw:S4_S3')
//			);
//		}
	}
	public function behaviors()
	{
		return array(
			'swBehavior' => array(
				'class'           => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'autoInsert' => false,
				'defaultWorkflow' => 'workflow1',
			)
		);
	}
}

?>