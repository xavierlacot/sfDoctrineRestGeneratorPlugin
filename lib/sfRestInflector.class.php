<?php

class sfRestInflector extends sfInflector
{
  public static function arrayToXml($array, $rootNodeName = 'data', $level = 0)
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
        $xml .= self::arrayToXml($value, $key, $level + 1);
        $xml .= '</'.$real_key.'>';
      }
      else
      {
        if ('' != trim($value))
        {
          if (htmlspecialchars($value)!=$value)
          {
            $xml .= '<'.$key.'><![CDATA['.$value.']]></'.$key.'>';
          }
          else
          {
            $xml .= '<'.$key.'>'.$value.'</'.$key.'>';
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