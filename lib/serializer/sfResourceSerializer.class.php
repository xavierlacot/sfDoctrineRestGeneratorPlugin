<?php

abstract class sfResourceSerializer
{
  abstract public function getContentType();

  /**
   * This preg-free version of the camelizer is two times faster than
   * sfInflector::camelize()
   *
   * @author CakePHP
   * @see http://book.cakephp.org/view/572/Class-methods
   * @param  string $string  The string to camelize
   * @return string with CamelCase
   */
  protected function camelize($string)
  {
    return str_replace(" ", "", ucwords(str_replace("_", " ", $string)));
  }

  /**
   * Creates an instance of a seriazlizer
   *
   * @param  string  $format   The serializer format (xml, json, etc.)
   * @return object  a serializer object
   * @throws sfException
   */
  public static function getInstance($format = 'xml')
  {
    $classname = sprintf('sfResourceSerializer%s', ucfirst($format));

    if (!class_exists($classname))
    {
      throw new sfException(sprintf('Could not find seriaizer "%s"', $classname));
    }

    return new $classname;
  }

  abstract public function serialize($array, $rootNodeName = 'data', $collection = true);
  abstract public function unserialize($payload);
}