  protected function doSave()
  {
    $this->object->save();
    return sfView::NONE;
  }
