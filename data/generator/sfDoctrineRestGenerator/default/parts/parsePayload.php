  protected function parsePayload($payload, $force = false)
  {
    if ($force || !isset($this->_payload_array))
    {
      $format = $this->getFormat();
      $serializer = $this->getSerializer();

      if ($serializer)
      {
        $payload_array = $serializer->unserialize($payload);
      }

    	if (!isset($payload_array) || !$payload_array)
    	{
    	  throw new sfException(sprintf('Could not parse payload, obviously not a valid %s data!', $format));
    	}

      $this->_payload_array = array();

      foreach ($payload_array as $name => $value)
      {
      	$name = sfInflector::underscore($name);
      	$this->_payload_array[$name] = trim((string)$value);
      }
    }

    return $this->_payload_array;
  }
