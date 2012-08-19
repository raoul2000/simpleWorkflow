<?php
class Component05 extends CComponent {
	public $status;
	public function workflow(){
		return array(
			'initial' => 'A',
			'node' => array(
		 		array(
		 			'id' => 'A',
		 			'label' => 'status A',
		 			'transition'=> array(
						'B'=>'',
						'C'=>'')
		 		),
		 		array(
		 			'id' => 'B',
		 			'label' => 'status B',
		 			'transition' => 'C,D'
		 		),
		 		array(
		 			'id' => 'C',
		 			'label' => 'status C',
		 			'transition' => array('D','B'),
		 		),
		 		array(
		 			'id' => 'D',
		 			'label' => 'status D'
		 		),
		 	)
		 );
	}
}
class GetNextStatusTest extends CDbTestCase {
	/**
	 *
	 * @see test/CDbTestCase::setUp()
	 */
	protected function setUp()
	{
		parent::setUp();
	}
	/**
	 * NextStatus when autoInsert is FASLE : returned status is initial Status
	 */
	public function testInitialisation1() {
		$a=new Component05();
		$a->attachBehaviors(array(
			'swBehavior' => array(
				'class'     => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'autoInsert'=> false
			)
		));
		$this->assertFalse($a->autoInsert);

		$n=$a->swGetNextStatus();
		$this->assertTrue(count($n)==1);
		$this->assertEquals($n[0]->toString(),'swComponent05/A');
		$this->assertTrue($a->swIsInitialStatus($n[0]));
	}
	/**
	 * NextStatus when autoInsert is TRUE
	 */
	public function testInitialisation2() {
		$a=new Component05();
		$a->attachBehaviors(array(
			'swBehavior' => array(
				'class'     => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
				'autoInsert'=> true,
			)
		));
		$this->assertTrue($a->autoInsert);

		$n=$a->swGetNextStatus('C');
		
		$this->assertTrue(count($n)==2);
		$this->assertEquals($n[0]->toString(),'swComponent05/B');
		$this->assertEquals($n[1]->toString(),'swComponent05/C');
		$this->assertFalse($a->swIsInitialStatus($n[0]));
		$this->assertFalse($a->swIsInitialStatus($n[1]));
	}
}
?>