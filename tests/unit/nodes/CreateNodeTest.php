<?php
class CreateNodeTest extends CDbTestCase {

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
	public function testCreateFromArray_01() {
		$n = new SWNode(
			array('id'=>'node1'),
			'w1'	// workflow id as second constructor argument
		);
		$this->assertEquals($n->getId(), 'node1');
		$this->assertEquals($n->getLabel(), $n->getId());
		$this->assertEquals($n->getWorkflowId(), 'w1');
	}
	public function testCreateFromArray_02() {
		try{
			$n = new SWNode(array('empty'),'w1');	// node id is missing
			$this->fail();
		}catch(SWException $e){
			$this->assertEquals($e->getCode(),SWException::SW_ERR_MISSING_NODE_ID);
		}
	}
	public function testCreateFromArray_03() {
		try{
			$n = new SWNode(array('id'=>'node1'));	// no workflow id provided
			$this->fail();
		}catch(SWException $e){
			$this->assertEquals($e->getCode(),SWException::SW_ERR_CREATE_NODE);
		}
	}
	public function testCreateFromArray_04() {
		$n = new SWNode(array('id'=>'w1/node1'));
		$this->assertEquals($n->getId(), 'node1');
		$this->assertEquals($n->getLabel(), $n->getId());
		$this->assertEquals($n->getWorkflowId(), 'w1');
	}
	public function testCreateFromArray_05() {
		$n = new SWNode(array('id'=>'w1/node1'));	// workflow id part of node id
		$this->assertEquals($n->getId(), 'node1');
		$this->assertEquals($n->getLabel(), $n->getId());
		$this->assertEquals($n->getWorkflowId(), 'w1');
	}
	
	public function testCreateFromString_01() {
		$n = new SWNode('w1/node1');
		$this->assertEquals($n->getId(), 'node1');
		$this->assertEquals($n->getLabel(), $n->getId());
		$this->assertEquals($n->getWorkflowId(), 'w1');
	}
	public function testCreateFromString_02() {
		$n = new SWNode('node1','w1');
		$this->assertEquals($n->getId(), 'node1');
		$this->assertEquals($n->getLabel(), $n->getId());
		$this->assertEquals($n->getWorkflowId(), 'w1');
	}
	public function testCreateFromString_03() {
		
		$n = new SWNode('a/node_1');
		$this->assertEquals($n->getLabel(),'node_1');
		
		try{
			$n = new SWNode('a/node-1');	// character '-' is not allowed
			$this->fail();
		}catch(SWException $e){
			$this->assertEquals($e->getCode(),SWException::SW_ERR_CREATE_NODE);
		}
		try{
			$n = new SWNode('a/1node');	// character '1' is not allowed
			$this->fail();
		}catch(SWException $e){
			$this->assertEquals($e->getCode(),SWException::SW_ERR_CREATE_NODE);
		}
	}
	public function testCreateFromString_04() {
	
		$n = new SWNode('a/node_1');
		try{
			$n = new SWNode('node-1','a');	// character '-' is not allowed
			$this->fail();
		}catch(SWException $e){
			$this->assertEquals($e->getCode(),SWException::SW_ERR_CREATE_NODE);
		}
		try{
			$n = new SWNode('1node','a');	// character '1' is not allowed
			$this->fail();
		}catch(SWException $e){
			$this->assertEquals($e->getCode(),SWException::SW_ERR_CREATE_NODE);
		}
	}
	public function testCreateFromString_05() {
	
		try{
			$n = new SWNode('','a');	// character '_' is not allowed
			$this->fail();
		}catch(SWException $e){
			$this->assertEquals($e->getCode(),SWException::SW_ERR_CREATE_NODE);
		}
	}
}
?>