<?php
/**
 * This class implements all the logic for the simpleWorkflow extension.
 * Following attributes can be initialized when this behavior is attached to the owner component :
 * <ul>
 * <li><b>statusAttribute</b> (string) : This is the column name where status is stored<br/>
 * 		If this attribute doesn't exist for  a model, the Workflow behavior is automatically disabled and a warning is
 * 		logged.<br/>
 * 		In the database, this attribute must be defined as a VARCHAR() whose length should be large enough to
 * 		contains a complete status name with format <b>workflowId/nodeId</b>.<br/>
 * example :
 * <pre>
 * task/pending
 * postWorkflow/to_review
 * </pre>
 * Default : 'status'
 * </li>
 * <li><b>defaultWorkflow</b> (string) : workflow name that should be used by default for the owner model <br/>
 * 		If this parameter is not set, then it is automatically created based on the name of the owner model, prefixed
 * 		with 'workflowNamePrefix' defined by the workflow source component. By default this value is set to 'sw' and so,
 * 		for example 'Model1' is associated by default with workflow 'swModel1'.<br/>
 * 		Default : SWWorkflowSource->workflowNamePrefix . ModelName
 * </li>
 * <li><b>autoInsert</b> (boolean) : <br/>
 * If TRUE, the model is automatically inserted in the workflow (if not already done) when it is saved.
 * If FALSE, it is developer responsability to insert the model in the workflow.<br/>
 * Default : true
 * </li>
 * <li><b>workflowSourceComponent</b> (string) : <br/>
 * Name of the workflow source component to use with this behavior.<br/>
 * By ddefault this parameter is set to 'swSource'(see {@link SWPhpWorkflowSource})
 * </li>
 * <li><b>enableEvent</b> (boolean) : <br/>
 * If TRUE, this behavior will fire SWEvents. Note that even if it
 * is true, this doesn't garantee that SW events will be fired as another condition is that the owner
 * component provides SWEvent handlers.<br/>
 * Default : true
 * </li>
 * <li><b>transitionBeforeSave</b> (boolean) : <br/>
 * If TRUE, SWEvents are fired and possible transitions tasks are executed <b>before</b> the owner model is
 * actually saved. If FALSE, events and task transitions are processed after save.<br/>
 * It has no effect if the transition is done programatically by a call to swNextStatus(), but only if it is done when the
 * owner model is saved.<br/>
 * Default : true
 * </li>
 * </ul>
 */
class SWActiveRecordBehavior extends CBehavior {
	/**
	 * @var string  This is the column name where status is stored.
	 */
	public $statusAttribute = 'status';
	/**
	 * @var string workflow name that should be used by default for the owner model.
	 */
	public $defaultWorkflow=null;
	/**
	 * @var boolean
	 */
	public $autoInsert=true;
	/**
	 * @var string name of the workflow source component
	 */
	public $workflowSourceComponent='swSource';
	/**
	 * @var boolean
	 */
	public $enableEvent=true;
	/**
	 * @var boolean
	 */
	public $transitionBeforeSave=true;
	
	///////////////////////////////////////////////////////////////////////////////////////////
	// private members
	
	private $_delayedTransition=null;			// delayed transition  (only when change status occures during save)
	private $_delayedEvent=array();				// delayed event stack (only when change status occures during save)
	private $_beforeSaveInProgress=false;		// prevent delayed event fire when status is changed by a call to swNextStatus
	private $_status=null;						// internal status for the owner model
	private $_wfs;								// workflow source component reference
	private $_locked=false;						// prevent reentrance
	private $_final=null;
		
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	
	/**
	 * @var string name of the class the owner should inherit from in order for SW events
	 * to be enabled.
	 */
	protected $eventClassName='SWActiveRecord';

	const SW_LOG_CATEGORY='application.simpleWorkflow';
	const SW_I8N_CATEGORY='simpleworkflow';


