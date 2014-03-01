<?php

/**
 * this class implements events fired by the simpleWorkflow behavior. This event is fired
 * at different time during a transition.
 */
class SWEvent extends CEvent
{
	/**
	 * @var SWNode source status the owner model is in
	 */
	public $source;

	/**
	 * @var SWNode destination status the owner model is sent to
	 */
	public $destination;

	/**
	 * @param mixed $sender sender of this event
	 * @param mixed $source
	 * @param $destination
	 */
	public function __construct($sender, $source, $destination)
	{
		parent::__construct($sender);
		$this->source = $source;
		$this->destination = $destination;
	}
}
