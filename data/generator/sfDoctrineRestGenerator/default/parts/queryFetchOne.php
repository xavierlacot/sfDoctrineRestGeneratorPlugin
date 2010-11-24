  /**
   * Execute the query for selecting an object, eventually along with related
   * objects
   *
   * @param   array   $params  an array of criterions for the selection
   */
  public function queryFetchOne($params)
  {
    $this->objects = array($this->dispatcher->filter(
      new sfEvent(
        $this,
        'sfDoctrineRestGenerator.filter_result',
        array()
      ),
      $this->query($params)->fetchOne(array(), Doctrine::HYDRATE_ARRAY)
    )->getReturnValue());
  }
