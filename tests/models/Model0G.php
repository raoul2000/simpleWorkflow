<?php
class Model0G extends CActiveRecord{
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
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('status','SWValidator'),	// mandatory
		);
	}
	public function behaviors()
	{
		return array(
			'swBehavior' => array(
				'class'           => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'defaultWorkflow' => 'MetadataWorkflow'
			)
		);
	}
}

?>