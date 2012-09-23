<?php
/**
 * This class implements a graph node for the simpleWorkflow extension.
 */
class SWNode extends CComponent {
	/**
	 * @var string workflow identifier
	 */
	private $_workflowId;
	/**
	 * @var string node identifier which must be unique within the workflow
	 */
	private $_id;
	/**
	 * @var string user friendly node name. If not provided at construction, the string
	 * 'workflowId/nodeId' will be used.
	 */
	private $_label;
	/**
	 * @var string expression evaluated in the context of an CActiveRecord object. It must returns
	 * a boolean value that is used to allow access to this node.
	 */
	private $_constraint = array();
	/**
	 * @var array
	 */
	private $_metadata = array();
	/**
	 * @var array array of transitions that exist between this node and other nodes
	 */
	private $_tr=array();
	
	/**
	 * Creates a workflow node instance.
	 * If no workflowId is specified in the nodeId, then the $defaultWorkflowId is used.<br/>
	 * Note that both workflow and node id must begin with a alphabetic character followed by aplha-numeric
	 * characters : all other characters are not accepted and cause an exception to be thrown (see {@link SWNode::parseNodeId()})
	 *
	 * @param mixed $node If a string is passed as argument, it can be both in format workflowId/NodeId
	 * or simply 'nodeId'. In this last case, argument $defaultWorkflowIs must be provided, otherwise it is
	 * ignored. <br/>
	 * The $node argument may also be provided as an associative array, with the following structure :<br/>
	 * <pre>
	 * 	{
	 * 		'id'         => string,			// mandatory
	 * 		'label'      => string ,		// optional
	 * 		'constraint' => string,			// optional
	 * 		'transition' => array,			// optional
	 * 		'metadata'   => array,			// optional
	 * 	}
	 * </pre>
	 * Again, the 'id' value may contain a workflow id (e.g 'workflowId/nodeId') but if it's not the case then
	 * the second argument $defaultWorkflowId must be provided.
	 * @param string defaultWorkflowId workflow Id that is used each time a workflow is needed to complete
	 * a status name.
	 */
	public function __construct($node, $defaultWorkflowId=null){
		
		if($node==null || empty($node))
			throw new SWException(Yii::t('simpleWorkflow','illegal argument exception : $node cannot be empty'), SWException::SW_ERR_CREATE_NODE);
		
		$st=array();
		
		if( $node instanceof SWNode )
		{
			// copy constructor : does not copy transitions, constraints and metadata
			
			$this->_workflowId = $node->getWorkflowId();
			$this->_id 	       = $node->getId();
			$this->_label      = $node->getLabel();
			$this->_metadata   = $node->getMetadata();
		}
		else {
			if( is_array($node))
			{
				if(!isset($node['id']))
					throw new SWException(Yii::t('simpleWorkflow','missing node id'),SWException::SW_ERR_MISSING_NODE_ID);
					
				// set node id -----------------------
			
				$st=$this->parseNodeId($node['id'],$defaultWorkflowId);
			
				if(isset($node['label'])){
					$this->_label=$node['label'];
				}
			
				if(isset($node['constraint'])){
					$this->_constraint=$node['constraint'];
				}
			
				if(isset($node['transition'])){
					$this->_loadTransition($node['transition'],$st['workflow']);
				}
			
				if(isset($node['metadata'])){
					$this->_metadata = $node['metadata'];
				}
			}
			elseif(is_string($node))
			{
				$st=$this->parseNodeId($node,$defaultWorkflowId);
			}
			
			$this->_workflowId = $st['workflow'];
			$this->_id 	       = $st['node'];
			
			if(!isset($this->_label))
				$this->_label=$this->_id;
		}
	}
	/**
	 * Parse a status name and return it as an array. The string passed as argument
	 * may be a complete status name (e.g workflowId/nodeId) and if no workflowId is
	 * specified, then an exception is thrown. Both workflow and node ids must match
	 * following patter:
	 * <pre>
	 *      [[:alpha:]][[:alnum:]]*
	 * </pre>
	 * @param string status status name (wfId/nodeId or nodeId)
	 * @return array the complete status (e.g array ( [workflow] => 'a' [node] => 'b' ))
	 */
	public function parseNodeId($status,$workflowId){
		$nodeId=$wfId=null;

		if(strstr($status,'/')){
			if(preg_match('/^([[:alpha:]][[:alnum:]_]*)\/([[:alpha:]][[:alnum:]_]*)$/',$status,$matches) == 1){
				$wfId   = $matches[1];
				$nodeId = $matches[2];
			}
		}
		else{
			if(preg_match('/^[[:alpha:]][[:alnum:]_]*$/',$status) == 1){
				$nodeId = $status;
				if(preg_match('/^[[:alpha:]][[:alnum:]_]*$/',$workflowId) == 1){
					$wfId = $workflowId;
				}
			}
		}
	
		if( $wfId == null || $nodeId == null){
			throw new SWException(Yii::t('simpleWorkflow','failed to create node from node Id = {nodeId}, workflow Id = {workflowId}',
				array('{nodeId}'=>$status,'{workflowId}'=>$workflowId)), SWException::SW_ERR_CREATE_NODE);
		}
		return array('workflow'=>$wfId,'node'=>$nodeId);
	}
	/**
	 * Overrides the default magic method defined at the CComponent level in order to
	 * return a metadata value if parent method fails.
	 *
	 * @see CComponent::__get()
	 */
	public function __get($name)
	{
		try{
			return parent::__get($name);
		}catch(CException $e){
			
			if(isset($this->_metadata[$name])){
				return $this->_metadata[$name];
			}else{
				throw new SWException(Yii::t('yii','Property "{property}" is not found.',
					array('{property}'=>$name)),SWException::SW_ERR_ATTR_NOT_FOUND);
			}
		}
	}
	/**
	 * Loads the set of transitions passed as argument.
	 *
	 * @param mixed $tr if provided as a string, it is a comma separated list of SWNodes id,
	 * This list can also be provided as an array
	 * @param string $defWfId Default workflow Id if nodes have no workflow id, this value is used
	 * as their workflow id.
	 */
	private function _loadTransition($tr, $defWfId)
	{
		if( is_string($tr))
		{
			$trAr=explode(',',$tr);
			foreach($trAr as $aTr)
			{
				$objNode=new SWNode(trim($aTr),$defWfId);
				$this->_tr[$objNode->toString()]=null;
			}
		}
		elseif( is_array($tr))
		{
			foreach($tr as $key => $value){
				if( is_string($key)){
					$objNode=new SWNode(trim($key),$defWfId);
					if($value!=null)
						$this->_tr[$objNode->toString()]=$value;
					else
						$this->_tr[$objNode->toString()]=null;
				}else {
					$objNode=new SWNode(trim($value),$defWfId);
					$this->_tr[$objNode->toString()]=null;
				}
			}
		}else {
			throw new SWException(__FUNCTION__. 'incorrect arg type : string or array expected');
		}
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////
	// accessors

	public function getWorkflowId() 	{return $this->_workflowId;}
	public function getId() 			{return $this->_id;}
	public function getLabel() 			{return $this->_label;}
	public function getNext() 			{return $this->_tr;}
	public function getConstraint() 	{return $this->_constraint;}
	public function getMetadata() 		{return $this->_metadata;}
	public function getNextNodeIds()    {return array_keys($this->_tr);}
	/**
	 * @returns String the task for this transition or NULL if no task is defined
	 * @param mixed $endNode SWNode instance or string that will be converted to SWNode instance (e.g 'workflowId/nodeId')
	 * @throws SWException
	 */
	public function getTransitionTask($endNode){
		
		if( ! $endNode instanceof SWNode ){
			$endNode = new SWNode($endNode, $this->getWorkflowId());
		}
		$endNodeId = $endNode->toString();
					
		return ( isset($this->_tr[$endNodeId])
			? $this->_tr[$endNodeId]
			: null
		);
	}

	public function __toString(){
		return $this->getWorkflowId().'/'.$this->getId();
	}
	public function toString(){
		return $this->__toString();
	}
	/**
	 * SWnode comparator method. Note that only the node and the workflow id
	 * members are compared.
	 *
	 * @param mixed SWNode object or string. If a string is provided it is used to create
	 * a new SWNode object.
	 */
	public function equals($status){

		if( $status instanceof SWNode )
		{
			return  $status->toString() == $this->toString();
		}
		else try{
			$other=new SWNode($status,$this->getWorkflowId());
			return $other->equals($this);
		}catch(Exception $e)
		{
			throw new SWException(Yii::t('simpleWorkflow','comparaison error - the value passed as argument (value={value}) cannot be converted into a SWNode',
				array('{value}'=> $status)), $e->getCode());
		}
	}
}
?>