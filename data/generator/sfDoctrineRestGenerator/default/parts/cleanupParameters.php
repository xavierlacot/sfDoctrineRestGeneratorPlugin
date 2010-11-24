  /**
   * Cleans up the request parameters
   *
   * @param   array  $params  an array of parameters
   * @return  array  an array of cleaned parameters
   */
  protected function cleanupParameters($params)
  {
    unset($params['sf_format']);
    unset($params['module']);
    unset($params['action']);

    $additional_params = <?php var_export($this->configuration->getValue('get.additional_params', array())); ?>;

    foreach ($params as $name => $value)
    {
      if (!$value || in_array($name, $additional_params))
      {
        unset($params[$name]);
      }
    }

    return $params;
  }
