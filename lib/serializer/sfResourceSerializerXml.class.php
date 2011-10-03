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

  /**
   * Transform the payload into array assuming the payload is XML formatted.
   *
   * @param string $payload
   * @return array
   * @throw Exception
   */
  public function unserialize($payload)
  {
    libxml_use_internal_errors(true);
    $payload = trim($payload);

    if (empty($payload))
    {
      throw new sfException("Empty payload, can't unserialize it.");
    }

    // Try to parse the XML
    $xml = @simplexml_load_string(
      $payload,
      'SimpleXMLElement',
      LIBXML_NONET
    );

    // If false, there is a parse error.
    if ($xml === false)
    {
      $errors = libxml_get_errors();
      $exception_message = '';

      foreach ($errors as $error)
      {
        $exception_message .= $this->formatXmlError($error);
      }

      libxml_clear_errors();
      throw new sfException("XML parsing error(s): \n".$exception_message);
    }

    $return = $this->unserializeToArray($xml);

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

  /**
   * Return a formatted LibXml Error message
   * @see http://www.php.net/manual/en/function.libxml-get-errors.php
   * @param LibXMLError $error
   * @return string
   */
  protected function formatXmlError($error)
  {
    $return  = "\n\n";

    switch ($error->level)
    {
      case LIBXML_ERR_WARNING:
        $return .= "Warning $error->code: ";
        break;
      case LIBXML_ERR_ERROR:
        $return .= "Error $error->code: ";
        break;
      case LIBXML_ERR_FATAL:
        $return .= "Fatal Error $error->code: ";
        break;
    }

    $return .= trim($error->message) . "\n  Line: $error->line" .  "\n  Column: $error->column";

    if ($error->file)
    {
        $return .= "\n  File: $error->file";
    }

    return "$return\n\n--------------------------------------------\n\n";
  }

  protected function unserializeToArray($data)
  {
    if ($data instanceof SimpleXMLElement)
    {
      $data = (array) $data;
    }

    if (is_array($data))
    {
      foreach ($data as $name => $item)
      {
        unset($data[$name]);
        $unserialized = $this->unserializeToArray($item, true);

        if ($item instanceof SimpleXMLElement)
        {
          $array = (array) $item;
          $array2 = (array) $item;
          $first = array_pop($array2);
          $single_xml_string = (count($array) < 1) || (count($array) == 1 && is_string($first) && trim($first) === '');
        }
        else
        {
          $single_xml_string = false;
        }

        if ($single_xml_string)
        {
          $data[sfInflector::underscore($name)] = trim(array_pop($unserialized));
        }
        elseif (is_string($unserialized))
        {
          $data[sfInflector::underscore($name)] = $unserialized;
        }
        else
        {
          $data[$name] = $unserialized;
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