	/**
	 * @return reference to the workflow source used by this behavior
	 */
	public function swGetWorkflowSource(){
		return $this->_wfs;
	}
	/**
	 * Checks that the owner component is able to handle workflow events that could be fired
	 * by this behavior
	 *
	 * @param CComponent $owner the owner component attaching this behavior
	 * @param string $className
	 * @return bool TRUE if workflow events are fired, FALSE if not.
	 */
	protected function canFireEvent($owner,$className){
		return $owner instanceof $className;
	}
	/**
	 * If the owner component is inserted into a workflow, this method returns the SWNode object
	 * that represent this status, otherwise NULL is returned.
	 *
	 * @return SWNode the current status or NULL if no status is set
	 */
	public function swGetStatus(){
		return $this->_status;
	}
	/**
	 * Event may be enabled by configuration (when the behavior is attached to the owner component) but it
	 * can be automatically disabled if the owner component does not define handlers for all SWEvents (i.e events
	 * fired when the owner component evolves in the workflow).
	 * {@link SWActiveRecordBehavior::attach}
	 *
	 * @return bool TRUE if workflow events are fire by this behavior, FALSE if not.
	 */
	public function swIsEventEnabled(){
		return $this->enableEvent;
	}
	/**
	 * Test if the owner component is currently in the status passed as argument.
	 *
	 * @param mixed $status name or SWNode instance of the status to test
	 * @returns boolean TRUE if the owner component is in the status passed as argument, FALSE otherwise
	 */
	public function swIsStatus($status){
		return $this->swHasStatus() && $this->swGetStatus()->equals($status);
	}
	/**
	 * Test if the current status is the same as the one passed as argument.
	 * A call to swStatusEquals(<em>null</em>) returns TRUE only if the owner component is not in a workflow.
	 *
	 * @param mixed $status string or SWNode instance.
	 * @return boolean
	 */
	public function swStatusEquals($status=null){
		
		if( ($status == null && $this->swHasStatus() == false) ||
			($status != null && $this->swHasStatus() &&  $this->swGetStatus()->equals($status)) )
			return true;
		else
			return false;
	}
	/**
	 * Test if the owner component is currently inserted in a workflow.
	 * This method is equivalent to swGetStatus()!=null.
	 *
	 * @return boolean true if the owner model is in a workflow, FALSE otherwise
	 * @see swGetStatus
	 */
	public function swHasStatus(){
		return ! $this->_status == null;
	}
	/**
	 * acquire the lock in order to avoid reentrance
	 *
	 * @throws SWException
	 */
	private function _lock(){
		if($this->_locked==true){
			throw new SWException(Yii::t(self::SW_I8N_CATEGORY,'re-entrant exception on set status'),
				SWException::SW_ERR_REETRANCE
			);
		}
		$this->_locked=true;
	}
	/**
	 * Release the lock
	 */
	private function _unlock(){
		$this->_locked=false;
	}
	/**
	 * Update the owner model attribute configured to store the current status and the internal
	 * value too.
	 *
	 * @param SWnode $SWNode internal status is set to this node
	 */
	private function _updateStatus($SWNode){
		if(! $SWNode instanceof SWNode)
			throw new SWException(Yii::t(self::SW_I8N_CATEGORY,'SWNode object expected'),SWException::SW_ERR_WRONG_TYPE);
		Yii::trace('_updateStatus : '.$SWNode->toString(),self::SW_LOG_CATEGORY);
		$this->_status=$SWNode;
		$this->_final = null;
	}
	/**
	 * Updates the owner component status attribute with the value passed as argument.
	 *
	 * @param mixed $status the new owner status value provided as a SWNode object or string
	 */
	private function _updateOwnerStatus($status){
		
		if($status instanceof SWNode)
			$this->getOwner()->{$this->statusAttribute} = $status->toString();
		elseif( is_string($status))
			$this->getOwner()->{$this->statusAttribute} = $status;
		else
			throw new SWException(Yii::t(self::SW_I8N_CATEGORY,'SWNode or string expected'),SWException::SW_ERR_WRONG_TYPE);
	}
	/**
	 * Returns the current workflow Id the owner component is inserted in, or NULL if the owner
	 * component is not inserted into a workflow.
	 *
	 * @param string current workflow Id or NULL
	 */
	public function swGetWorkflowId() {
		return ($this->swHasStatus()?$this->_status->getWorkflowId():null);
	}
	/**
	 * Overloads parent attach method so at the time the behavior is about to be
	 * attached to the owner component, the behavior is initialized.<br/>
	 * During the initialisation, following actions are performed:<br/>
	 * <ul>
	 * <li>The status attribute exists</li>
	 * <li>Check whether or not, workflow events should be enabled, by testing if the owner component
	 * class inherits from the 'SWComponent' or 'SWActiveRecord' class. </li>
	 * </ul>
	 *
	 * @see base/CBehavior::attach()
	 */
	public function attach($owner){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
			
		if( ! $this->canFireEvent($owner, $this->eventClassName)){
			if( $this->swIsEventEnabled()){
				
				// workflow events are enabled by configuration but the owner component is not
				// able to handle workflow event : warning
				
				Yii::log(Yii::t(self::SW_I8N_CATEGORY,'events disabled : owner component doesn\'t inherit from {className}',
							array('{className}' => $this->eventClassName)),
					CLogger::LEVEL_WARNING,self::SW_LOG_CATEGORY);
			}
			$this->enableEvent=false;	// force
		}
		
		parent::attach($owner);

		if( $this->getOwner() instanceof CActiveRecord ){
		
			$statusAttributeCol = $this->getOwner()->getTableSchema()->getColumn($this->statusAttribute);
			if(!isset($statusAttributeCol) || $statusAttributeCol->type != 'string' )
			{
				throw new SWException(Yii::t(self::SW_I8N_CATEGORY,'attribute {attr} not found',
					array('{attr}'=>$this->statusAttribute)),SWException::SW_ERR_ATTR_NOT_FOUND);
			}
		}
		// preload the workflow source component
		$this->_wfs= Yii::app()->{$this->workflowSourceComponent};
		
		// load the default workflow id now because the owner model maybe able to provide it
		// together with the whole workflow definition. In this case, this definition must be pushed
		// to the SWWorkflowSource component (done by swGetDefaultWorkflowId).
	
		$defWid = $this->swGetDefaultWorkflowId();
		
		// autoInsert now !
		
		if($this->autoInsert == true && $this->getOwner()->{$this->statusAttribute} == null){
			$this->swInsertToWorkflow($defWid);
		}
	}
	/**
	 * Finds out what should be the default workflow to use with the owner model.
	 * To find out what is the default workflow, this method perform following tests :
	 * <ul>
	 * 	<li>behavior initialization parameter <i>defaultWorkflow</i></li>
	 * 	<li>owner component method <i>workflow</i> : if the owner component is able to provide the
	 * complete workflow, this method will invoke SWWorkflowSource.addWorkflow</li>
	 *  <li>created based on the configured prefix followed by the model class name. The default workflow prefix is 'sw' so
	 *  if the owner model is MyModel, the default workflow id will be swMyModel (case sensitive) </li>
	 * </ul>
	 * @return string workflow id to use with the owner component or NULL if no workflow was found
	 */
	public function swGetDefaultWorkflowId(){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		
		if( $this->defaultWorkflow == null)
		{
			$workflowName=null;
			if( $this->defaultWorkflow != null)
			{
				// the behavior has been initialized with the default workflow name
				
				$workflowName=$this->defaultWorkflow;
			}
			elseif(method_exists($this->getOwner(),'workflow'))
			{
				
				$wf=$this->getOwner()->workflow();
				if( is_array($wf)){
					
					// Cool ! the owner is able to provide its own private workflow definition ...and optionally
					// a workflow name too. If no workflow name is provided, the model name is used to
					// identity the workflow
					
					$workflowName=(isset($wf['name'])
						? $wf['name']
						: $this->swGetWorkflowSource()->workflowNamePrefix.get_class($this->getOwner())
					);
					
					$this->swGetWorkflowSource()->addWorkflow($wf,$workflowName);
					Yii::trace('workflow provided by owner',self::SW_LOG_CATEGORY);
					
				}elseif(is_string($wf)) {
					
					// the owner returned a string considered as its default workflow Id
	
					$workflowName=$wf;
				}else {
					throw new SWException(Yii::t(self::SW_I8N_CATEGORY, 'incorrect type returned by owner method : string or array expected'),
						SWException::SW_ERR_WRONG_TYPE);
				}
			}else {
	
				// ok then, let's use the owner model name as the workflow name and hope that
				// its definition is available in the workflow basePath.
				
				$workflowName=$this->swGetWorkflowSource()->workflowNamePrefix.get_class($this->getOwner());
			}
			$this->defaultWorkflow=$workflowName;
			Yii::trace('defaultWorkflow : '.$this->defaultWorkflow,self::SW_LOG_CATEGORY);
		}
		return $this->defaultWorkflow;
	}
	/**
	 * Insert the owner component into the workflow whose id is passed as argument.
	 * If NULL is passed as argument, the default workflow is used. If no error occurs, when this method ends, the owner
	 * component's status is the initial node of the selected workflow.
	 *
	 * @param string $workflowId workflow Id or NULL. If NULL the default workflow Id is used
	 * @throws SWException the owner model is already in a workflow
	 * @return boolean TRUE
	 */
	public function swInsertToWorkflow($workflowId=null){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		
		if($this->swHasStatus())
		{
			throw new SWException(
				Yii::t(self::SW_I8N_CATEGORY,'object already in a workflow : {status}',array('{status}'=>$this->swGetStatus())),
				SWException::SW_ERR_IN_WORKFLOW
			);
		}
		
		$wfName=( $workflowId == null
			? $this->swGetDefaultWorkflowId()
			: $workflowId
		);
		
		if( $wfName == null )
		{
			throw new SWException(
				Yii::t(self::SW_I8N_CATEGORY,'failed to get the workflow name'),
				SWException::SW_ERR_IN_WORKFLOW
			);
		}
		$initialNode=$this->swGetWorkflowSource()->getInitialNode($wfName);
		
		$this->onEnterWorkflow(
			new SWEvent($this->getOwner(),null,$initialNode)
		);
		$this->_updateStatus($initialNode);
		$this->_updateOwnerStatus($initialNode);
		return true;
	}
	/**
	 * Removes the owner component from its current workflow.
	 * An exception is thrown if the owner model is not in a final status (i.e a status
	 * with no outgoing transition).
	 *
	 * see  {@link SWActiveRecordBehavior::swIsFinalStatus()}
	 * @throws SWException
	 */
	public function swRemoveFromWorkflow(){
		
		if( $this->swIsFinalStatus() == false)
			throw new SWException('current status is not final : '.$this->swGetStatus()->toString(),
				SWException::SW_ERR_STATUS_UNREACHABLE);

		$this->onLeaveWorkflow(
			new SWEvent($this->getOwner(),$this->_status,null)
		);
		$this->_status = null;
		$this->_final  = null;
		$this->_updateOwnerStatus('');
	}
	/**
	 * This method returns a list of nodes that can be actually reached at the time the method is called. To be reachable,
	 * a transition must exist between the current status and the next status, AND if a constraint is defined, it must be
	 * evaluated to true.
	 *
	 * @return array SWNode object array for all nodes thats can be reached from the current node.
	 */
	public function swGetNextStatus(){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		$n=array();
		if($this->swHasStatus()){
			$allNxtSt=$this->swGetWorkflowSource()->getNextNodes($this->_status);
			if( $allNxtSt != null)
			{
				foreach ( $allNxtSt as $aStatus ) {
       				if($this->swIsNextStatus($aStatus) == true){
       					$n[]=$aStatus;
       				}
				}
			}
		}else{
			$n[]=$this->swGetWorkflowSource()->getInitialNode($this->swGetDefaultWorkflowId());
		}
		return $n;
	}
	/**
	 * Returns all statuses belonging to the workflow the owner component is inserted in. If the
	 * owner component is not inserted in a workflow, an empty array is returned.
	 *
	 * @return array list of SWNode objects.
	 */
	public function swGetAllStatus(){
		if(!$this->swHasStatus() or $this->swGetWorkflowId() == null)
			return array();
		else
			return $this->swGetWorkflowSource()->getAllNodes($this->swGetWorkflowId());
	}
	/**
	 * Checks if the status passed as argument can be reached from the current status. This occurs when
	 * <br/>
	 * <ul>
	 * 	<li>a transition has been defined in the workflow between those 2 status</li>
	 * <li>the destination status has a constraint that is evaluated to true in the context of the
	 * owner model</li>
	 * </ul>
	 * Note that if the owner component is not in a workflow, this method returns true if argument
	 * $nextStatus is the initial status for the workflow associated with the owner model. In other words
	 * the initial status for a given workflow is considered as the 'next' status, for all component associated
	 * to this workflow but not inserted in it. Of course, if a constraint is associated with the initial
	 * status, it must be evaluated to true.
	 *
	 * @param mixed nextStatus String or SWNode object for the next status
	 * @return boolean TRUE if the status passed as argument can be reached from the current status, FALSE
	 * otherwise.
	 */
	public function swIsNextStatus($nextStatus){
		Yii::trace(__CLASS__.'.'.__FUNCTION__.' $nextStatus = '.$nextStatus,self::SW_LOG_CATEGORY);
		
		$bIsNextStatus=false;
		
		// get (create) a SWNode object
		
		$nxtNode=$this->swGetWorkflowSource()->createSWNode(
        			$nextStatus,
        			$this->swGetDefaultWorkflowId()
        		);
		
		if( (! $this->swHasStatus() and $this->swIsInitialStatus($nextStatus)) or
		    (  $this->swHasStatus() and $this->swGetWorkflowSource()->isNextNode($this->_status,$nxtNode)) ){
			
			// Note : the transition NULL -> S is valid only if S is an initial status
			
		    // there is a transition between current and next status,
		    // now let's see if constraints to actually enter in the next status
		    // are evaluated to true.
		    
		    $swNodeNext=$this->swGetWorkflowSource()->getNodeDefinition($nxtNode);
		    if($this->_evaluateConstraint($swNodeNext->getConstraint()) == true)
		    {
		    	$bIsNextStatus=true;
		    }
		    else
		    {
		    	$bIsNextStatus=false;
		    	Yii::trace('constraint evaluation returned FALSE for : '.$swNodeNext->getConstraint(),
		    		self::SW_LOG_CATEGORY
		    	);
		    }
		}
		Yii::trace('SWItemBehavior->swIsNextStatus returns : '.($bIsNextStatus==true?'true':'false'),self::SW_LOG_CATEGORY);
		return $bIsNextStatus;
	}
	/**
	 * Creates a new node from the string passed as argument. If $str doesn't contain
	 * a workflow Id, this method uses the workflowId associated with the owner
	 * model. The node created here doesn't have to exist within a workflow.
	 * This method is mainly used by the SWValidator
	 *
	 * @param string $str string status name
	 * @return SWNode the node
	 */
	public function swCreateNode($str){
		return $this->swGetWorkflowSource()->createSWNode(
			$str,
			$this->swGetDefaultWorkflowId()
		);
	}
	/**
	 * Evaluate the expression passed as argument in the context of the owner
	 * model and returns the result of evaluation as a boolean value.
	 */
	private function _evaluateConstraint($constraint){
		return ( $constraint == null or
			$this->getOwner()->evaluateExpression($constraint) ==true?true:false);
	}
	/**
	 * If a expression is attached to the transition, then it is evaluated in the context
	 * of the owner model, otherwise, the processTransition event is raised. Note that the value
	 * returned by the expression evaluation is ignored.
	 */
	private function _runTransition($sourceSt,$destSt,$params=null){
		
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		if($sourceSt != null && $sourceSt instanceof SWNode ){
			$tr=$sourceSt->getTransitionTask($destSt);
			
			Yii::trace('transition process = '.$tr,self::SW_LOG_CATEGORY);

			if( $tr != null)
			{
				if( $this->transitionBeforeSave){
					
					if( is_string($tr))
					{
						$this->getOwner()->evaluateExpression($tr,array(
							'owner' 		=> $this->getOwner(),
							'sourceStatus'  => $sourceSt->toString(),
							'targetStatus'  => $destSt->toString(),
							'params'   		=> $params)
						);
					}
					else
					{
						$this->getOwner()->evaluateExpression($tr,array($this->getOwner(),$sourceSt->toString(), $destSt->toString(), $params));
					}
					
				}else {
					$this->_delayedTransition = $tr;
				}
			}
		}
	}
	/**
	 * Checks if the status passed as argument, or the current status (if NULL is passed) is a final status
	 * of the corresponding workflow.
	 * By definition a final status as no outgoing transition to other status.
	 *
	 * @param status status to test, or null (will test current status)
	 * @return boolean TRUE when the owner component is in a final status, FALSE otherwise
	 */
	public function swIsFinalStatus($status=null){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		if($this->_final == null)
		{
			$workflowId=($this->swHasStatus()?$this->swGetWorkflowId():$this->swGetDefaultWorkflowId());
			
			if( $status != null){
				$swNode=$this->swGetWorkflowSource()->createSWNode($status,$workflowId);
			}elseif($this->swHasStatus() == true) {
				$swNode=$this->_status;
			}else {
				return false;
			}
			$this->_final =  (count($this->swGetWorkflowSource()->getNextNodes($swNode,$workflowId))===0);
		}
		return $this->_final;

	}
	/**
	 * Checks if the status passed as argument, or the current status (if NULL is passed) is the initial status
	 * of the corresponding workflow. An exception is raised if the owner model is not in a workflow
	 * and if $status is null.
	 *
	 * @param mixed $status string or SWNode instance
	 * @return boolean TRUE if the owner component is in an initial status or if $status is an initial
	 * status.
	 * @throws SWException
	 */
	
