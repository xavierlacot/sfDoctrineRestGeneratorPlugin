  protected function parsePayload($payload, $force = false)
  {
    if ($force || !isset($this->_payload_array))
    {
      $format = $this->getRequest()->getParameter('sf_format);

      if (!in_array($format, <?php var_export($this->configuration->getValue('default.formats_enabled', array('json', 'xml'))) ?>))
      {
        $format = 'xml';
      }

      if ('xml' == $format)
      {
    	  $payload_array = @simplexml_load_string($payload);
      }
      elseif ('json' == $format)
      {
    	  $payload_array = json_decode($payload, true);
      }

    	if (!isset($payload_array) || !$payload_array)
    	{
    	  throw new sfException('Could not load payload, obviously not a valid XML!');
    	}

      $this->_payload_array = array();

      foreach ($payload_array as $name => $value)
      {
      	$name = sfInflector::underscore($name);
      	$this->_payload_array[$name] = trim(rtrim((string)$value));
      }
    }

    return $this->_payload_array;
  }
