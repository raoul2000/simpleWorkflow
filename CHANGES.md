==== 1.0.0.3
	- enh : gii command : SWyEdConverter now tests that the domxml extension is available (loaded) before process
	- update command/README.me
	
==== 1.0.0.2
	- fix : small bug fixes
	- enh : refactor SWNode and SWPhpWorkflowSource
	- enh : add more unit tests
	
==== 1.0.0.1
	- enh : when loading a workflow, raise exception on duplicate node id
	
==== 1.0.0.0
	- fix : autoinsert into workflow.
	- enh : replace function is_a() by instanceof	(kjharni on http://www.yiiframework.com/forum/index.php/topic/12071-extension-simpleworkflow/page__view__findpost__p__128227)
	- enh : metadata. It is now possible to add any value to node definition by using the metadata attribute.
	- enh : workflow class definition. A workflow can be defined as a class that must implement method getDefinition(). This method returns the workflow
	definition in its array format.
	- enh : add 'leaveWorkflow' event. This event is fired whenever a component included in a workflow reset its status. This
	must be done from a final status only.

==== 0.0.0.6 : RC2
	- fix : replace 'split' with 'explode' (got 2 doodle)
	- enh : SWActiveRecordBehavior->swValidate now returns boolean 
	- enh : Workflow Driven Model Validation. It is now possible to define validators which are only
	executed upon specific transitions (this is done by defining specific scenario names).
	 
==== 0.0.0.5 : RC1
	- demo
	
==== 0.0.0.4
	- add more demo and unit tests
	- complete documentation
	- add SWActiveRecordBehavior._beforeSaveInProgress (private) in order to handle differences
	between change status during AR save and call to swNextStatus. In this last case, no delayed
	event or transition should apply.
	
==== 0.0.0.3
	- add demo6
	- create SWHelper : nextStatuslistData and allStatuslistData
	- add : SWActiveRecordBehavior.swGetAllStatus()
	- update documentation
	
==== 0.0.0.2
	- add various example
	- merge _discoverWorkflow() and swGetDefaultWorkflowId() methods
	- add event support : onEnterWorkflow, onBeforeTransition, onProcessTransition,
	onAfterTransition, onFinalStatus.
	- add the 'transitionBeforeSave' option : when true, a transition process is executed
	after the AR has been saved. Events onProcessTransition, onAfterTransition, onFinalStatus
	are also fired after AR is saved. (delayed transition process and event fired).
	  
==== 0.0.0.1 
	- initial version