<?php
class EventError04ARTest extends CDbTestCase {
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
	public function testEvent1() {
		$m=Model0B::model()->findByPk('2');
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S2');
		try {
			$m->swNextStatus('S4');
		} catch (SWException $e) {
			$this->assertEquals($e->getCode(),SWException::SW_ERR_REETRANCE);
		}
	}}
?>