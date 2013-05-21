<?php namespace Core;

abstract class Model {

	public static function factory($name)
	{
		$class = '\\Model\\'.$name;
		return new $class;
	}

}
