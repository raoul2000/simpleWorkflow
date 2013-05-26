<?php
class Model0I extends CActiveRecord {
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'item';
	}
	public function behaviors()
	{
		return array(
		'swBehavior' => array(
		'class'      => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
		'autoInsert' => true,
		)
		);
	}
	public function workflow(){
		return array(
			'initial' => 's1',
			'node' => array(
				array(
					'id' => 's1',
					'label' => 'label1',
					'transition'=> 's2'
				),
				array(
					'id' => 's2',
					'label' => 'label2'
				)
			)
		);
	}
}