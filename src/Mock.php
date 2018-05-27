<?php

namespace ersatz;

trait Mock
{
	public function setReturnValue( $method, $returnValue ) {
		$field = $method . "ReturnValue";
		$this->$field = $returnValue;
	}
	
	public function getCalls ( $method ) {
		$callsProperty = $method . "Calls";
	
		return $this->$callsProperty;
	}
}