	public function swIsInitialStatus($status=null){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);

		if( $status !=  null)
		{
			// create the node to compare with initial node
			
			$workflowId=( $this->swHasStatus()
				? $this->swGetWorkflowId()
				: $this->swGetDefaultWorkflowId()
			);
			$swNode=$this->swGetWorkflowSource()->createSWNode($status,$workflowId);
		}
		elseif($this->swHasStatus() == true)
		{
			// $status is null : the current status will be compared with initial node
			
			$swNode=$this->_status;
		}
		else {
			throw new SWException(Yii::t(self::SW_I8N_CATEGORY,'no status passed and no current status available'),
				SWException::SW_ERR_CREATE_FAILS);
		}
		
		$swInit=$this->swGetWorkflowSource()->getInitialNode($swNode->getWorkflowId());
		return $swInit->equals($swNode);
	}
	/**
	 * Validates the status attribute stored in the owner model. This attribute is valid if : <br/>
	 * <ul>
	 * 	<li>it is not empty</li>
	 * 	<li>it contains a valid status name</li>
	 * 	<li>this status can be reached from the current status</li>
	 * 	<li>or it is equal to the current status (no status change)</li>
	 * </ul>
	 * @param string $attribute status attribute name (by default 'status')
	 * @param mixed $value current value of the status attribute provided as a string or a SWNode object
	 * @return boolean TRUE if the status attribute contains a valid value, FALSE otherwise
	 */
	public function swValidate($attribute, $value){
        Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
        $bResult=false;
        try{
        	if($value instanceof SWNode){
        		$swNode=$value;
        	}else {
        		$swNode = $this->swGetWorkflowSource()->createSWNode(
        			$value,
        			$this->swGetDefaultWorkflowId()
        		);
        	}
			if($this->swIsNextStatus($value)==false and $swNode->equals($this->swGetStatus()) == false){
				$this->getOwner()->addError($attribute,Yii::t(self::SW_I8N_CATEGORY,'not a valid next status'));
			}else {
				$bResult=true;
			}
        }catch(SWException $e){
        	$this->getOwner()->addError($attribute,Yii::t(self::SW_I8N_CATEGORY,'value {node} is not a valid status',array(
        		'{node}'=>$value)
        	));
        }
        return $bResult;
	}
	/**
	 * This is an alias for methode {@link SWActiveRecordBehavior::swSetStatus()} and should not be used anymore
	 * @deprecated
	 */
	public function swNextStatus($nextStatus,$params=null){
		return $this->swSetStatus($nextStatus,$params);
	}
	/**
	 * Set the owner component into the status passed as argument.
	 * If a transition could be performed, the owner status attribute is updated with the new status value in the form <em>workflowId/nodeId</em>.
	 * This method is responsible for firing {@link SWEvents} and executing workflow tasks if defined for the given transition.
	 *
	 * @param mixed $nextStatus string or array. If array, it must contains a key equals to the name of the status
	 * attribute, and its value is the one of the destination node (e.g. $arr['status']). This is mainly useful when
	 * processing _POST array. If a string is provided, it must contain the fullname of the target node (e.g. <em>workfowId/nodeId</em>)
	 * @return boolean True if the transition could be performed, FALSE otherwise
	 */
	public function swSetStatus($nextStatus,$params=null){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		
		if( $nextStatus == null )
			throw new SWException('argument "nextStatus" is missing');
		
		$bResult   = false;
		$nextNode  = null;
		
		if(is_array($nextStatus) && isset($nextStatus[$this->statusAttribute]))
		{
			// $nextStatus may be provided as an array with a 'statusAttribute' key
			// example : $array['status']
			$nextStatus=$nextStatus[$this->statusAttribute];
		}
		elseif( $nextStatus instanceof SWNode)
		{
			$nextStatus = $nextStatus->toString();
		}

		try{
			$this->_lock();
			
			if( $this->swHasStatus() == false && $nextStatus != null)
			{
				// insertion into workflow //////////////////////////////////////////////////////////////
				//  $c->swNextStatus($status) was called. $c is not currently in a workflow and $status is
				// assumed to be an initial node

				$nextNode=$this->swGetWorkflowSource()->getNodeDefinition(
					$nextStatus,
					$this->swGetDefaultWorkflowId()
				);
									
				if( $this->swIsInitialStatus($nextNode) == false)
					throw new SWException('status is not initial : '.$nextNode->toString(),
						SWException::SW_ERR_STATUS_UNREACHABLE);
				
				$this->onEnterWorkflow(
					new SWEvent($this->getOwner(),null,$nextNode)
				);
				$this->_updateStatus($nextNode);
				$this->_updateOwnerStatus($nextNode);
				$bResult = true;
			}
			elseif( $this->swHasStatus() == true && $nextStatus != null)
			{
				// perform transition //////////////////////////////////////////////////////////////
				
				$nextNode=$this->swGetWorkflowSource()->getNodeDefinition(
					$nextStatus,
					$this->swGetWorkflowId()
				);
					
				if( $this->swIsNextStatus($nextNode) )
				{
					$event=new SWEvent($this->getOwner(),$this->_status,$nextNode);
						
					$this->onBeforeTransition($event);
					$this->onProcessTransition($event);
						
					$this->_runTransition($this->_status,$nextNode,$params);
					
					$this->_updateStatus($nextNode);
					$this->_updateOwnerStatus($nextNode);
					
					$this->onAfterTransition($event);
						
					if($this->swIsFinalStatus()){
						$this->onFinalStatus($event);
					}
					$bResult = true;
				}
				elseif( $nextNode->equals($this->swGetStatus()) == false)
				{
					throw new SWException('no transition between current and next status : '
						.$this->swGetStatus()->toString().' -> '. $nextNode->toString(),
						SWException::SW_ERR_STATUS_UNREACHABLE);
				}
				// else
				// 		there is not transition between both status but as they are identical, no operation
				//		should be performed.
			}
		} catch (CException $e) {
			$this->_unlock();
			Yii::log($e->getMessage(),CLogger::LEVEL_ERROR,self::SW_LOG_CATEGORY);
			throw $e;
		}
		$this->_unlock();
		return $bResult;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////
	// Events
	//
	
	/**
	 *
	 * @see base/CBehavior::events()
	 */
	public function events()
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
			
		// this behavior could be attached to a CComponent based class other
		// than CActiveRecord.
		
		if($this->getOwner() instanceof CActiveRecord){
			$ev=array(
				'onBeforeSave'=> 'beforeSave',
				'onAfterSave' => 'afterSave',
				'onAfterFind' => 'afterFind'
			);
		} else {
			$ev=array();
		}
		
		if($this->swIsEventEnabled())
		{
			Yii::trace('workflow event enabled',self::SW_LOG_CATEGORY);
			$this->getOwner()->attachEventHandler('onEnterWorkflow',array($this->getOwner(),'enterWorkflow'));
			$this->getOwner()->attachEventHandler('onBeforeTransition',array($this->getOwner(),'beforeTransition'));
			$this->getOwner()->attachEventHandler('onAfterTransition',array($this->getOwner(),'afterTransition'));
			$this->getOwner()->attachEventHandler('onProcessTransition',array($this->getOwner(),'processTransition'));
			$this->getOwner()->attachEventHandler('onFinalStatus',array($this->getOwner(),'finalStatus'));
			$this->getOwner()->attachEventHandler('onLeaveWorkflow',array($this->getOwner(),'leaveWorkflow'));
			$ev=array_merge($ev, array(
				// Custom events
				'onEnterWorkflow'	 => 'enterWorkflow',
				'onBeforeTransition' => 'beforeTransition',
				'onProcessTransition'=> 'processTransition',
				'onAfterTransition'  => 'afterTransition',
				'onFinalStatus'		 => 'finalStatus',
				'onLeaveWorkflow'	 => 'leaveWorkflow',
			));
		}
		return $ev;
	}
	/**
	 * Depending on the value of the owner status attribute, and the current status, this method performs an
	 * actual transition.
	 * @param Event $event
	 * @return boolean
	 */
	public function beforeSave($event)
	{
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
	
		$this->_beforeSaveInProgress = true;

			$ownerStatus = $this->getOwner()->{$this->statusAttribute};
			if( $ownerStatus == null &&  $this->swHasStatus() == false )
			{
				if($this->autoInsert == true)
					$this->swNextStatus();	// insert into workflow
			}
			else
			{
				$this->swNextStatus($ownerStatus);
			}

		$this->_beforeSaveInProgress = false;
		return true;
	}
	/**
	 * When option transitionBeforeSave is false, if a task is associated with
	 * the transition that was performed, it is executed now, that it after the activeRecord
	 * owner component has been saved. The onAfterTransition is also raised.
	 *
	 * @param SWEvent $event
	 */
	public function afterSave($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		if( $this->_delayedTransition != null )
		{
			Yii::trace('running delayed transition process');
			$tr=$this->_delayedTransition;
			$this->_delayedTransition=null;
			$this->getOwner()->evaluateExpression($tr);
		}
		
		foreach ($this->_delayedEvent as $delayedEvent) {
			$this->_raiseEvent($delayedEvent['name'],$delayedEvent['objEvent']);
		}
		$this->_delayedEvent=array();
	}
	/**
	 * Responds to {@link CActiveRecord::onAfterFind} event.
	 * This method is called when a CActiveRecord instance is created from DB access (model
	 * read from DB). At this time, the worklow behavior must be initialized.
	 *
	 * @param CEvent event parameter
	 */
	public function afterFind($event){

		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);

		if( !$this->getEnabled())
			return;
			
		try{
			// call _init here because 'afterConstruct' is not called when an AR is created
			// as the result of a query, and we need to initialize the behavior.
		
			$status=$this->getOwner()->{$this->statusAttribute};

			if( $status != null )
			{
				// the owner model already has a status value (it has been read from db)
				// and so, set the underlying status value without performing any transition
				
				$st=$this->swGetWorkflowSource()->getNodeDefinition($status,$this->swGetWorkflowId());
				$this->_updateStatus($st);
			}
			
		}catch(SWException $e){
			Yii::log(Yii::t(self::SW_I8N_CATEGORY,'failed to set status : {status}',
				array('{status}'=>$status)),
				CLogger::LEVEL_WARNING,
				self::SW_LOG_CATEGORY
			);
			Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
		}
	}
	/**
	 * Log event fired
	 *
	 * @param string $ev event name
	 * @param SWNode $source
	 * @param SWNode $dest
	 */
	private function _logEventFire($ev,$source,$dest){
		Yii::log(Yii::t('simpleWorkflow','event fired : \'{event}\' status [{source}] -> [{destination}]',
			array(
				'{event}'		=> $ev,
				'{source}'		=> ( $source == null ?'null':$source),
				'{destination}'	=> $dest,
			)),
			CLogger::LEVEL_INFO,
			self::SW_LOG_CATEGORY
		);
	}
	private function _raiseEvent($evName,$event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		if( $this->swIsEventEnabled() ){
			$this->_logEventFire($evName, $event->source, $event->destination);
			$this->getOwner()->raiseEvent($evName, $event);
		}
	}
	/**
	 * Default implementation for the onEnterWorkflow event.<br/>
	 * This method is dedicated to be overloaded by custom event handler.
	 * @param SWEvent the event parameter
	 */
	public function enterWorkflow($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
	}
	/**
	 * This event is raised after the record instance is inserted into a workflow. This may occur
	 * at construction time (new) if the behavior is initialized with autoInsert set to TRUE and in this
	 * case, the 'onEnterWorkflow' event is always fired. Consequently, when a model instance is created
	 * from database (find), the onEnterWorkflow is fired even if the record has already be inserted
	 * in a workflow (e.g contains a valid status).
	 *
	 * @param SWEvent the event parameter
	 */
	public function onEnterWorkflow($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		$this->_raiseEvent('onEnterWorkflow',$event);
	}
	/**
	 * Default implementation for the onEnterWorkflow event.<br/>
	 * This method is dedicated to be overloaded by custom event handler.
	 * @param SWEvent the event parameter
	 */
	public function leaveWorkflow($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
	}
	/**
	 * This event is raised after the record instance is removed from a workflow.
	 * This occures when the owner status attribut is set to NULL, for instance by calling
	 * $c->swNextStatus()
	 *
	 * @param SWEvent the event parameter
	 */
	public function onLeaveWorkflow($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		$this->_raiseEvent('onLeaveWorkflow',$event);
	}
	/**
	 * Default implementation for the onBeforeTransition event.<br/>
	 * This method is dedicated to be overloaded by custom event handler.
	 * @param SWEvent the event parameter
	 */
	public function beforeTransition($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
	}
	/**
	 * This event is raised before a workflow transition is applied to the owner instance.
	 *
	 * @param SWEvent the event parameter
	 */
	public function onBeforeTransition($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		$this->_raiseEvent('onBeforeTransition',$event);
	}
	/**
	 * Default implementation for the onProcessTransition event.<br/>
	 * This method is dedicated to be overloaded by custom event handler.
	 * @param SWEvent the event parameter
	 */
	public function processTransition($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
	}
	/**
	 * This event is raised when a workflow transition is in progress. In such case, the user may
	 * define a handler for this event in order to run specific process.<br/>
	 * Depending on the <b>'transitionBeforeSave'</b> initialization parameters, this event could be
	 * fired before or after the owner model is actually saved to the database. Of course this only
	 * applies when status change is initiated when saving the record. A call to swNextStatus()
	 * is not affected by the 'transitionBeforeSave' option.
	 *
	 * @param SWEvent the event parameter
	 */
	public function onProcessTransition($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		if( $this->transitionBeforeSave || $this->_beforeSaveInProgress == false){
			$this->_raiseEvent('onProcessTransition',$event);
		}else {
			$this->_delayedEvent[]=array('name'=> 'onProcessTransition','objEvent'=>$event);
		}
	}
	/**
	 * Default implementation for the onAfterTransition event.<br/>
	 * This method is dedicated to be overloaded by custom event handler.
	 *
	 * @param SWEvent the event parameter
	 */
	public function afterTransition($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
	}
	/**
	 * This event is raised after the onProcessTransition is fired. It is the last event fired
	 * during a non-final transition.<br/>
	 * Again, in the case of an AR being saved, this event may be fired before or after the record
	 * is actually save, depending on the <b>'transitionBeforeSave'</b> initialization parameters.
	 *
	 * @param SWEvent the event parameter
	 */
	public function onAfterTransition($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		if( $this->transitionBeforeSave || $this->_beforeSaveInProgress == false){
			$this->_raiseEvent('onAfterTransition',$event);
		}else {
			$this->_delayedEvent[]=array('name'=> 'onAfterTransition','objEvent'=>$event);
		}
	}
	/**
	 * Default implementation for the onFinalStatus event.<br/>
	 * This method is dedicated to be overloaded by custom event handler.
	 * @param SWEvent the event parameter
	 */
	public function finalStatus($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
	}
	/**
	 * This event is raised at the end of a transition, when the destination status is a
	 * final status (i.e the owner model has reached a status from where it will not be able
	 * to move).
	 *
	 * @param SWEvent the event parameter
	 */
	public function onFinalStatus($event){
		Yii::trace(__CLASS__.'.'.__FUNCTION__,self::SW_LOG_CATEGORY);
		if( $this->transitionBeforeSave || $this->_beforeSaveInProgress == false){
			$this->_raiseEvent('onFinalStatus',$event);
		}else {
			$this->_delayedEvent[]=array('name'=> 'onFinalStatus','objEvent'=>$event);
		}
	}
}
?>