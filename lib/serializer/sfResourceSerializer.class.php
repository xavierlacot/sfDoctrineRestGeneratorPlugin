<?php

abstract class sfResourceSerializer
{
  abstract public function getContentType();

  public static function getInstance($format = 'xml')
  {
    $classname = sprintf('sfResourceSerializer%s', ucfirst($format));

    if (!class_exists($classname))
    {
      throw new sfException(sprintf('Could not find seriaizer "%s"', $classname));
    }

    return new $classname;
  }

  abstract public function serialize($array, $rootNodeName = 'data');
}