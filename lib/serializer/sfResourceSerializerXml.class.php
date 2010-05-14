<?php

class sfResourceSerializerXml extends sfResourceSerializer
{
  public function getContentType()
  {
    return 'application/xml';
  }

  public function serialize($array, $rootNodeName = 'data')
  {
    return $this->arrayToXml($array, $rootNodeName, 0);
  }

  public function arrayToXml($array, $rootNodeName = 'data', $level = 0)
  {
    $xml = '';

    if (0 == $level)
    {
      $xml .= '<?xml version="1.0" encoding="utf-8"?><'.ucfirst(sfInflector::camelize($rootNodeName)).'s>';
    }

    foreach ($array as $key => $value)
    {
      $key = ucfirst(sfInflector::camelize($key));

      if (is_numeric($key))
      {
        $key = ucfirst(sfInflector::camelize($rootNodeName));
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
      $xml .= '</'.ucfirst(sfInflector::camelize($rootNodeName)).'s>';
    }

    return $xml;
  }
}