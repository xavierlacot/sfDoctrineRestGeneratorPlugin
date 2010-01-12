  protected function parsePayload($xml, $force = false)
  {
    if ($force || !isset($this->_payload_array))
    {
    	$dom = @simplexml_load_string($xml);

    	if (!$dom)
    	{
    	  throw new sfException('Could not load payload, obviously not a valid XML!');
    	}

      $this->_payload_array = array();

      foreach ($dom as $name => $value)
      {
      	$name = sfInflector::underscore($name);
      	$this->_payload_array[$name] = trim(rtrim((string)$value));
      }
    }

    return $this->_payload_array;
  }
