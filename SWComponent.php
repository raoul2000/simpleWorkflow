<?php

/**
 * This is the base class for all components that needs to handle events
 * fired by the simpleWorkflow behavior.
 * Note that in most cases, this class is not used as the sW behavior is usually
 * attached to a CActiveRecord object.
 */
class SWComponent extends CComponent
{
	public function onEnterWorkflow($event) { }
	public function enterWorkflow($event) { }
	public function onBeforeTransition($event) { }
	public function beforeTransition($event) { }
	public function onProcessTransition($event) { }
	public function processTransition($event) { }
	public function onAfterTransition($event) { }
	public function afterTransition($event) { }
	public function onFinalStatus($event) { }
	public function finalStatus($event) { }
	public function onLeaveWorkflow($event) { }
	public function leaveWorkflow($event) { }
}