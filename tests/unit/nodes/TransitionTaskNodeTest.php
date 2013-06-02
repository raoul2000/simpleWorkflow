<?php
class TransitionTaskNodeTest extends CDbTestCase {

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
	public function testTransitionTask_01() {

		$n=new SWNode(
			array(
				'id' => 'node1',
				'transition' => 'node2,node1'
			),
			'w1'
		);
		
		$this->assertTrue($n->equals('w1/node1'));
		$this->assertTrue($n->getTransitionTask(new SWNode('node2','w1')) == null);
		$this->assertTrue($n->getTransitionTask(new SWNode('node1','w1')) == null);
		$this->assertTrue($n->getTransitionTask(new SWNode('node3','w1')) == null);
	}
	public function testTransitionTask_02() {
	
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
	
		$this->assertTrue($n->getTransitionTask('w1/node1') == 'task1');
		$this->assertTrue($n->getTransitionTask('node1') == 'task1');
		
		$this->assertTrue($n->getTransitionTask('w2/node2') == 'task2');
		$this->assertTrue($n->getTransitionTask('node2') ==  null);		// will be converted to w1/node2 : no transition then

		$this->assertTrue($n->getTransitionTask('w1/node3') == null);
		$this->assertTrue($n->getTransitionTask('node3') == null);
	}
}
?>