  /**
   * Applies the get validators to the XML posted to the service
   * @param   array   $params  An array of criterions used for the selection
   */
  public function validateIndex($params)
  {
  	$validators = $this->getIndexValidators();

  	foreach ($params as $name => $value)
  	{
  		if (!isset($validators[$name]))
  		{
  			throw new sfException(sprintf('Could not validate field "%s"', $name));
  		}
  		else
  		{
  			$validators[$name]->clean($value);
  		}
  	}
  }
