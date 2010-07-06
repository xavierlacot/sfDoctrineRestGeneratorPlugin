  /**
   * Updates a <?php echo $this->getModelClass() ?> object
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::PUT));
    $content = $request->getParameter('content');
    $request->setRequestFormat('html');

    try
    {
      $this->validateUpdate($content);
    }
    catch (Exception $e)
    {
      $format = $request->getParameter('sf_format');

      if (!in_array($format, <?php var_export($this->configuration->getValue('default.formats_enabled', array('json', 'xml'))) ?>))
      {
        $format = '<?php echo $this->configuration->getValue('get.default_format') ?>';
      }

    	$this->getResponse()->setStatusCode(406);
      $serializer = sfResourceSerializer::getInstance($format);
      $this->getResponse()->setContentType($serializer->getContentType());
      $error = $e->getMessage();

      // event filter to enable customisation of the error message.
      $result = $this->dispatcher->filter(
        new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
        $error
      )->getReturnValue();

      if ($error === $result)
      {
        $error = array(array('message' => $error));
        $this->output = $serializer->serialize($error, 'error');
      }
      else
      {
        $this->output = $serializer->serialize($result);
      }

      $this->setTemplate('index');
      return sfView::SUCCESS;
    }

    // retrieve the object
    <?php $primaryKey = Doctrine_Core::getTable($this->getModelClass())->getIdentifier() ?>
    $primaryKey = $request->getParameter('<?php echo $primaryKey ?>');
    $this->object = Doctrine_Core::getTable($this->model)->findOneBy<?php echo sfInflector::camelize($primaryKey) ?>($primaryKey);
    $this->forward404Unless($this->object);

    // update and save it
    $this->updateObjectFromRequest($content);
    return $this->doSave();
  }
