<?php
/**
 * sfTesterJsonResponse implements tests for the symfony response object.
 */
class sfTesterJsonResponse extends sfTester
{
  /**
   * Prepares the tester.
   */
  public function prepare()
  {
  }

  /**
   * Initializes the tester.
   */
  public function initialize()
  {
  }

  /**
   * Try to decode the response body from json format
   *
   * @return boolean
   */
  public function isJson()
  {
    $is_json = json_decode($this->browser->getResponse()->getContent());
    $is_json !== false ? $this->tester->pass('content is valid json') : $this->tester->fail('content is not valid json');

    return $this->getObjectToReturn();
  }
}
