<?php

namespace ersatz;

trait Mock
{
	public function setReturnValue( $method, $returnValue ) {
		$field = $method . "ReturnValue";
		$this->$field = $returnValue;
	}
	
	public function setReturnValues( $method, $returnValues ) {
		$field = $method . "ReturnValues";
		$this->$field = $returnValues;
	}
	
	public function getCalls ( $method ) {
		$callsProperty = $method . "Calls";
	
		return $this->$callsProperty;
	}
}