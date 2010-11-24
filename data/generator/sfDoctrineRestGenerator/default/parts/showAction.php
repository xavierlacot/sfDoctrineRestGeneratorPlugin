  /**
   * Retrieves a <?php echo $this->getModelClass() ?> object
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeShow(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $params = $request->getParameterHolder()->getAll();

    // notify an event before the action's body starts
    $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

    $request->setRequestFormat('html');
    $this->setTemplate('index');
    $params = $this->cleanupParameters($params);

    try
    {
      $format = $this->getFormat();
      $this->validateShow($params);
    }
    catch (Exception $e)
    {
    	$this->getResponse()->setStatusCode(406);
      $serializer = $this->getSerializer();
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

      return sfView::SUCCESS;
    }

    $this->queryFetchOne($params);
    $this->forward404Unless(is_array($this->objects[0]));

<?php foreach ($this->configuration->getValue('get.object_additional_fields') as $field): ?>
    $this->embedAdditional<?php echo $field ?>(0, $params);
<?php endforeach; ?>
<?php foreach ($this->configuration->getValue('get.global_additional_fields') as $field): ?>
    $this->embedGlobalAdditional<?php echo $field ?>($params);
<?php endforeach; ?>

    $this->setFieldVisibility();
    $this->configureFields();

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType($serializer->getContentType());
    $this->output = $serializer->serialize($this->objects[0], $this->model, false);
    unset($this->objects);
  }
