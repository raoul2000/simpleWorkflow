<?php
/**
 * @author Raoul
 * @copyright Copyright 2010
 * @created 31 ao�t 2010 - 19:49:19
 */

class Metadata extends CDbTestCase {
	public $fixtures=array(
		'items'=>'Model0G',
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
	public function test1() {
		
		$m=new Model0F();
		$m->status = SWWorkflow1::S1;
		$this->assertTrue($m->swNextStatus());
		$this->assertTrue($m->swGetStatus()->color != null);
		try{
			$this->assertTrue( isset($m->swGetStatus()->not_found) == false);
			$this->fails();
		}catch(Exception $e){
			
		}
	}
	public function test2(){
		$m = Model0G::model()->findByPk(5);
		$this->assertTrue($m != null);
		
		$this->assertTrue($m->swGetStatus()->attr1 == 'value1');
		$this->assertTrue(is_array($m->swGetStatus()->arr1));
		$this->assertTrue($m->swGetStatus()->arr1[0] == 1);
		$this->assertTrue($m->swGetStatus()->arr1[1] == 2);
		$this->assertTrue($m->swGetStatus()->arr1[2] == 3);
		
		$this->assertTrue( isset($m->swGetStatus()->attr1_not_found) == false );
		
		$m->status = 'S2';
		$this->assertTrue($m->save());

		$this->assertTrue($m->swStatusEquals('S2'));
		$this->assertTrue( isset($m->swGetStatus()->attr1) == false );
		$this->assertTrue( isset($m->swGetStatus()->arr1) == false );
		
		
		$m->swNextStatus('S4');

		$this->assertTrue( isset($m->swGetStatus()->attr1) == false );
		$this->assertTrue( isset($m->swGetStatus()->arr1) == false );
		
		$this->assertTrue($m->swGetStatus()->attr4 == 'value4');
		$this->assertTrue(is_array($m->swGetStatus()->arr4));

		$this->assertTrue($m->swGetStatus()->arr4[0] == 4);
		$this->assertTrue($m->swGetStatus()->arr4[1] == 5);
		$this->assertTrue($m->swGetStatus()->arr4[2] == 'string6');
		
		
		
	}
}
?>