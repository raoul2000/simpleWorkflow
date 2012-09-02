<?php
class Model06a extends CActiveRecord {

	public function onEnterWorkflow($event)
	{
		$this->raiseEvent('onEnterWorkflow',$event);
	}
// 	public function onBeforeTransition($event)
// 	{
// 		$this->raiseEvent('onBeforeTransition',$event);
// 	}

// 	public function onProcessTransition($event)
// 	{
// 		$this->raiseEvent('onProcessTransition',$event);
// 	}

// 	public function onAfterTransition($event)
// 	{
// 		$this->raiseEvent('onAfterTransition',$event);
// 	}

// 	public function onFinalStatus($event)
// 	{
// 		$this->raiseEvent('onFinalStatus',$event);
// 	}

// 	public function onLeaveWorkflow($event)
// 	{
// 		$this->raiseEvent('onLeaveWorkflow',$event);
// 	}
	
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
				'class'      		=> 'application.tests.behaviors.Custom01SWBehavior',
				'autoInsert' 		=> false,
				'defaultWorkflow' 	=> 'workflow1'
			)
		);
	}
}

?>