  /**
  * Generates the url to an other action of the module
  * @param string $action the action to which a url must gbe generated
  *                       (index|show|delete|create)
  * @param bool $absolute whether or not to generate an absolute url
  * @return string the generated url
  **/
  protected function getUrlForAction($action = 'show', $absolute = true)
  {
    if ($action == 'show')
    {
      $route_parameters = $this->object->identifier();
    }
    else
    {
      $route_parameters = '';
    }

    return $this->getController()->genUrl(
      array_merge(
        array(
          'sf_route' => '<?php echo $this->getModuleName(); ?>_'.$action,
          'sf_format' => $this->getFormat(),
        ),
        $route_parameters
      ),
      $absolute
    );
  }
