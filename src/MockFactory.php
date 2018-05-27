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
	private function declareMockClass( $reflection, $mockShortName, $mockedShortName )
	{
		$php = [];
		$mockedNamespace = $reflection->getNamespaceName();
		$extends = $reflection->isInterface() ? 'implements' : 'extends';
		$php[] = <<<EOT
class $mockShortName $extends $mockedNamespace\\$mockedShortName {
	use \\ersatz\Mock;

	public function __construct() {}
EOT;
		$php = $this->addMethodPhp( $reflection, $php );
		$php[] = '}';
		$toEval = implode( "\n\n", $php );
		eval( $toEval );
	}
	private function addMethodPhp( $reflection, $php )
	{
		foreach ( $reflection->getMethods() as $method ) {
			$methodName = $method->name;
			if ( $methodName === "__construct" ) continue;
			$params = [];
			foreach ( $method->getParameters() as $i => $parameter ) {
				if ( $parameter->isArray() ) $type = 'array ';
				else if ( $parameterClass = $parameter->getClass() ) $type = '\\' . $parameterClass->getName() . ' ';
				else $type = '';
				$default = ( $parameter->isOptional() ) ? "= null" : "";
				
				if ( $parameter->isVariadic() ) {
					$params[] = "...\${$parameter->getName()}";
				} else {
					$params[] = "$type \${$parameter->getName()} $default";
				}
			}
			$paramString = implode( ',', $params );
			$valueField = $methodName . "ReturnValue";
			$valuesField = $methodName . "ReturnValues";
			$php[] = <<<EOT
	private \${$methodName}Calls = [];
	
	public function $methodName($paramString) {
		\$this->{$methodName}Calls[] = func_get_args();
		
		if ( ! empty(\$this->$valuesField) ) {
			return array_shift(\$this->$valuesField);
		}
		
		if ( isset( \$this->$valueField ) ) {
			return \$this->$valueField;
		}
	}
EOT;
		}
		//var_dump($php);
		return $php;
	}
}