<?php
/**
 * This is the base class for all AR models that needs to handle events
 * fired by the simpleWorkflow behavior.
 * Handling simpleWorkflow events can also be achieved by creating a behavior that
 * inherits from SWActiveRecordBehavior and overload default event handlers
 */
class SWActiveRecord extends CActiveRecord
{
	public function onEnterWorkflow($event)
	{
	}
	public function enterWorkflow($event)
	{
	}
	public function onBeforeTransition($event)
	{
	}
	public function beforeTransition($event)
	{
	}
	public function onProcessTransition($event)
	{
	}
	public function processTransition($event)
	{
	}
	public function onAfterTransition($event)
	{
	}
	public function afterTransition($event)
	{
	}
	public function onFinalStatus($event)
	{
	}
	public function finalStatus($event)
	{
	}
	public function onLeaveWorkflow($event)
	{
	}
	public function leaveWorkflow($event)
	{
	}
}
?>