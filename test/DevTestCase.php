<?php

namespace ersatz;

abstract class DevTestCase extends TestCase {
	/** @var Basic $mockBasic */
	public $mockBasic;
	
	public function testCanCreateMockObjectOfOriginalClass() {
		$this->assertTrue( $this->mockBasic instanceof Basic );
	}
}