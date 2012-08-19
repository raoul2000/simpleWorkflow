<?php
class Validator02Test extends CDbTestCase {
    public $fixtures=array(
        'items'=>'Model06',
    );
	/**
	 *
	 * @see test/CDbTestCase::setUp()
	 */
	protected function setUp()
	{
		parent::setUp();
	}
	/**
	 *
	 */
	public function testSwValidation1() {
		
		$m=Model0E::model()->findByPk('4');
	
		$this->assertEquals($m->swGetStatus()->toString(),'workflowPartial1/S1');
		$this->assertEquals($m->validate(),true);
		
		// no transition S2->S3 : validation fails
		
		$m->status = 'S3';
		$this->assertEquals($m->validate(),true);
		
		// transition S2->S4 exist
		
		$m->status = 'S2';
		$this->assertEquals($m->validate(),true);
		
		$m->save();
		$this->assertEquals($m->swGetStatus()->toString(),'workflowPartial1/S2');
		
		$m->status = 'workflowPartial2/P1';
		$this->assertEquals($m->validate(),false);
		$m->password = 'pwd';
		$this->assertEquals($m->validate(),true);
		$m->save();
		$this->assertEquals($m->swGetStatus()->toString(),'workflowPartial2/P1');
		
	}
	public function testSwValidation2() {
		
		$m=new Model0E();
	
		$this->assertEquals($m->swHasStatus(),false);
		$m->ruleSet=2;
		$m->status = 'S1';
		$this->assertEquals($m->validate(),false);
		$m->username = 'name';
		$this->assertEquals($m->validate(),true);
		
	}
}
?>