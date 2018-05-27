<?php

namespace ersatz;

class Basic {
	public function __construct()
	{
		throw new \Exception( "__construct should be overridden!" );
	}
}