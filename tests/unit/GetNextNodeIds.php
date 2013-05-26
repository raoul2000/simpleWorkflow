<?php
class GetNextNodeIds extends CDbTestCase {

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
	public function testNextNodeIds_01() {

		$n=new SWNode(
			array(
				'id' => 'w1/node1',
				'transition' => 'node2,node1'
			)
		);
		
		$this->assertTrue($n->equals('w1/node1'));
		$next = $n->getNextNodeIds();
		$this->assertTrue(count($next) == 2);
		$this->assertTrue(in_array('w1/node1',$next));
		$this->assertTrue(in_array('w1/node2',$next));
		$this->assertFalse(in_array('w1/node3',$next));
	}
	public function testNextNodeIds_02() {
	
		$n=new SWNode(
			array(
			'id' => 'w1/node1'
			)
		);
		$next = $n->getNextNodeIds();
		$this->assertTrue(count($next) == 0);
	}
	public function testNextNodeIds_03() {
	
		$n=new SWNode(
			array(
			'id' => 'w1/node1',
			'transition' => array('node2','node1')
			)
		);
	
		$this->assertTrue($n->equals('w1/node1'));
		$next = $n->getNextNodeIds();
		$this->assertTrue(count($next) == 2);
		$this->assertTrue(in_array('w1/node1',$next));
		$this->assertTrue(in_array('w1/node2',$next));
		$this->assertFalse(in_array('w1/node3',$next));
	}
	public function testNextNodeIds_04() {
	
		$n=new SWNode(
			array(
			'id' => 'w1/node1',
			'transition' => array('node2','w2/node1')
			)
		);
	
		$this->assertTrue($n->equals('w1/node1'));
		$next = $n->getNextNodeIds();
		$this->assertTrue(count($next) == 2);
		$this->assertTrue(in_array('w2/node1',$next));
		$this->assertTrue(in_array('w1/node2',$next));
		$this->assertFalse(in_array('w1/node3',$next));
	}
	public function testNextNodeIds_05() {
	
		$n=new SWNode(
			array(
			'id' => 'w1/node1',
			'transition' => 'w2/node2,w1/node1'
			)
		);
	
		$this->assertTrue($n->equals('w1/node1'));
		$next = $n->getNextNodeIds();
		$this->assertTrue(count($next) == 2);
		$this->assertTrue(in_array('w1/node1',$next));
		$this->assertTrue(in_array('w2/node2',$next));
		$this->assertFalse(in_array('w1/node3',$next));
	}
	public function testNextNodeIds_06() {
	
		$n=new SWNode(
			array(
				'id' => 'w1/node1',
				'transition' => array(
					'node1'    => 'task1',
					'w2/node2' => 'task2',
					'node3'
				)
			)
		);
	
		$this->assertTrue($n->equals('w1/node1'));
		$next = $n->getNextNodeIds();
		$this->assertEquals(count($next), 3);
		$this->assertTrue(in_array('w1/node1',$next));
		$this->assertTrue(in_array('w2/node2',$next));
		$this->assertTrue(in_array('w1/node3',$next));
	}
}
?>