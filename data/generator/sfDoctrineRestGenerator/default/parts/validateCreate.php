  /**
   * Applies the creation validators to the XML posted to the service
   * @param   string   $xml  A XML string
   */
  public function validateCreate($xml)
  {
  	$validators = $this->getCreateValidators();
    $params = $this->parsePayload($xml);
    $unused = array_keys($validators);

  	foreach ($params as $name => $value)
  	{
  		if (!isset($validators[$name]))
  		{
  			throw new sfException(sprintf('Could not validate field "%s"', $name));
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
      $validators[$name]->clean(null);
    }
  }
