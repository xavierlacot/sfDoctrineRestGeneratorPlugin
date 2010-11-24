<?php

class sfResourceSerializerYaml extends sfResourceSerializer
{
  public function getContentType()
  {
    return 'application/yaml';
  }

  public function serialize($array, $rootNodeName = 'data', $collection = true)
  {
    return sfYaml::dump(array($rootNodeName => $array), 5);
  }

  public function unserialize($payload)
  {
    return sfYaml::parse($array);
  }
}