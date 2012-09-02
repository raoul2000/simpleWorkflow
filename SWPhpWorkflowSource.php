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
class SWPhpWorkflowSource extends SWWorkflowSource {
	/**
	 * @var string the base path alias where all workflow are stored.By default, it is set to
	 * application.models.workflows (folder  "protected/models/workflows").
	 */
	public $basePath = 'application.models.workflows';
	/**
	 * @var string Definition type for workflow. Allowed values are : class, array. Default is 'array'
	 */
	public $definitionType = 'array';
	
	private $_workflow;	// workflow definition collection
	private $_workflowBasePath;
	/**
	 * Initialize the component with configured values. To preload workflows, set configuration
	 * setting 'preload' to an array containing all workflows to preload. If no preload is set
	 * workflows are loaded on demand.
	 *
	 * @see SWWorkflowSource
	 */
	public function init()
	{
		parent::init();
		$this->_workflowBasePath = Yii::getPathOfAlias($this->basePath);
		if( is_array($this->preload) and count($this->preload)!=0){
			foreach ( $this->preload as $wfId ) {
				Yii::t('simpleWorkflow','preloading workflow : {name}',array('{name}'=>$wfId));
				$this->_load($wfId,true);
			}
		}
		if( $this->definitionType == 'class'){
			Yii::import($this->basePath.'.*');
		}
		Yii::trace(Yii::t('simpleWorkflow','SWWorkflowSource initialized - basePath : '.$this->basePath),'application.simpleWorkflow');
	}
	
	//
	///////////////////////////////////////////////////////////////////////////////////
	// private methods
		
	/**
	 * Loads a workflow from a php source file into the $this->_workflow
	 * associative array. A call to reset() will unload all workflows.
	 */
	private function _load($wfId, $forceReload)
	{
		if( !is_string($wfId) or empty($wfId))
		{
			throw new SWException(Yii::t('simpleWorkflow','failed to load workflow - invalid workflow Id : {workflowId}',
				array('{workflowId}'=>$wfId)),
				SWException::SW_ERR_WORKFLOW_ID);
		}
		
		if( !isset($this->_workflow[$wfId]) or $forceReload==true)
		{
			
			if($this->definitionType == 'class')
			{
				$wo = new $wfId;
				$this->_workflow[$wfId] = $this->_createWorkflow($wo->getDefinition(),$wfId);
			}
			elseif( $this->definitionType == 'array')
			{
				$fname=$this->_workflowBasePath.DIRECTORY_SEPARATOR.$wfId.'.php';
				if( file_exists($fname)==false){
					throw new SWException(Yii::t('simpleWorkflow','workflow definition file not found : {file}',
						array('{file}'=>$fname)),
						SWException::SW_ERR_WORKFLOW_NOT_FOUND
					);
				}
				
				Yii::trace(
					Yii::t('simpleWorkflow','loading workflow {wfId} from file {file}',
						array('{wfId}'=>$wfId,'{file}'=>$fname)
					),
					'application.simpleWorkflow'
				);
								
				$this->_workflow[$wfId] = $this->_createWorkflow(require($fname),$wfId);
			}
		}
		return $this->_workflow[$wfId];
	}
	/**
	 * @param array $wf workflow definition
	 * @param string $wfId workflow Id
	 */
	private function _createWorkflow($wf,$wfId)
	{
		if(!is_array($wf) || empty($wfId)){
			throw new SWException(Yii::t('simpleWorkflow','invalid argument'));
		}
		$wfDefinition=array();
		
		if( !isset($wf['initial'])) {
			throw new SWException(Yii::t('simpleWorkflow','missing initial status for workflow {workflow}',
				array('{workflow}'=>$wfId)),
				SWException::SW_ERR_IN_WORKFLOW
			);
		}
			
		// load node list
		$nodeIds = array();
		foreach($wf['node'] as $rnode)
		{
			$node=new SWNode($rnode,$wfId);
			
			if(in_array($node->getId(),$nodeIds )){
				throw new SWException(Yii::t('simpleWorkflow','duplicate node id {nodeId}',
					array('{nodeId}'=>$node->getId())),
					SWException::SW_ERR_IN_WORKFLOW
				);
			}else{
				$nodeIds[] = $node->getId();
			}
			
			$wfDefinition[$node->getId()]=$node;
			if($node->getId()==$wf['initial']){
				$wfDefinition['swInitialNode']= $node;
			}
		}
		// checks that initialnode is set
		 
		if(!isset($wfDefinition['swInitialNode']))
			throw new SWException(Yii::t('simpleWorkflow','missing initial status for workflow {workflow}',
				array('{workflow}'=>$wfId)),
				SWException::SW_ERR_IN_WORKFLOW
			);
		
		return $wfDefinition;
	}
	/**
	 * Returns the SWNode object from the workflow collection.
	 *
	 * @param SWnode swNode node to search for in the node list
	 * @return SWNode the SWNode object retrieved from the workflow collection, or NULL if this
	 * node could not be found in the workflow collection
	 */
	private function _getNode($swNode){

		$wfId=$swNode->getWorkflowId();
		if($wfId==null)
		{
			throw new SWException(Yii::t('simpleWorkflow','workflow {workflow} not found',
				array('{workflow}'=>$wfId)),
				SWException::SW_ERR_WORKFLOW_NOT_FOUND
			);
		}
		
		$this->_load($wfId,false);
		$nodeId=$swNode->getId();
		if(isset($this->_workflow[$wfId][$nodeId])){
			return $this->_workflow[$wfId][$nodeId];
		}else {
			return null;
		}
	}

