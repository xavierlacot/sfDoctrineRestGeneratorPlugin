  protected function getSerializer()
  {
    if (!isset($this->serializer))
    {
      try
      {
        $this->serializer = sfResourceSerializer::getInstance($this->getFormat());
      }
      catch (sfException $e)
      {
        $this->serializer = sfResourceSerializer::getInstance('<?php echo $this->configuration->getValue('get.default_format') ?>');
        throw new sfException($e->getMessage());
      }
    }

    return $this->serializer;
  }
