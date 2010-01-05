  protected function updateObjectFromRequest($content)
  {
    $this->object->importFrom('xml', $content);
  }
