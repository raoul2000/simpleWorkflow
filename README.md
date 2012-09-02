![simpleWorkflow](http://s172418307.onlinehome.fr/project/yiiDemo/images/sw-logo-big.png)


The **simpleWorkflow** extension is a set of Yii components that is dedicated to provide an easy way to manage the life cycle of CActiveRecord objects inside a workflow. 
It provides features to control the behavior of the active record in its associated workflow : transition tasks, status constraints, event model.


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

###Gii Command
Creating a workflow 'by hand' can become an error-prone task when several nodes and edges are required. One good option is to create the workflow using a **visual tools**, and after some searches
it seems that one of the best application to do it is [yEd Graph Editor](http://www.yworks.com/en/products_yed_about.html). Of course, it is free to use !


Once created and saved to graphml format, the workflow can be converted into a simpleworkflow, ready to use. To learn more about the simpleWorkflow Gii command, please check the
`command` folder.

 
Copyright (c) Raoul All rights reserved.
Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer. Redistributions in binary form must 
reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution. 
Neither the name of Raoul nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY 
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT 
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE 
