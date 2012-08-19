<?php
class Validator01Test extends CDbTestCase {
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
	public function testNoSwValidationEnabled() {
		
		$m=Model0D::model()->findByPk('2');
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S2');
		$this->assertEquals($m->validate(),true);
		
		// no transition S2->S3 : validation fails
		
		$m->status = 'S3';
		$this->assertEquals($m->validate(),false);
		
		// transition S2->S4 exist
		
		$m->status = 'S4';
		$this->assertEquals($m->validate(),true);
		
		$m->save();
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S4');
		
		// S4->S3 exist but validator states that $username is required
		// so validation fails. However, $enableSwValidation is false (default) so validation
		// eventually succeeds.
		
		$m->status = 'S3';
		$this->assertEquals($m->validate(),true);
		$m->save();
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S3');
			
	}
	public function testSwValidationEnabled() {
		
		
		$m=Model0D::model()->findByPk('2');
		$m->ruleSet=2;
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S2');
		$this->assertEquals($m->validate(),true);
		
		// no transition S2->S3 : validation fails
		$m->status = 'S3';
		$this->assertEquals($m->validate(),false);
		
		// transition S2->S4 exist
		
		$m->status = 'S4';
		$this->assertEquals($m->validate(),true);
		
		$m->save();
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S4');
		
		// S4->S3 exist but validator states that $username is required on this transition
		// so validation fails. $enableSwValidation is TRUE, validation fails
		
		$m->status = 'S3';
		$this->assertEquals($m->validate(),false);
		$m->save();
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S4');
	}
	/**
	 *
	 */
	public function testSwValidationEnabled2() {
		
		$m=Model0D::model()->findByPk('1');
		$m->ruleSet=2;
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S1');
		
		// username required on sw:S1_S1
		
		$m->status = 'S1';
		$this->assertEquals($m->validate(),false);
		
		// no sw scenario for S1_S2 (wrong syntax. see model)
			
		$m->status = 'S2';
		$this->assertEquals($m->validate(),true);
		
		// username required on sw:S1_S3
		
		$m->status = 'S3';
		$this->assertEquals($m->validate(),false);
	}
	
	public function testSwValidationRuleSet3() {
		
		$m=Model0D::model()->findByPk('1');
		$m->ruleSet=3;
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S1');
		
		// username required on sw:S1_S1
		
		$m->status = 'S1';
		$this->assertEquals($m->validate(),false);
		
		// no sw scenario for S1_S2 (wrong syntax, now prefix see model)
			
		$m->status = 'S2';
		$this->assertEquals($m->validate(),true);
		
		// username required on sw:S1_S3
		
		$m->status = 'S3';
		$this->assertEquals($m->validate(),false);
		
		
		$m->username='name';
		$this->assertEquals($m->validate(),true);
		
		// no sw scenario for S1_S2 (wrong syntax, now prefix see model)
		
		$m->status = 'S2';
		$this->assertEquals($m->validate(),true);
		
		// username required on sw:S1_S3
		
		$m->status = 'S3';
		$this->assertEquals($m->validate(),true);
	}
	public function testSwValidationRuleSet4() {
		
		$m=Model0D::model()->findByPk('1');
		$m->ruleSet=4;
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S1');
		
		// username required on sw:S1_S1
		
		$m->status = 'S1';
		$this->assertEquals($m->validate(),false);
		
		// no sw scenario for S1_S2 (wrong syntax, now prefix see model)
			
		$m->status = 'S2';
		$this->assertEquals($m->validate(),false);
		
		// username required on sw:S1_S3
		
		$m->status = 'S3';
		$this->assertEquals($m->validate(),false);
		
		$m->username='name';
		$this->assertEquals($m->validate(),false);
		
		// no sw scenario for S1_S2 (wrong syntax, now prefix see model)
			
		$m->status = 'S2';
		$this->assertEquals($m->validate(),false);
		
		// username required on sw:S1_S3
		
		$m->status = 'S3';
		$this->assertEquals($m->validate(),false);
		
		$m->password='pwd';
		$this->assertEquals($m->validate(),true);
		
		// no sw scenario for S1_S2 (wrong syntax, now prefix see model)
			
		$m->status = 'S2';
		$this->assertEquals($m->validate(),true);
		
		// username required on sw:S1_S3
		
		$m->status = 'S3';
		$this->assertEquals($m->validate(),true);
		
	}
}
?>