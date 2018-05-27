<?php

namespace ersatz;

final class TestErsatz extends DevTestCase
{
	/** @var Basic $mockBasic */
	public $mockBasic;
	
	/** @var iInterface $mockiInterface */
	public $mockiInterface;
	
	public function testCanCreateMockObjectOfOriginalClass()
	{
		$this->assertTrue($this->mockBasic instanceof Basic);
	}
	
	public function testCanCreateMockObjectOfInterface()
	{
		$this->assertTrue($this->mockiInterface instanceof iInterface);
	}
}