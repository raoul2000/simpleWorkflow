<?php

/**
 * This is the base class for all workflow source implementations. It provides
 * basic initialization features and a set of methods that must be implemented
 * by workflow source classes.
 */
abstract class SWWorkflowSource extends CApplicationComponent
{
	/**
	 * @var array list of workflow names that should be loaded when the component is initialized
	 */
	public $preload = array();
	/**
	 * @var string when a workflow name is automatically  built from the model name, this prefix is added to the
	 * model name so to avoid clashes (e.g. model 'MyModel' is by default inserted into workflow 'swMyModel')
	 */
	public $workflowNamePrefix = 'sw';

	/**
	 * Create and returns a SWNode object. The SWNode returned doesn't have to be defined in a workflow currently loaded.
	 * If $node is a string, it can be a fully qualified node id (e.g workflowId/NodeId)
	 * or only a nodeId, but in this case, argument $workflowId must contain the id of the workflow to use.
	 * If $node is a SWNode object, then it is returned with no modification.
	 *
	 * @param $node
	 * @param $workflowId
	 * @return SWNode
	 */
	public function createSWNode($node, $workflowId)
	{
		return new SWNode($node, $workflowId);
	}

	/**
	 * Add a workflow to the internal workflow collection. The definition
	 * of the workflow to add is provided in the $definition argument as an associative array.
	 * This method is used for instance when a workflow definition is provided by a
	 * model and not by a php file or another source. If a workflow with the same id is already
	 * loaded, it is not over written.
	 *
	 * @param array $definition workflow definition
	 * @param string $id unique id for the workflow to add
	 */
	abstract public function addWorkflow($definition, $id);

	/**
	 * Loads the workflow whose id is passed as argument from the source.
	 * If it was already loaded, then it is not reloaded unless $forceReload is set to TRUE.
	 * If the workflow could not be found, an exception is thrown.
	 *
	 * @param string $workflowId the id of the workflow to load
	 * @param boolean $forceReload force workflow reload
	 */
	abstract public function loadWorkflow($workflowId, $forceReload = false);

	/**
	 * Search for the node passed as argument in the workflow definition. Note that if
	 * this node is not found among the currently loaded workflows, this method will try
	 * to load the workflow it belongs to.
	 *
	 * @param mixed $node String or SWNode object to look for
	 * @param null $defaultWorkflowId
	 * @return SWNode the node as it is defined in a workflow, or NULL if not found
	 */
	abstract public function getNodeDefinition($node, $defaultWorkflowId = null);

	/**
	 * Returns an array containing all SWNode object for each status that can be reached
	 * from $startStatus. It does not evaluate node constraint but only the fact that a transition
	 * exist between $startStatus and nodes returned. If no nodes are found, an empty array is returned.
	 * An exception is thrown if $startStatus is not found among all workflows available.
	 *
	 * @param $sourceNode
	 * @param null $workflowId
	 * @return array SWNode array
	 */
	abstract public function getNextNodes($sourceNode, $workflowId = null);

	/**
	 * Checks if there is a transition between the two nodes passed as argument.
	 *
	 * @param mixed $sourceNode can be provided as a SWNode object, or as a string that can contain a workflowId or not.
	 * @param mixed $targetNode target node to test
	 * @param mixed $workflowId
	 * @return boolean true if $nextStatus can be reached from $startStatus
	 */
	abstract public function isNextNode($sourceNode, $targetNode, $workflowId = null);

	/**
	 * Returns the initial node defined for the workflow whose id is passed as
	 * argument. A valid workflow must have one and only one initial status. If it's
	 * note the case, workflow can't be loaded.
	 *
	 * @param $workflowId
	 * @return SWNode initial node for $workflowId
	 */
	abstract public function getInitialNode($workflowId);

	/**
	 * Fetch all nodes belonging to the workflow whose Id is passed as argument.
	 *
	 * @param string $workflowId id of the workflow that owns all nodes returned
	 * @return array all nodes belonging to workflow $workflowId
	 */
	abstract public function getAllNodes($workflowId);

}
