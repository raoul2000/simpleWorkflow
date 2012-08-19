<?php
class NextStatusTest extends CDbTestCase {
    public $fixtures=array(
        'items'=>'Model01',
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
	public function testInitialisation1() {

		$m=Model01::model()->findByPk('1');
		
		$m->swNextStatus('S2');
		
		$this->assertEquals($m->swGetWorkflowId(),'workflow1');
		$this->assertEquals($m->swGetDefaultWorkflowId(),'workflow1');
		$this->assertTrue($m->swHasStatus());
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S2');
		$this->assertTrue($m->swGetStatus()->equals('S2'));
		$this->assertFalse($m->swGetStatus()->equals('s2'));
		$continue=true;
		try {
			$m->swNextStatus('S3');
			$continue=false;
		} catch (Exception $e) {
			$this->assertEquals($e->getCode(),SWException::SW_ERR_STATUS_UNREACHABLE);
		}
		$this->assertTrue($continue);
		
		$m->swNextStatus('S4');
		$this->assertTrue($m->swGetStatus()->equals('S4'));
		
		$m->swNextStatus('S5');
		$this->assertTrue($m->swGetStatus()->equals('S5'));
		$this->assertTrue($m->swIsFinalStatus());
	}
}
?>