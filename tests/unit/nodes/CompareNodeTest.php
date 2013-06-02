<?php
class CompareNodeTest extends CDbTestCase {

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
	public function testEquals_01() {

		$n=new SWNode('node1','w1');
		$this->assertTrue($n->equals('w1/node1'));
		
		$this->assertFalse($n->equals('w2/node1'));
		$this->assertFalse($n->equals('w1/node2'));
		
		$k=new SWNode('node1','w2');
		$this->assertTrue($k->equals('w2/node1'));
		
		$this->assertFalse( $n->equals($k) );

	}
	public function testEquals_02() {
	
		// compare (success)
		$n=new SWNode('node1','w1');
		$this->assertTrue($n->equals('w1/node1'));
	
		// identity
		$k=new SWNode('node1','w1');
		$this->assertTrue($k->equals('w1/node1'));
	
		$this->assertTrue( $n->equals($k) );
	}
	public function testEquals_03() {
	
		$n=new SWNode('node1','w1');
		try{
			$n->equals('');
			$this->fail();
		}catch(SWException $e){
			$this->assertEquals($e->getCode(),SWException::SW_ERR_CREATE_NODE);
		}

		try{
			$n->equals(null);
			$this->fail();
		}catch(SWException $e){
			$this->assertEquals($e->getCode(),SWException::SW_ERR_CREATE_NODE);
		}
	}
}
?>