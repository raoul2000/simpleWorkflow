<?php

/**
 * This class gives access to workflow and statuses stored as PHP files.
 * Following attributes can be initialized when the component is configured:
 * <ul>
 * <li><b>basePath</b> (string) : the base path alias where all workflow are stored.By default, it is set to
 * application.models.workflows (folder  "protected/models/workflows").
 * </li>
 * <li><b>definitionType</b> (string) :  Defines the type of PHP file to load. A Workflow can be defined in
 * a PHP file that contains a simple array definition (definitionType = 'array'), or by a
 * class (definitionType = 'class'). By default this attribute is set to 'array'.
 * </li>
 * </ul>
 */
class SWPhpWorkflowSource extends SWWorkflowSource
{
	/**
	 * @var string the base path alias where all workflow are stored.By default, it is set to
	 * application.models.workflows (folder  "protected/models/workflows").
	 */
	public $basePath = 'application.models.workflows';
	/**
	 * @var string Definition type for workflow. Allowed values are : class, array. Default is 'array'
	 */
	public $definitionType = 'array';

	/**
	 * workflow definition collection
	 * @var
	 */
	private $_workflow;
	private $_workflowBasePath;

	/**
	 * Initialize the component with configured values. To pre-load workflows, set configuration
	 * setting 'preload' to an array containing all workflows to pre-load. If no pre-load is set
	 * workflows are loaded on demand.
	 *
	 * @see SWWorkflowSource
	 */
	public function init()
	{
		parent::init();
		$this->_workflowBasePath = Yii::getPathOfAlias($this->basePath);

		if (is_array($this->preload) && count($this->preload)) {
			foreach ($this->preload as $wfId)
				$this->_load($wfId, true);
		}

		if ($this->definitionType == 'class')
			Yii::import($this->basePath . '.*');
	}

	/**
	 * Loads a workflow from a php source file into the $this->_workflow associative array. A call to reset() will unload all workflows.
	 */
	private function _load($wfId, $forceReload)
	{
		if (!is_string($wfId) || empty($wfId))
			throw new SWException('Failed to load workflow - invalid workflow Id: ' . $wfId, SWException::SW_ERR_WORKFLOW_ID);

		if (!isset($this->_workflow[$wfId]) || $forceReload === true) {
			if ($this->definitionType == 'class') {
				$wo = new $wfId;
				$this->_workflow[$wfId] = $this->_createWorkflow($wo->getDefinition(), $wfId);
			} elseif ($this->definitionType == 'array') {
				$fname = $this->_workflowBasePath . DIRECTORY_SEPARATOR . $wfId . '.php';

				if (file_exists($fname) === false)
					throw new SWException("Workflow definition file not found: {$fname}", SWException::SW_ERR_WORKFLOW_NOT_FOUND);

				$this->_workflow[$wfId] = $this->_createWorkflow(require($fname), $wfId);
			}
		}

		return $this->_workflow[$wfId];
	}

	/**
	 * @param array $wf workflow definition
	 * @param string $wfId workflow Id
	 * @return array
	 * @throws SWException
	 */
	private function _createWorkflow($wf, $wfId)
	{
		if (!is_array($wf) || empty($wfId))
			throw new SWException('Invalid argument');

		$wfDefinition = array();

		if (!isset($wf['initial']))
			throw new SWException('missing initial status for workflow: ' . $wfId, SWException::SW_ERR_IN_WORKFLOW);

		/**
		 * load node list
		 */
		$nodeIds = array();

		foreach ($wf['node'] as $rnode) {
			$node = new SWNode($rnode, $wfId);

			if (in_array($node->getId(), $nodeIds))
				throw new SWException('Duplicate node id: ' . $node->getId(), SWException::SW_ERR_IN_WORKFLOW);

			$nodeIds[] = $node->getId();
			$wfDefinition[$node->getId()] = $node;

			if ($node->getId() == $wf['initial'] || $node->toString() == $wf['initial'])
				$wfDefinition['swInitialNode'] = $node;
		}

		/**
		 * checks that initial node is set
		 */
		if (!isset($wfDefinition['swInitialNode']))
			throw new SWException('Missing initial status for workflow: ' . $wfId, SWException::SW_ERR_IN_WORKFLOW);

		return $wfDefinition;
	}

