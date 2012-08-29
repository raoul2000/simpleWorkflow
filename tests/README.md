This is the PHPUnit Test suite for the simpleWorkflow Yii extension.

To install, just replace the existing protected/tests folder with this one. Before being
able to run the tests be sure to :

* Configure a DB connection
* Create the 'item' table by running the tests/models/create.sql SQL script
* Check that relative path defined in bootstrap.php match your environment settings

Below is a test configuration sample file located in `protected/config/test.php`

```php
return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'import'=>array(
			'application.extensions.simpleWorkflow.*',
			'application.tests.models.*',
		),
		'components'=>array(
			'fixture'  => array(
				'class'=>'system.test.CDbFixtureManager',
			),
			'swSource'=> array(
				'class'   => 'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
				'basePath'=> 'application.tests.workflows',
			),
			'swSourceClass'=> array(
				'class'			 => 'application.extensions.simpleWorkflow.SWPhpWorkflowSource',
				'definitionType' => 'class'
			),
			
			// update to fit your test db name settings
			
			'db'=>array(
				'connectionString'  =>'mysql:host=localhost;dbname=db1',  
				'emulatePrepare'    => true,
				'username' 		    => 'root',
				'password' 		    => '********',
				'charset' 			=> 'utf8',
				'enableParamLogging'=> true,
				'enableProfiling'	=> true,
			),
		),
	)
);
```
