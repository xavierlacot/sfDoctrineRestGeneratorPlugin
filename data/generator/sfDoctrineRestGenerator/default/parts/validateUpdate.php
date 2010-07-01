  /**
   * Applies the update validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateUpdate($payload)
  {
  	$validators = $this->getUpdateValidators();
    $params = $this->parsePayload($payload);
  	$this->validate($params, $validators);
  }