	/**
	 * Returns the SWNode object from the workflow collection.
	 *
	 * @param SWNode $swNode node to search for in the node list
	 * @return  SWNode the SWNode object retrieved from the workflow collection, or NULL if this node could not be found in the workflow collection
	 * @throws SWException
	 */
	private function _getNode($swNode)
	{
		$wfId = $swNode->getWorkflowId();

		if ($wfId === null)
			throw new SWException('Workflow not found: ' . $wfId, SWException::SW_ERR_WORKFLOW_NOT_FOUND);

		$this->_load($wfId, false);
		$nodeId = $swNode->getId();

		if (isset($this->_workflow[$wfId][$nodeId]))
			return $this->_workflow[$wfId][$nodeId];

		return null;
	}

	/**
	 * Verify if a workflow has been loaded.
	 *
	 * @param string $workflowId workflow id
	 * @return boolean TRUE if the workflow whose id is $workflowId has already been loaded, FALSE otherwise
	 */
	public function isWorkflowLoaded($workflowId)
	{
		return isset($this->_workflow[$workflowId]);
	}

	/**
	 * Loads the workflow whose id is passed as argument.
	 * By default, if the workflow has already been loaded it is not reloaded unless
	 * $forceReload is TRUE
	 *
	 * @param string $workflowId  the workflow id
	 * @param bool $forceReload  TRUE to force workflow loading, FALSE otherwise
	 * @return bool
	 */
	public function loadWorkflow($workflowId, $forceReload = false)
	{
		return ($this->_load($workflowId, $forceReload) !== null);
	}

	/**
	 * This method is used to add a new workflow definition to the current workflow collection.
	 *
	 * @param array $definition the workflow definition in its array form
	 * @param string $id the workflow id
	 * @throws SWException
	 */
	public function addWorkflow($definition, $id)
	{
		if (!is_array($definition))
			throw new SWException('Array expected');

		if (!isset($this->_workflow[$id]))
			$this->_workflow[$id] = $this->_createWorkflow($definition, $id);
	}

	/**
	 * @see SWWorkflowSource::getNodeDefinition()
	 */
	public function getNodeDefinition($node, $defaultWorkflowId = null)
	{
		return $this->_getNode($this->createSWNode($node, $defaultWorkflowId));
	}

	/**
	 * @see SWWorkflowSource::getNextNodes()
	 */
	public function getNextNodes($sourceNode, $workflowId = null)
	{
		$result = array();

		/**
		 * convert startStatus into SWNode
		 */
		$startNode = $this->getNodeDefinition($this->createSWNode($sourceNode, $workflowId));

		if ($startNode === null)
			throw new SWException('Node could not be found: ' . $sourceNode, SWException::SW_ERR_NODE_NOT_FOUND);

		foreach ($startNode->getNext() as $nxtNodeId => $tr)
			$result[] = $this->_getNode(new SWNode($nxtNodeId, $workflowId));

		return $result;
	}

	/**
	 * @see SWWorkflowSource::isNextNode()
	 */
	public function isNextNode($sourceNode, $targetNode, $workflowId = null)
	{
		$startNode = $this->createSWNode($sourceNode, $workflowId);

		$nextNode = $this->createSWNode($targetNode,
			($workflowId != null
				? $workflowId
				: $startNode->getWorkflowId()
			)
		);

		$nxt = $this->getNextNodes($startNode);

		if ($nxt !== null)
			return in_array($nextNode->toString(), $nxt);

		return false;
	}

	/**
	 * @see SWWorkflowSource::getInitialNode()
	 */
	public function getInitialNode($workflowId)
	{
		$this->_load($workflowId, false);
		return $this->_workflow[$workflowId]['swInitialNode'];
	}

	/**
	 * @see SWWorkflowSource::getAllNodes()
	 */
	public function getAllNodes($workflowId)
	{
		$result = array();
		$wf = $this->_load($workflowId, false);

		foreach ($wf as $key => $value) {
			if ($key !== 'swInitialNode')
				$result[] = $value;
		}

		return $result;
	}
}
