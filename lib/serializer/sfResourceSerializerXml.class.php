<?php

class sfResourceSerializerXml extends sfResourceSerializer
{
  public function getContentType()
  {
    return 'application/xml';
  }

  public function serialize($array, $rootNodeName = 'data', $collection = true)
  {
    $camelizedRootNodeName = $this->camelize($rootNodeName);
    return $this->arrayToXml($array, $camelizedRootNodeName, 0, $collection);
  }

  public function unserialize($payload)
  {
    return @simplexml_load_string($payload);
  }

  protected function arrayToXml($array, $rootNodeName = 'Data', $level = 0, $collection = true)
  {
    $xml = '';

    if (0 == $level)
    {
      $plural = (true === $collection) ? 's' : '';
      $xml .= '<?xml version="1.0" encoding="utf-8"?><'.$rootNodeName.$plural.'>';
    }

    foreach ($array as $key => $value)
    {
      if (is_numeric($key))
      {
        $key = $rootNodeName;
      }
      else
      {
        $key = $this->camelize($key);
      }

      if (is_array($value))
      {
        $real_key = $key;

        if (!count($value) || isset($value[0]))
        {
          $real_key .= 's';
        }

        $xml .= '<'.$real_key.'>';
        $xml .= $this->arrayToXml($value, $key, $level + 1);
        $xml .= '</'.$real_key.'>';
      }
      else
      {
        $trimed_value = ($value !== false) ? trim($value) : '0';

        if ($trimed_value !== '')
        {
          if (htmlspecialchars($trimed_value) != $trimed_value)
          {
            $xml .= '<'.$key.'><![CDATA['.$trimed_value.']]></'.$key.'>';
          }
          else
          {
            $xml .= '<'.$key.'>'.$trimed_value.'</'.$key.'>';
          }
        }
      }
    }

    if (0 == $level)
    {
      $xml .= '</'.$rootNodeName.$plural.'>';
    }

    return $xml;
  }
}