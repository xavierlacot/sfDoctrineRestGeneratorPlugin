  protected function doSave()
  {
    $this->object->save();

    // Set a Location header with the path to the new / updated object
    $this->getResponse()->setHttpHeader('Location', $this->getUrlForAction('show', false));

    return sfView::NONE;
  }
