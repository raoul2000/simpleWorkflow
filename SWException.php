<?php

/**
 * Exception thrown by the simpleWorkflow behavior
 */
class SWException extends CException
{
	const SW_ERR_ATTR_NOT_FOUND		= 0x01;
	const SW_ERR_REETRANCE			= 0x02;
	const SW_ERR_WRONG_TYPE			= 0x03;
	const SW_ERR_IN_WORKFLOW		= 0x04;
	const SW_ERR_CREATE_FAILS		= 0x05;
	const SW_ERR_WRONG_STATUS		= 0x06;
	const SW_ERR_WORKFLOW_NOT_FOUND	= 0x07;
	const SW_ERR_WORKFLOW_ID		= 0x08;
	const SW_ERR_NODE_NOT_FOUND		= 0x09;
	const SW_ERR_STATUS_UNREACHABLE	= 0x0A;
	const SW_ERR_CREATE_NODE		= 0x0B;
	const SW_ERR_MISSING_NODE_ID	= 0x0C;
}