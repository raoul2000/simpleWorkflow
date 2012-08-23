![simpleWorkflow](http://s172418307.onlinehome.fr/project/yiiDemo/images/sw-logo-big.png)


The simpleWorkflow extension is a set of Yii components that is dedicated to provide an easy way to manage the life cycle of CActiveRecord objects inside a workflow. It provides features to control the behavior of the active record in its associated workflow : transition tasks, status constraints, event model.


###Resources
* [Demo and doc](http://s172418307.onlinehome.fr/project/yiiDemo/index.php?r=simpleworkflowdemo/index)
* [Join discussion, report a bug](http://www.yiiframework.com/forum/index.php?/topic/12071-extension-simpleworkflow/)




##Documentation

###Requirements
* Yii 1.1.4 or above

###Installation
* Extract the released files under `protected/extensions`.
* add the SWPhpWorkflowSource component to your configuration

```php
'components'=>array(	
	// adding the simple Workflow source component
	'swSource'=> array(
		'class'=>'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
	), ...

```
* add simpleWorkflow extension base folder to your imports

```php
'import'=>array(
	...
	'application.extensions.simpleWorkflow.*',	// Import simpleWorkflow extension
), 
```

###Usage
Once installed and correctly configured, the simpleWorkflow extension will handle the workflow for any model. 
This happens automatically when the model is read/saved from/to database, or programatically by the developer. For instance :

```php
[php]
$m=MyModel::model()->findByPk('1');
if( $m->swHasStatus() ){
     echo 'status : '.$m->swGetStatus()->toString();		
}else {
     $m->swInsertToWorkflow();
     $m->save();
}
```

Enable simpleWorkflow for a given model, you must attach the `SWActiveRecordBehavior` behavior to this model.
For more information on how to use the simpleWorkflow extension, please refer to the [documentation](http://s172418307.onlinehome.fr/project/yiiDemo/index.php?r=simpleworkflowdemo/index#installation)

