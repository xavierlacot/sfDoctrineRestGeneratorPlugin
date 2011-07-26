  /**
   * Applies a set of validators to an array of parameters
   *
   * @param array   $params      An array of parameters
   * @param array   $validators  An array of validators
   * @throw sfException
   */
  public function postValidate($params, $validators, $prefix = '')
  {
    foreach ($params as $name => $value)
    {
      if (isset($validators[$name]))
      {
        if (is_array($validators[$name]))
        {
          // validator for a related object
          $this->validate($value, $validators[$name], $prefix.$name.'.');
        }
        else
        {
          $validators[$name]->clean($value);
        }
      }
    }
  }