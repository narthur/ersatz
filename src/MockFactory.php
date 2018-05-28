<?php

namespace ersatz;

class MockFactory
{
	public function buildMock( $class )
	{
		$reflection = new \ReflectionClass( __NAMESPACE__ . "\\" . $class );
		$mockedShortName = $reflection->getShortName();
		$mockShortName = "Mock$mockedShortName";
		$mockClass = "\\$mockShortName";
		if ( ! class_exists($mockClass) ) {
			$this->declareMockClass( $reflection, $mockShortName, $mockedShortName );
		}
		return new $mockClass;
	}
	private function declareMockClass( \ReflectionClass $reflection, $mockShortName, $mockedShortName )
	{
		$php = [];
		$mockedNamespace = $reflection->getNamespaceName();
		$extends = $reflection->isInterface() ? 'implements' : 'extends';
		$php[] = <<<EOT
class $mockShortName $extends $mockedNamespace\\$mockedShortName {
	use \\ersatz\Mock;
EOT;
		$php = $this->addMethodPhp( $reflection, $php );
		$php[] = '}';
		$toEval = implode( "\n\n", $php );
		eval( $toEval );
	}
	private function addMethodPhp( \ReflectionClass $reflection, $php )
	{
		foreach ( $reflection->getMethods() as $method ) {
			if ( $method->name === "__construct" ) continue;
			$params = array_reduce( $method->getParameters(), function($carry, \ReflectionParameter $parameter) {
				if ( $parameter->isArray() ) $type = 'array ';
				else if ( $parameterClass = $parameter->getClass() ) $type = '\\' . $parameterClass->getName() . ' ';
				else $type = '';
				$default = ( $parameter->isOptional() ) ? "= null" : "";
				if ( $parameter->isVariadic() ) {
					return $carry + ["...\${$parameter->getName()}"];
				} else {
					return $carry + ["$type \${$parameter->getName()} $default"];
				}
			}, [] );
			$paramString = implode( ',', $params );
			$php[] = <<<EOT
	public function $method->name($paramString) {
		return \$this->handleCall("$method->name", func_get_args());
	}
EOT;
		}
		return $php;
	}
}