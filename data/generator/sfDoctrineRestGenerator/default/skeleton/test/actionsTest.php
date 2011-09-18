<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');

/**
 * This is an example class of sfTestFunctional
 * It may require some attention to work with the default values (line 40).
 */

$browser           = new sfBrowser();
$test_browser      = new sfTestFunctional($browser);
$test_browser->setTester('json.response', 'sfTesterJsonResponse');

$test_browser->
  get('/##MODULE_NAME##')->

  with('request')->begin()->
    isParameter('module', '##MODULE_NAME##')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
  end()->

  with('json.response')->begin()->
    isJson()->
  end()
;

/**
 * Test the creation
 */
$entity         = new ##MODEL_CLASS##();
$entity_array   = $entity->exportTo('array');
$identifier     = $entity->getTable()->getIdentifier();

/**
 * Please build a valid $entity_array here
 */
unset($entity_array[$identifier]);
//$entity_array['name'] = "pony";
//$entity_array['created_at'] = date('Y-m-d H:i:s');
//$entity_array['updated_at'] = date('Y-m-d H:i:s');

$test_browser->
  call('/##MODULE_NAME##', 'post', array('content' => json_encode($entity_array)))->

  with('request')->begin()->
    isParameter('module', '##MODULE_NAME##')->
    isParameter('action', 'create')->
  end()->

  with('response')->begin()->//debug()->
    isStatusCode(200)->
  end()
;

/**
 * If the new entity has been created
 */
$location = $browser->getResponse()->getHttpHeader('Location');

if ($location)
{
  // Get ?
  $test_browser->
    get($location)->

    with('request')->begin()->
      isParameter('module', '##MODULE_NAME##')->
      isParameter('action', 'show')->
    end()->

    with('response')->begin()->
      isStatusCode(200)->
    end()->

    with('json.response')->begin()->
      isJson()->
    end()
  ;

  // Update ?
  $test_browser->
    call($location, 'put', array('content' => json_encode($entity_array)))->

    with('request')->begin()->
      isParameter('module', '##MODULE_NAME##')->
      isParameter('action', 'update')->
    end()->

    with('response')->begin()->//debug()->
      isStatusCode(200)->
    end()
  ;

  // Delete ?
  $test_browser->
    call($location, 'delete')->

    with('request')->begin()->
      isParameter('module', '##MODULE_NAME##')->
      isParameter('action', 'delete')->
    end()->

    with('response')->begin()->//debug()->
      isStatusCode(200)->
    end()
  ;
}
else
{
  $test_browser->test()->fail("The last response doesn't have any Location header!");
}