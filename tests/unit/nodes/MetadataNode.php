<?php
class MetadataNode extends CDbTestCase {

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
	public function testMetadata_01() {

		$n=new SWNode(array(
			'id' => 'w1/node1',
			'metadata' => array(
				'attr1'=>'value1',
				'attr2'=>'value2',
				'attr3'=> array(1,2,3)
			)
		));
		$this->assertEquals($n->attr1, 'value1');
		$this->assertEquals($n->attr2, 'value2');
		$this->assertTrue(is_array($n->attr3) and isset($n->attr3[1]));
		
		$this->assertEquals(count(array_diff($n->attr3, array(1,2,3))),0);
		
		$this->assertTrue(count($n->getMetadata()) == 3);
		$md = $n->getMetadata();
		$this->assertEquals($md['attr1'], 'value1');
		$this->assertEquals($md['attr2'], 'value2');
		$this->assertTrue(is_array($md['attr3']));
		
	}
	public function testMetadata_02() {
	
		$n=new SWNode(array(
		'id' => 'w1/node1',
			'metadata' => array(
				'attr1'=>'value1',
				'attr2'=>'value2',
				'attr3'=> array(1,2,3)
			)
		));
		
		try{
			$n->attr4;
			$this->fail();
		}catch(SWException $e){
			$this->assertEquals($e->getCode(),SWException::SW_ERR_ATTR_NOT_FOUND);
		}
		
	}
}
?>