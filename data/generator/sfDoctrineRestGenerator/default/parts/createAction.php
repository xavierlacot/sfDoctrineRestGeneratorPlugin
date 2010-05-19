  /**
   * Creates a <?php echo $this->getModelClass() ?> object
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $content = $request->getParameter('content');
    $request->setRequestFormat('html');

    try
    {
      $this->validateCreate($content);
    }
    catch (Exception $e)
    {
      $format = $request->getParameter('sf_format');

      if (!in_array($format, <?php var_export($this->configuration->getValue('default.formats_enabled', array('json', 'xml'))) ?>))
      {
        $format = 'xml';
      }

    	$this->getResponse()->setStatusCode(406);
      $error = array(array('message' => $e->getMessage()));
      $serializer = sfResourceSerializer::getInstance($format);
      $this->getResponse()->setContentType($serializer->getContentType());
      $this->output = $serializer->serialize($error, 'error');
      $this->setTemplate('index');
      return sfView::SUCCESS;
    }

    $this->object = $this->createObject();
    $this->updateObjectFromRequest($content);
    return $this->doSave();
  }
