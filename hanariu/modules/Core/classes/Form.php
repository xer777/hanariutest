<?php namespace Core;

class Form {

	public static function open($action = NULL, array $attributes = NULL)
	{
		if ($action instanceof \Request)
		{
			$action = $action->uri();
		}

		if ( ! $action)
		{
			$action = '';
		}
		elseif (\strpos($action, '://') === FALSE)
		{
			// Make the URI absolute
			$action = \URL::site($action);
		}

		$attributes['action'] = $action;
		$attributes['accept-charset'] = \Hanariu::$charset;

		if ( ! isset($attributes['method']))
		{
			$attributes['method'] = 'post';
		}

		return '<form'.\Core\HTML::attributes($attributes).'>';
	}


	public static function close()
	{
		return '</form>';
	}


	public static function input($name, $value = NULL, array $attributes = NULL)
	{
		$attributes['name'] = $name;
		$attributes['value'] = $value;

		if ( ! isset($attributes['type']))
		{
			$attributes['type'] = 'text';
		}

		return '<input'.\Core\HTML::attributes($attributes).' />';
	}

	public static function hidden($name, $value = NULL, array $attributes = NULL)
	{
		$attributes['type'] = 'hidden';

		return \Core\Form::input($name, $value, $attributes);
	}

	public static function password($name, $value = NULL, array $attributes = NULL)
	{
		$attributes['type'] = 'password';

		return \Core\Form::input($name, $value, $attributes);
	}

	public static function file($name, array $attributes = NULL)
	{
		$attributes['type'] = 'file';

		return \Core\Form::input($name, NULL, $attributes);
	}

	public static function checkbox($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
	{
		$attributes['type'] = 'checkbox';

		if ($checked === TRUE)
		{
			$attributes[] = 'checked';
		}

		return \Core\Form::input($name, $value, $attributes);
	}

	public static function radio($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
	{
		$attributes['type'] = 'radio';

		if ($checked === TRUE)
		{
			$attributes[] = 'checked';
		}

		return \Core\Form::input($name, $value, $attributes);
	}


	public static function textarea($name, $body = '', array $attributes = NULL, $double_encode = TRUE)
	{
		$attributes['name'] = $name;
		$attributes += array('rows' => 10, 'cols' => 50);

		return '<textarea'.\Core\HTML::attributes($attributes).'>'.\Core\HTML::chars($body, $double_encode).'</textarea>';
	}


	public static function select($name, array $options = NULL, $selected = NULL, array $attributes = NULL)
	{
		$attributes['name'] = $name;

		if (\is_array($selected))
		{
			$attributes[] = 'multiple';
		}

		if ( ! \is_array($selected))
		{
			if ($selected === NULL)
			{
				$selected = array();
			}
			else
			{
				$selected = array( (string) $selected);
			}
		}

		if (empty($options))
		{
			$options = '';
		}
		else
		{
			foreach ($options as $value => $name)
			{
				if (\is_array($name))
				{
					$group = array('label' => $value);

					// Create a new list of options
					$_options = array();

					foreach ($name as $_value => $_name)
					{
						$_value = (string) $_value;
						$option = array('value' => $_value);

						if (in_array($_value, $selected))
						{
							$option[] = 'selected';
						}

						$_options[] = '<option'.\Core\HTML::attributes($option).'>'.\Core\HTML::chars($_name, FALSE).'</option>';
					}

					$_options = "\n".\implode("\n", $_options)."\n";
					$options[$value] = '<optgroup'.\Core\HTML::attributes($group).'>'.$_options.'</optgroup>';
				}
				else
				{
					$value = (string) $value;
					$option = array('value' => $value);

					if (\in_array($value, $selected))
					{
						$option[] = 'selected';
					}

					$options[$value] = '<option'.\Core\HTML::attributes($option).'>'.\Core\HTML::chars($name, FALSE).'</option>';
				}
			}

			$options = "\n".\implode("\n", $options)."\n";
		}

		return '<select'.\Core\HTML::attributes($attributes).'>'.$options.'</select>';
	}

	public static function submit($name, $value, array $attributes = NULL)
	{
		$attributes['type'] = 'submit';

		return \Core\Form::input($name, $value, $attributes);
	}


	public static function image($name, $value, array $attributes = NULL, $index = FALSE)
	{
		if ( ! empty($attributes['src']))
		{
			if (\strpos($attributes['src'], '://') === FALSE)
			{
				// Add the base URL
				$attributes['src'] = \URL::base($index).$attributes['src'];
			}
		}

		$attributes['type'] = 'image';

		return \Core\Form::input($name, $value, $attributes);
	}


	public static function button($name, $body, array $attributes = NULL)
	{
		$attributes['name'] = $name;
		return '<button'.\Hanariu\HTML::attributes($attributes).'>'.$body.'</button>';
	}


	public static function label($input, $text = NULL, array $attributes = NULL)
	{
		if ($text === NULL)
		{
			$text = \ucwords(\preg_replace('/[\W_]+/', ' ', $input));
		}

		$attributes['for'] = $input;
		return '<label'.\Hanariu\HTML::attributes($attributes).'>'.$text.'</label>';
	}

	public static function open_fieldset($attributes = NULL)
	{
		return '<fieldset'.\Hanariu\HTML::attributes($attributes).'>';
	}


	public static function close_fieldset()
	{
		return '</fieldset>';
	}

	public static function legend($text = NULL, array $attributes = NULL)
	{
		if ($text === NULL)
		{
			// Use the input name as the text
			$text = \ucwords(\preg_replace('/[\W_]+/', ' ', $input));
		}

		return '<legend'.\Hanariu\HTML::attributes($attributes).'>'.$text.'</legend>';
	}
}
