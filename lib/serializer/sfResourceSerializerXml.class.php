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
    $return = $this->unserializeToArray(@simplexml_load_string(
      $payload,
      'SimpleXMLElement',
      LIBXML_NOERROR || LIBXML_NOWARNING || LIBXML_NONET
    ));

    // Shift any root node and return only the nested array
    if (is_array($return) && count($return) == 1)
    {
      // Don't want to break up the $return array
      $return_shifted = $return;
      $collection_return = array_shift($return_shifted);

      if (is_array($collection_return))
      {
        $return = $collection_return;
      }
    }

    return $return;
  }

  protected function unserializeToArray($data)
  {
    libxml_use_internal_errors(true);

    if ($data instanceof SimpleXMLElement)
    {
      $data = (array) $data;
    }

    if (is_array($data))
    {
      foreach ($data as $name => $item)
      {
        if ((!is_array($item) && (!is_object($item))) || ($item instanceof SimpleXMLElement && count((array) $item) < 1))
        {
          $item = trim((string)$item);
          unset($data[$name]);

          if ('' != $item)
          {
            $data[sfInflector::underscore($name)] = $this->unserializeToArray($item, true);
          }
        }
        else
        {
          $data[$name] = $this->unserializeToArray($item, true);
        }
      }
    }

    return $data;
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