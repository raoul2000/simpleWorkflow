<?php
/**
 * defaultWorkflow='' (swModel04 will be used) autoInsert=false
 *
 * @author Raoul
 * @copyright Copyright 2010
 * @created 29 août 2010 - 14:22:58
 */			
class Model04 extends CActiveRecord{
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
				'class'      => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'autoInsert' => false
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
}

?>