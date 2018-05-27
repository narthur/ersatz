<?php

namespace ersatz;

abstract class TestCase extends \PHPUnit\Framework\TestCase {
	/* Helper Fields */
	
	/** @var MockFactory $objectMocker */
	protected $objectMocker;
	
	protected function setUp()
	{
		define( "ABSPATH", "/" );
		
		$_POST = array();
		$_GET  = array();
		
		$this->objectMocker = new MockFactory();
		
		$this->resetMocks();
		$this->resetMockedObjects();
	}
	
	private function resetMocks() {
		$fieldNames = $this->getFilteredFieldNames( [ $this, 'isMockField' ] );
		foreach ($fieldNames as $fieldName) {
			$className = substr( $fieldName, 4 );
			$this->$fieldName = $this->objectMocker->buildMock( "$className" );
		}
	}
	
	private function isMockField( $key ) {
		return strpos( $key, 'mock' ) !== false && strpos( $key, 'mocked' ) === false;
	}
	
	private function resetMockedObjects() {
		$fieldNames = $this->getFilteredFieldNames( [ $this, "isMockedField" ] );
		foreach ($fieldNames as $fieldName) {
			$this->resetMockedObject( $fieldName );
		}
	}
	
	private function isMockedField( $key ) {
		return strpos( $key, "mocked" ) !== false;
	}
	
	private function getFilteredFieldNames( $callback )
	{
		return array_keys( array_filter( get_object_vars( $this ), $callback, ARRAY_FILTER_USE_KEY ) );
	}
	
	private function resetMockedObject( $fieldName )
	{
		$className = __NAMESPACE__ . "\\" . substr( $fieldName, 6 );
		$params = $this->getConstructorParameters( $className );
		$mocks = $this->getMocksForParams( $params );
		$this->$fieldName = new $className( ...$mocks );
	}
	
	private function getConstructorParameters( $fullClassName )
	{
		$reflectionClass = new \ReflectionClass( $fullClassName );
		$constructor = $reflectionClass->getConstructor();
		return ( $constructor ) ? $constructor->getParameters() : [];
	}
	
	private function getMocksForParams( $params )
	{
		return array_map( [ $this, "getMockForParam" ], $params );
	}
	
	private function getMockForParam( $param ) {
		$class = $param->getClass()->name;
		$reflect = new \ReflectionClass( "\\$class" );
		$shortName = $reflect->getShortName();
		$mockName = 'mock' . $shortName;
		return $this->$mockName;
	}
	
	protected function output( $data ) {
		fwrite(STDERR, print_r("\n" . var_export($data, true) . "\n", TRUE));
	}
	
	/* Assertions */
	protected function assertCalled( $mock, $method ) {
		$mockName = get_class( $mock );
		$error = "Failed asserting that $mockName->$method() was called.";
		$this->assertNotEmpty( $mock->getCalls( $method ), $error );
	}
	
	protected function assertNotCalled( $mock, $method ) {
		$mockName = get_class( $mock );
		$error = "Failed asserting that $mockName->$method() was not called.";
		$this->assertEmpty( $mock->getCalls( $method ), $error );
	}
	
	protected function assertCalledWith( $mock, $method, ...$arguments ) {
		$calls = $mock->getCalls( $method );
		$mockName = get_class( $mock );
		
		$this->assertMethodExists( $mockName, $method, $calls );
		
		$error = $this->makeHaystackError( $mockName, $method, $arguments, $calls, "was" );
		
		$this->assertTrue( in_array( $arguments, $calls, TRUE ), $error );
	}
	
	protected function assertNotCalledWith( $mock, $method, ...$arguments ) {
		$calls = $mock->getCalls( $method );
		$mockName = get_class( $mock );
		
		$this->assertMethodExists( $mockName, $method, $calls );
		
		$error = $this->makeHaystackError( $mockName, $method, $arguments, $calls, "was not" );
		
		$this->assertFalse( in_array( $arguments, $calls, TRUE ), $error );
	}
	
	private function assertMethodExists( $mockName, $method, $calls ) {
		$nullError = "$mockName->$method() does not exist.";
		$this->assertNotNull( $calls, $nullError );
	}
	
	private function makeHaystackError( $mockName, $method, $arguments, $calls, $wasOrWasNot ) {
		$failureMessage = "Failed asserting that $mockName->$method() $wasOrWasNot called with specified args.";
		
		try {
			$errorLines = [
				$failureMessage,
				"Needle:",
				var_export( $arguments, TRUE ),
				"Haystack:",
				var_export( $calls, TRUE )
			];
		} catch ( \Exception $e ) {
			$errorLines = [
				$failureMessage,
				"Failed to export needle and haystack:",
				$e->getMessage()
			];
		}
		
		return implode( "\r\n\r\n", $errorLines );
	}
	
	protected function assertAnyCallMatches( $mock, $method, $callback ) {
		$calls = $mock->getCalls( $method );
		
		$result = array_reduce( $calls, $callback, FALSE );
		
		$this->assertTrue( $result );
	}
}