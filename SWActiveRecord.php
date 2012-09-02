<?php
/**
 * This is the base class for all AR models that needs to handle events
 * fired by the simpleWorkflow behavior.
 * Handling simpleWorkflow events can also be achieved by creating a behavior that
 * inherits from SWActiveRecordBehavior and overload default event handlers
 */
class SWActiveRecord extends CActiveRecord {
	public function onEnterWorkflow($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
	public function enterWorkflow($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
	public function onBeforeTransition($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
	public function beforeTransition($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
	public function onProcessTransition($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
	public function processTransition($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
	public function onAfterTransition($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
	public function afterTransition($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
	public function onFinalStatus($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
	public function finalStatus($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
	public function onLeaveWorkflow($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
	public function leaveWorkflow($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,'application.simpleWorkflow');
	}
}
?>