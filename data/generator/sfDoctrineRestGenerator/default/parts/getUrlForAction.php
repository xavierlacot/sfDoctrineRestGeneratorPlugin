  /**
  * generate the url to another action of the module
  * @param string $route_name the route name to generate (index|show|delete|create)
  * @param bool $absolute absolute url
  * @return string the generated url
  **/
  protected function getUrlForAction($route_name='index', $absolute=true){


    $route_parameters = (in_array($route_name,array('show','delete'))?$this->object->identifier():'');


    return $this->getController()->genUrl(
       array_merge(array(
        'sf_route' => '<?php echo $this->getModuleName(); ?>_show',
        'sf_format' => $this->getFormat(),
        ), $route_parameters
       ),$absolute);


  }

