<?php namespace Core;

class Validation implements \ArrayAccess {


	public static function factory(array $array)
	{
		return new Validation($array);
	}

	protected $_bound = array();
	protected $_rules = array();
	protected $_labels = array();
	protected $_empty_rules = array('not_empty', 'matches');
	protected $_errors = array();
	protected $_data = array();


	public function __construct(array $array)
	{
		$this->_data = $array;
	}

	public function offsetSet($offset, $value)
	{
		throw new \Core_Exception('Validation objects are read-only.');
	}

	public function offsetExists($offset)
	{
		return isset($this->_data[$offset]);
	}

	public function offsetUnset($offset)
	{
		throw new \Core_Exception('Validation objects are read-only.');
	}

	public function offsetGet($offset)
	{
		return $this->_data[$offset];
	}

	public function copy(array $array)
	{
		$copy = clone $this;
		$copy->_data = $array;
		return $copy;
	}

	public function as_array()
	{
		return $this->_data;
	}

	public function data()
	{
		return $this->_data;
	}

	public function label($field, $label)
	{
		$this->_labels[$field] = $label;
		return $this;
	}

	public function labels(array $labels)
	{
		$this->_labels = $labels + $this->_labels;
		return $this;
	}

	public function rule($field, $rule, array $params = NULL)
	{
		if ($params === NULL)
		{
			$params = array(':value');
		}

		if ($field !== TRUE AND ! isset($this->_labels[$field]))
		{
			$this->_labels[$field] = \preg_replace('/[^\pL]+/u', ' ', $field);
		}

		$this->_rules[$field][] = array($rule, $params);

		return $this;
	}

	public function rules($field, array $rules)
	{
		foreach ($rules as $rule)
		{
			$this->rule($field, $rule[0], \Arr::get($rule, 1));
		}

		return $this;
	}

	public function bind($key, $value = NULL)
	{
		if (\is_array($key))
		{
			foreach ($key as $name => $value)
			{
				$this->_bound[$name] = $value;
			}
		}
		else
		{
			$this->_bound[$key] = $value;
		}

		return $this;
	}

	public function check()
	{
		if (\Hanariu::$profiling === TRUE)
		{
			$benchmark = \Profiler::start('\Hanariu\Validation', __FUNCTION__);
		}

		$data = $this->_errors = array();
		$original = $this->_data;
		$expected = \Arr::merge(\array_keys($original), \array_keys($this->_labels));
		$rules     = $this->_rules;

		foreach ($expected as $field)
		{
			$data[$field] = \Arr::get($this, $field);

			if (isset($rules[TRUE]))
			{
				if ( ! isset($rules[$field]))
				{
					$rules[$field] = array();
				}

				$rules[$field] = \array_merge($rules[$field], $rules[TRUE]);
			}
		}

		$this->_data = $data;
		unset($rules[TRUE]);

		$this->bind(':validation', $this);
		$this->bind(':data', $this->_data);

		foreach ($rules as $field => $set)
		{
			$value = $this[$field];
			$this->bind(array
			(
				':field' => $field,
				':value' => $value,
			));

			foreach ($set as $array)
			{
				list($rule, $params) = $array;

				foreach ($params as $key => $param)
				{
					if (\is_string($param) AND \array_key_exists($param, $this->_bound))
					{
						$params[$key] = $this->_bound[$param];
					}
				}

				$error_name = $rule;

				if (\is_array($rule))
				{
					if (\is_string($rule[0]) AND \array_key_exists($rule[0], $this->_bound))
					{
						$rule[0] = $this->_bound[$rule[0]];
					}

					$error_name = $rule[1];
					$passed = \call_user_func_array($rule, $params);
				}
				elseif ( ! is_string($rule))
				{
					$error_name = FALSE;
					$passed = \call_user_func_array($rule, $params);
				}
				elseif (\method_exists('Valid', $rule))
				{
					$method = new \ReflectionMethod('Valid', $rule);
					$passed = $method->invokeArgs(NULL, $params);
				}
				elseif (\strpos($rule, '::') === FALSE)
				{
					$function = new \ReflectionFunction($rule);
					$passed = $function->invokeArgs($params);
				}
				else
				{
					list($class, $method) = \explode('::', $rule, 2);
					$method = new \ReflectionMethod($class, $method);
					$passed = $method->invokeArgs(NULL, $params);
				}

				if ( ! \in_array($rule, $this->_empty_rules) AND ! \Core\Valid::not_empty($value))
					continue;

				if ($passed === FALSE AND $error_name !== FALSE)
				{
					$this->error($field, $error_name, $params);
					break;
				}
				elseif (isset($this->_errors[$field]))
				{
					break;
				}
			}
		}

		$this->_data = $original;

		if (isset($benchmark))
		{
			\Profiler::stop($benchmark);
		}

		return empty($this->_errors);
	}

	public function error($field, $error, array $params = NULL)
	{
		$this->_errors[$field] = array($error, $params);

		return $this;
	}


	public function errors($file = NULL, $translate = TRUE)
	{
		if ($file === NULL)
		{
			return $this->_errors;
		}

		$messages = array();

		foreach ($this->_errors as $field => $set)
		{
			list($error, $params) = $set;
			$label = $this->_labels[$field];

			if ($translate)
			{
				if (\is_string($translate))
				{
					$label = __($label, NULL, $translate);
				}
				else
				{
					$label = __($label);
				}
			}

			$values = array(
				':field' => $label,
				':value' => \Arr::get($this, $field),
			);

			if (\is_array($values[':value']))
			{
				$values[':value'] = \implode(', ', \Arr::flatten($values[':value']));
			}

			if ($params)
			{
				foreach ($params as $key => $value)
				{
					if (\is_array($value))
					{
						$value = \implode(', ', \Arr::flatten($value));
					}
					elseif (\is_object($value))
					{
						continue;
					}

					if (isset($this->_labels[$value]))
					{
						$value = $this->_labels[$value];

						if ($translate)
						{
							if (\is_string($translate))
							{
								$value = __($value, NULL, $translate);
							}
							else
							{
								$value = __($value);
							}
						}
					}
					$values[':param'.($key + 1)] = $value;
				}
			}

			if ($message = \Core\Message::message($file, "{$field}.{$error}") AND \is_string($message)){}
			elseif ($message = \Core\Message::message($file, "{$field}.default") AND \is_string($message)){}
			elseif ($message = \Core\Message::message($file, $error) AND \is_string($message)){}
			elseif ($message = \Core\Message::message('validation', $error) AND \is_string($message)){}
			else
			{
				$message = "{$file}.{$field}.{$error}";
			}

			if ($translate)
			{
				if (\is_string($translate))
				{
					$message = __($message, $values, $translate);
				}
				else
				{
					$message = __($message, $values);
				}
			}
			else
			{
				$message = \strtr($message, $values);
			}

			$messages[$field] = $message;
		}

		return $messages;
	}

}
