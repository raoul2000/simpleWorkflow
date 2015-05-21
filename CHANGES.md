##version 1.0.9
* add composer.json ( [cornernote](https://github.com/cornernote)) and publish to https://packagist.org/

##version 1.0.0.8
* gii command : add SWyEdConverterDOM that uses DOM extension. If domxml extension is available, use it in priority otherwise
use DOM
* update phpuni.xml to generate coverage report
* add swSetStatus() to replace swNextStatus() which is still supported, but deprecated


##version 1.0.0.7
* gii command : add class constant WORKFLOW_ID

##version 1.0.0.6
* change allowed status name pattern : now character '_' is allowed so a node or workflow id such as 'workflow_A/status_name' is permited.
* change the status separator character for SW scenario names. The '_' is replaced by '-'. **UPDATED requires** SW scenario
names such as 'xxxxx_xxxx' must be replaced by 'xxxx-xxxx'.

##version 1.0.0.5
* add swGetWorkflowSource() as public method to return the workflow source component used by the behavior

##version 1.0.0.4
* change swNextStatus() : it is not permitted anymore to call 'swNextStatus()' with no argument. To insert a model into a 
workflow, swNextStatus() must be replaced by a call to 'swInsertToWorkflow()'.
* enh : add method 'swRemoveFromWorkflow()'. This method is only successful when the owner model is in a final status (i.e a status
with no outgoing transition).
* enh : it is now possible to pass parameters to the task transition. To do so, use the optional second argument when calling
'swNextStatus()'. 
Example : 
```php

	// if a transition is defined between the current model status
	// and status 'S2', it will be called with the params array as argument
	 
	$model->swNextStatus('S2',array('var1'=>'value1'));			
```

##version 1.0.0.3
* enh : gii command : SWyEdConverter now tests that the domxml extension is available (loaded) before process
* update command/README.me

##version 1.0.0.2
* add : Gii command to convert [yEd Graph Editor](http://www.yworks.com/en/products_yed_download.html) workflows into sW workflows (experimental)
* add : SWyEdconverter : implemented actual graphml to sW PHP file conversion - **requires domxml PHP extension**

##version 1.0.0.1
* enh : when loading a workflow, raise exception on duplicate node id
	
##version 1.0.0.0
* fix : autoinsert into workflow [(jmariani)](http://www.yiiframework.com/forum/index.php/topic/12071-extension-simpleworkflow/page__view__findpost__p__164472)
* enh : replace function is_a() by instanceof	[(kjharni)](http://www.yiiframework.com/forum/index.php/topic/12071-extension-simpleworkflow/page__view__findpost__p__128227)
* enh : metadata. It is now possible to add any value to node definition by using the metadata attribute.
* enh : workflow class definition. A workflow can be defined as a class that must implement method getDefinition(). This method returns the workflow
	definition in its array format.
* enh : add 'leaveWorkflow' event. This event is fired whenever a component included in a workflow reset its status. This
	must be done from a final status only.

##version 0.0.0.6 : RC2
* fix : replace 'split' with 'explode' [(got 2 doodle)](http://www.yiiframework.com/forum/index.php/topic/12071-extension-simpleworkflow/page__view__findpost__p__60273)
* enh : SWActiveRecordBehavior->swValidate now returns boolean 
* enh : Workflow Driven Model Validation. It is now possible to define validators which are only
	executed upon specific transitions (this is done by defining specific scenario names).
	 
##version 0.0.0.5 : RC1
* demo
	
##version 0.0.0.4
* add more demo and unit tests
* complete documentation
* add SWActiveRecordBehavior._beforeSaveInProgress (private) in order to handle differences
	between change status during AR save and call to swNextStatus. In this last case, no delayed
	event or transition should apply.
	
##version 0.0.0.3
* add demo6
* create SWHelper : nextStatuslistData and allStatuslistData
* add : SWActiveRecordBehavior.swGetAllStatus()
* update documentation
	
##version 0.0.0.2
* add various example
* merge _discoverWorkflow() and swGetDefaultWorkflowId() methods
* add event support : onEnterWorkflow, onBeforeTransition, onProcessTransition,
	onAfterTransition, onFinalStatus.
* add the 'transitionBeforeSave' option : when true, a transition process is executed
	after the AR has been saved. Events onProcessTransition, onAfterTransition, onFinalStatus
	are also fired after AR is saved. (delayed transition process and event fired).
	  
##version 0.0.0.1 
* initial version