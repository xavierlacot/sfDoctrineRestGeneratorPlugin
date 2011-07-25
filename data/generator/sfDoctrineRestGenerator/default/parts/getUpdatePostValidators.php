  /**
   * Returns the list of validators for an update request.
   * @return  array  an array of validators
   */
  public function getUpdatePostValidators()
  {
    return $this->getCreatePostValidators() ;
  }
