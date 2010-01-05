  /**
   * Creates a <?php echo $this->getModelClass() ?> object
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $content = $request->getParameter('content');

    try
    {
      $this->validateCreate($content);
    }
    catch (Exception $e)
    {
      $this->getResponse()->setStatusCode(406);
      echo $e->getMessage();
      return sfView::NONE;
    }

    $this->object = $this->createObject();
    $this->updateObjectFromRequest($content);
    return $this->doSave();
  }
