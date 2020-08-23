![simpleWorkflow](http://s172418307.onlinehome.fr/project/yiiDemo/images/sw-logo-big.png)


The **simpleWorkflow** extension is a set of Yii components that is dedicated to provide an easy way to manage the life cycle of CActiveRecord objects inside a workflow. 
It provides features to control the behavior of the active record in its associated workflow : transition tasks, status constraints, event model.

> Note that the *simpleWorklfow* extension, following Yii 1.1 life cycle, has also reached end of life. Please consider using [yii2-workflow](https://github.com/raoul2000/yii2-workflow) extension with Yii 2.x

### Resources
- [Usage Guide](./doc/DOCUMENTATION.md)
- [API documentation](http://s172418307.onlinehome.fr/project/sandbox/www/resources/simpleWorkflow/api/index.html)
- [Join discussion](http://www.yiiframework.com/forum/index.php?/topic/12071-extension-simpleworkflow/)

## Documentation

### Requirements

* Yii 1.1.4 or above

### Installation

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

### Usage

Once installed and correctly configured, the simpleWorkflow extension will handle the workflow for any model. 
To enable simpleWorkflow for a given model, you must attach the `SWActiveRecordBehavior` behavior to this model.
 
```php
class MyModel extends CActiveRecord {
	public function behaviors()
	{
		return array(
			'swBehavior' => array(
				'class' => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior'
			)
		);
	}
}
```

The model can then be inserted into a workflow, and evolve among statuses inside this workflow.
For instance, the code below insert an existing record into a workflow, or if already done, displays its current status :

```php
$m=MyModel::model()->findByPk('1');
if( $m->swHasStatus() ){
     echo 'status : '.$m->swGetStatus()->toString();		
}else {
     $m->swInsertToWorkflow();
     $m->save();
}
```

For more information on how to use the simpleWorkflow extension, please refer to the [full documentation](http://s172418307.onlinehome.fr/project/yiiDemo/index.php?r=simpleworkflowdemo/index)

### Gii Command

Creating a workflow 'by hand' can become an error-prone task when several nodes and edges are required. One good option is to create the workflow using a **visual tools**, and after some searches
it seems that one of the best application to do it is [yEd Graph Editor](http://www.yworks.com/en/products_yed_about.html). Of course, it is free to use !


Once created and saved to graphml format, the workflow can be converted into a simpleworkflow, ready to use. To learn more about the simpleWorkflow Gii command, please check the
`command` folder.
