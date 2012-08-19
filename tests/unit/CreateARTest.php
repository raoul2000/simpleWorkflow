<?php
class CreateARTest extends CDbTestCase {
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
		$m=new Model01();

		$this->assertEquals($m->swGetWorkflowId(),'workflow1');
		$this->assertEquals($m->swGetDefaultWorkflowId(),'workflow1');
		$this->assertTrue($m->swHasStatus());
		
		$this->assertTrue($m->swIsInitialStatus($m->swGetStatus()));
		$this->assertTrue($m->swIsInitialStatus('workflow1/S1'));
		$this->assertTrue($m->swIsInitialStatus('S1'));
		$this->assertTrue($m->swIsInitialStatus());
		$this->assertFalse($m->swIsFinalStatus());
		
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S1');
		$this->assertEquals(count($m->swGetAllStatus()),5);
		$this->assertEquals($m->swIsEventEnabled(),false);
	}
	/**
	 * http://www.yiiframework.com/forum/index.php/topic/12071-extension-simpleworkflow/page__view__findpost__p__164472
	 */
	public function testInitialisation1_bis() {
		$m=new Model01();
		
		$this->assertEquals($m->swGetWorkflowId(),'workflow1');
		$this->assertEquals($m->swGetDefaultWorkflowId(),'workflow1');
		$this->assertTrue($m->swHasStatus());
	
		$this->assertTrue($m->save());
	}
	/**
	 *
	 */
 	public function testInitialisation2() {
		$continue=true;
 		try {
			$m=new Model02();
			$this->assertTrue($m->swNextStatus());
			$continue=false;
		} catch (Exception $e) {
			$this->assertEquals($e->getCode(), SWException::SW_ERR_WORKFLOW_NOT_FOUND);
		}
		$this->assertTrue($continue);
	}
	/**
	 *
	 */
	public function testInitialisation3() {
		$m=new Model03();	// autoInsert=false, workflow=workflow1
		
		$this->assertEquals($m->swHasStatus(),false);
		$this->assertEquals($m->swGetWorkflowId(),null);
		$this->assertEquals($m->swGetDefaultWorkflowId(),'workflow1');
		$this->assertEquals(count($m->swGetAllStatus()),0);
		$this->assertEquals($m->swGetStatus(),null);
		$this->assertEquals($m->swIsEventEnabled(),false);
		$this->assertFalse($m->swIsFinalStatus());
		
		$continue=true;
		try {
			$this->assertTrue($m->swIsInitialStatus($m->swGetStatus()));
			$continue=false;
		} catch (Exception $e) {
			$this->assertEquals($e->getCode(),SWException::SW_ERR_CREATE_FAILS);
		}
		$this->assertTrue($continue);
			
		$this->assertTrue($m->swIsInitialStatus('workflow1/S1'));
		$this->assertTrue($m->swIsInitialStatus('S1'));
		
		// lazy workflow insertion
		
		$m->swInsertToWorkflow();
		
		$this->assertEquals($m->swGetWorkflowId(),'workflow1');
		$this->assertEquals($m->swGetDefaultWorkflowId(),'workflow1');
		$this->assertTrue($m->swHasStatus());
		$this->assertTrue($m->swIsInitialStatus());
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S1');
		$this->assertEquals(count($m->swGetAllStatus()),5);
		$this->assertEquals($m->swIsEventEnabled(),false);
			
		$this->assertTrue($m->swIsInitialStatus($m->swGetStatus()));
		$this->assertTrue($m->swIsInitialStatus('workflow1/S1'));
		$this->assertTrue($m->swIsInitialStatus('S1'));
		$this->assertFalse($m->swIsFinalStatus());
	}
	/**
	 * Model has no declared default workflow so 'swModel04' will be used, but
	 * as it does not exist, some exception will be thrown.
	 * Model is later inserted into a workflow different that the default workflow.
	 */
 	public function testInitialisation4() {
		$m=new Model04();	// autoInsert=false, workflow=?

		$this->assertEquals($m->swHasStatus(),false);
		$this->assertEquals($m->swGetWorkflowId(),null);
		$this->assertEquals($m->swGetDefaultWorkflowId(),'swModel04');
		$this->assertEquals(count($m->swGetAllStatus()),0);
		$this->assertEquals($m->swGetStatus(),null);
		$this->assertEquals($m->swIsEventEnabled(),false);
		$continue=true;

		try {
			$this->assertTrue($m->swIsInitialStatus($m->swGetStatus()));
			$continue=false;
		} catch (Exception $e) {
			// workflow 'swModel05' does not exist
			$this->assertEquals($e->getCode(),SWException::SW_ERR_CREATE_FAILS);
		}
		$this->assertTrue($continue);
		$continue=true;
		$this->assertTrue($m->swIsInitialStatus('workflow1/S1'));

		$continue=true;
 		try {
 			// 'S1' is assumed to belong to default workflow 'swModel04' but this
 			// workflow does not exist
			$this->assertTrue($m->swIsInitialStatus('S1'));
			$continue=false;
		} catch (Exception $e) {
			// workflow 'swModel05' does not exist
			$this->assertEquals($e->getCode(),SWException::SW_ERR_WORKFLOW_NOT_FOUND);
		}
		$this->assertTrue($continue);
		$continue=true;
		$this->assertFalse($m->swIsFinalStatus());
		
		// lazy workflow insertion
		
		$m->swInsertToWorkflow('workflow1');
		
		$this->assertEquals($m->swGetWorkflowId(),'workflow1');
		$this->assertEquals($m->swGetDefaultWorkflowId(),'swModel04');
		$this->assertTrue($m->swHasStatus());
		$this->assertTrue($m->swIsInitialStatus());
		$this->assertEquals($m->swGetStatus()->toString(),'workflow1/S1');
		$this->assertEquals(count($m->swGetAllStatus()),5);
		$this->assertEquals($m->swIsEventEnabled(),false);
			
		$this->assertTrue($m->swIsInitialStatus($m->swGetStatus()));
		$this->assertTrue($m->swIsInitialStatus('workflow1/S1'));
		$this->assertTrue($m->swIsInitialStatus('S1'));
		$this->assertFalse($m->swIsFinalStatus());
	}
	/**
	 * Exception thrown because owner AR does not have a attr to store status
	 */
 	public function testInitialisation5() {
		$continue=true;
 		try {
			$m=new Model05();

			$continue=false;
		} catch (Exception $e) {
			$this->assertEquals($e->getCode(), SWException::SW_ERR_ATTR_NOT_FOUND);
		}
		$this->assertTrue($continue);
	}
}
?>