  protected function updateObjectFromRequest($content)
  {
    $this->object->importFrom('array', $this->parsePayload($content));
  }
