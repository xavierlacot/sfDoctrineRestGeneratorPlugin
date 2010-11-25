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

      $filter_params = <?php var_export(array_merge(
        $this->configuration->getValue('get.global_additional_fields', array()),
        $this->configuration->getValue('get.object_additional_fields', array())
      )) ?>;

      $this->_payload_array = sfDoctrineRestGenerator::underscorePayload($payload_array, $filter_params);
    }

    return $this->_payload_array;
  }
