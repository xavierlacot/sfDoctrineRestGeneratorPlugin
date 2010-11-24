  /**
   * Applies the get validators to the constraint parameters passed to the
   * webservice
   *
   * @param   array   $params  An array of criterions used for the selection
   */
  public function validateShow($params)
  {
  	$validators = $this->getIndexValidators();
  	$this->validate($params, $validators);
  }
