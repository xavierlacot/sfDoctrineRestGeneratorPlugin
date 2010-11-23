<?php

class sfDoctrineRestGeneratorConfiguration
{
  protected
    $configuration = array();

  /**
   * Constructor.
   */
  public function __construct()
  {
    $this->compile();
  }

  protected function compile()
  {
    $this->configuration = array(
      'default' => array(
        'fields'                      => $this->getFieldsDefault(),
        'formats_enabled'             => $this->getFormatsEnabled(),
        'formats_strict'              => $this->getFormatsStrict(),
        'separator'                   => $this->getSeparator()
      ),
      'get'     => array(
        'additional_params'           => $this->getAdditionalParams(),
        'default_format'              => $this->getDefaultFormat(),
        'display'                     => $this->getDisplay(),
        'embed_relations'             => $this->getEmbedRelations(),
        'embedded_relations_hide'     => $this->getEmbeddedRelationsHide(),
        'fields'                      => $this->getFieldsGet(),
        'filters'                     => $this->getFilters(),
        'global_additional_fields'    => $this->getGlobalAdditionalFields(),
        'hide'                        => $this->getHide(),
        'max_items'                   => $this->getMaxItems(),
        'object_additional_fields'    => $this->getObjectAdditionalFields(),
        'pagination_custom_page_size' => $this->getPaginationCustomPageSize(),
        'pagination_enabled'          => $this->getPaginationEnabled(),
        'pagination_page_size'        => $this->getPaginationPageSize(),
        'sort_custom'                 => $this->getSortCustom(),
        'sort_default'                => $this->getSortDefault()
      )
    );
  }

  /**
   * Gets the value for a given key.
   *
   * @param array  $config  The configuration
   * @param string $key     The key name
   * @param mixed  $default The default value
   *
   * @return mixed The key value
   */
  static public function getFieldConfigValue($config, $key, $default = null)
  {
    $ref   =& $config;
    $parts =  explode('.', $key);
    $count =  count($parts);
    for ($i = 0; $i < $count; $i++)
    {
      $partKey = $parts[$i];
      if (!isset($ref[$partKey]))
      {
        return $default;
      }

      if ($count == $i + 1)
      {
        return $ref[$partKey];
      }
      else
      {
        $ref =& $ref[$partKey];
      }
    }

    return $default;
  }

  public function getContextConfiguration($context)
  {
    if (!isset($this->configuration[$context]))
    {
      throw new InvalidArgumentException(sprintf('The context "%s" does not exist.', $context));
    }

    return $this->configuration[$context];
  }

  /**
   * Gets the configuration for a given field.
   *
   * @param string  $key     The configuration key (title.list.name for example)
   * @param mixed   $default The default value if none has been defined
   * @param Boolean $escaped Whether to escape single quote (false by default)
   *
   * @return mixed The configuration value
   */
  public function getValue($key, $default = null, $escaped = false)
  {
    if (preg_match('/^(?P<context>[^\.]+)\.(?P<key>.+)$/', $key, $matches))
    {
      $v = sfModelGeneratorConfiguration::getFieldConfigValue($this->getContextConfiguration($matches['context']), $matches['key'], $default);
    }
    elseif (!isset($this->configuration[$key]))
    {
      throw new InvalidArgumentException(sprintf('The key "%s" does not exist.', $key));
    }
    else
    {
      $v = $this->configuration[$key];
    }

    return $escaped ? str_replace("'", "\\'", $v) : $v;
  }
}
