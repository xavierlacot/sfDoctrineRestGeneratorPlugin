<?php

class sfResourceSerializerJson extends sfResourceSerializer
{
  public function getContentType()
  {
    return 'application/json';
  }

  public function serialize($array, $rootNodeName = 'data')
  {
    return json_encode($array);
  }
}