	//
	///////////////////////////////////////////////////////////////////////////////////
	//	public methods
	/**
	 * Verify if a workflow has been loaded.
	 *
	 * @param string $workflowId workflow id
	 * @return boolean TRUE if the workflow whose id is $workflowId has already been loaded,
	 * FALSE otherwise
	 */
	public function isWorkflowLoaded($workflowId){
		return isset($this->_workflow[$workflowId]);
	}
	/**
	 * Loads the workflow whose id is passed as argument.
	 * By default, if the workflow has already been loaded it is not reloaded unless
	 * $forceReload is TRUE
	 * @param string $workflowId the workflow id
	 * @param boolean $forceReload TRUE to force workflow loading, FALSE otherwise
	 */
	public function loadWorkflow($workflowId,$forceReload=false){
		return $this->_load($workflowId,$forceReload) != null;
	}
	/**
	 * This method is used to add a new workflow definition to the current workflow collection.
	 * @param array $definition the workflow definition in its array form
	 * @param string $id the workflow id
	 */
	public function addWorkflow($definition, $id){
		if(!is_array($definition))
			throw new SWException(Yii::t('simpleWorkflow','array expected'));
			
		if(isset($this->_workflow[$id])){
			Yii::trace(Yii::t('simpleWorkflow','workflow {workflow} already loaded',array('{workflow}'=>$id))	,'application.simpleWorkflow');
		}else {
			$this->_workflow[$id] = $this->_createWorkflow($definition,$id);
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see SWWorkflowSource::getNodeDefinition()
	 */
	public function getNodeDefinition($node, $defaultWorkflowId=null)
	{
		return $this->_getNode(
			$this->createSWNode($node,$defaultWorkflowId)
		);
	}
	/**
	 * (non-PHPdoc)
	 * @see SWWorkflowSource::getNextNodes()
	 */
	public function getNextNodes($sourceNode,$workflowId=null){
		$result=array();
		
		// convert startStatus into SWNode
		$startNode=$this->getNodeDefinition(
			$this->createSWNode($sourceNode,$workflowId)
		);
		
		if($startNode==null){
			throw new SWException(Yii::t('simpleWorkflow','node {node} could not be found',
				array('{node}'=>$sourceNode),
				SWException::SW_ERR_NODE_NOT_FOUND
			));
		}else {
			foreach($startNode->getNext() as $nxtNodeId => $tr){
				$result[]=$this->_getNode(new SWNode($nxtNodeId,$workflowId));
			}
		}
		return $result;
	}
	/**
	 * (non-PHPdoc)
	 * @see SWWorkflowSource::isNextNode()
	 */
	public function isNextNode($sourceNode,$targetNode,$workflowId=null){
		
		$startNode=$this->createSWNode($sourceNode,$workflowId);
		$nextNode=$this->createSWNode(
			$targetNode,
			( $workflowId!=null
				? $workflowId
				: $startNode->getWorkflowId()
			)
		);
				
		$nxt=$this->getNextNodes($startNode);
		if( $nxt != null){
			return in_array($nextNode->toString(),$nxt);
		}else {
			return false;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see SWWorkflowSource::getInitialNode()
	 */
	public function getInitialNode($workflowId){
		$this->_load($workflowId,false);
		return $this->_workflow[$workflowId]['swInitialNode'];
	}
	/**
	 * (non-PHPdoc)
	 * @see SWWorkflowSource::getAllNodes()
	 */
	public function getAllNodes($workflowId)
	{
		$result=array();
		$wf=$this->_load($workflowId,false);
		foreach($wf as $key => $value){
			if($key!='swInitialNode'){
				$result[]=$value;
			}
		}
		return $result;
	}
}
?>
