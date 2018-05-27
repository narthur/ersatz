<?php

namespace ersatz;

final class TestErsatz extends DevTestCase {
	/** @var Basic $mockBasic */
	public $mockBasic;
	
	public function testCanCreateMockObjectOfOriginalClass() {
		$this->assertTrue( $this->mockBasic instanceof Basic );
	}
}