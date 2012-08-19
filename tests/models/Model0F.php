<?php
/**
 * defaultWorkflow=workflow1 autoInsert=true (default)
 *
 */
class Model0F extends CActiveRecord{
	public $taskCallCount = 0;
	/**
	 *
	 * id
	 * status
	 * history
	 */
	/**
	 * Returns the static model of the specified AR class.
	 * @return wfitem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'item';
	}
	public function behaviors()
	{
		return array(
			'swBehavior' => array(
				'class'           => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'defaultWorkflow' => 'SWWorkflow1',
				'workflowSourceComponent'=> 'swSourceClass',
				'autoInsert'=> false
			)
		);
	}
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'status'=>'Status',
			'userStatus'=>'Free Status',
		);
	}
	public function task1(){
		$this->taskCallCount++;
	}
}

?>