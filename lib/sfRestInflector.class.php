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
        $xml .= '<'.$key.'>';
        $xml .= self::arrayToXml($value, '', $level + 1);
        $xml .= '</'.$key.'>';
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