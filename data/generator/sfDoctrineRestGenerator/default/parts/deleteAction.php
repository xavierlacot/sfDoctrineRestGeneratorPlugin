  /**
   * Removes a <?php echo $this->getModelClass() ?> object, based on its
   * primary key
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::DELETE));
    $id = $request->getParameter('id');
    $this->item = Doctrine::getTable($this->model)->find($id);
    $this->forward404Unless($this->item);
    $this->item->delete();
    return sfView::NONE;
  }
