  /**
   * Applies a set of validators to an array of parameters
   *
   * @param array   $params      An array of parameters
   * @param array   $validators  An array of validators
   * @throw sfException
   */
  public function validate($params, $validators)
  {
    $unused = array_keys($validators);

  	foreach ($params as $name => $value)
  	{
  		if (!isset($validators[$name]))
  		{
  			throw new sfException(sprintf('Could not validate extra field "%s"', $name));
  		}
  		else
  		{
  			$validators[$name]->clean($value);
        unset($unused[array_search($name, $unused, true)]);
  		}
  	}

    // are non given values required?
    foreach ($unused as $name)
    {
      try
      {
        $validators[$name]->clean(null);
      }
      catch (Exception $e)
      {
  			throw new sfException(sprintf('Could not validate field "%s": %s', $name, $e->getMessage()));
      }
    }
  }