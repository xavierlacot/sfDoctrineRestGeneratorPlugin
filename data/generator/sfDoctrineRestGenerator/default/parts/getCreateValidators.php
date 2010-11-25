  /**
   * Returns the list of validators for a create request.
   * @return  array  an array of validators
   */
  public function getCreateValidators()
  {
    return <?php echo $this->getCreateValidatorsArray($this); ?>;
  }
