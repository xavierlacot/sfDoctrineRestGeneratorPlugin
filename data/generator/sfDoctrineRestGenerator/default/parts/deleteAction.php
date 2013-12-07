  /**
   * Removes a <?php echo $this->getModelClass() ?> object, based on its
   * primary key
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeDelete(sfWebRequest $request)
  {
<?php $primaryKey = $this->configuration->getValue('default.update_key', Doctrine_Core::getTable($this->getModelClass())->getIdentifier()); ?>
    $this->forward404Unless($request->isMethod(sfRequest::DELETE));
    $primaryKey = $request->getParameter('<?php echo $primaryKey ?>');
    $this->forward404Unless($primaryKey);
    $this->item = Doctrine_Core::getTable($this->model)->findOneBy<?php echo sfInflector::camelize($primaryKey) ?>($primaryKey);
    $this->forward404Unless($this->item);
    $this->item->delete();
    return sfView::NONE;
  }
