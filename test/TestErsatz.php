<?php

namespace ersatz;

final class TestErsatz extends DevTestCase
{
	/** @var Basic|Mock $mockBasic */
	public $mockBasic;
	
	/** @var iInterface|Mock $mockiInterface */
	public $mockiInterface;
	
	public function testCanCreateMockObjectOfOriginalClass()
	{
		$this->assertTrue($this->mockBasic instanceof Basic);
	}
	
	public function testCanCreateMockObjectOfInterface()
	{
		$this->assertTrue($this->mockiInterface instanceof iInterface);
	}
	
	public function testSetReturnValue()
	{
		$this->mockiInterface->setReturnValue( "someFunction", "returnValue" );
		
		$result = $this->mockiInterface->someFunction("hi");
		
		$this->assertEquals( "returnValue", $result );
	}
	
	public function testGetCalls()
	{
		$this->mockiInterface->someFunction("hi");
		
		$calls = $this->mockiInterface->getCalls( "someFunction" );
		
		$this->assertEquals( [
			["hi"]
		], $calls );
	}
}