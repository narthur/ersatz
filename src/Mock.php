<?php

namespace ersatz;

trait Mock
{
	public function setReturnValue( $method, $returnValue ) {
		$field = $method . "ReturnValue";
		$this->$field = $returnValue;
	}
}