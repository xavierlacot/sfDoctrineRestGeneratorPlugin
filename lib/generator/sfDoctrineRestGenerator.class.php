<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Model generator.
 *
 * @package    symfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfModelGenerator.class.php 23194 2009-10-19 16:37:13Z fabien $
 */
class sfDoctrineRestGenerator extends sfGenerator
{
  protected
    $configuration = null,
    $primaryKey    = array(),
    $modelClass    = '',
    $params        = array(),
    $config        = array(),
    $formObject    = null;

  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);
    $this->setGeneratorClass('sfDoctrineRestGenerator');
  }

  public function underscore($name)
  {
    return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), '\\1_\\2', $name));
  }

  public static function underscorePayload($payload_array, $filter_params = array())
  {
    foreach ($payload_array as $name => $value)
    {
      if (!in_array($name, $filter_params))
      {

        if (!is_array($value))
        {
          $value = trim((string)$value);
          $underscored_name = sfInflector::underscore($name);
          $payload_array[$underscored_name] = $value;
          unset($payload_array[$name]);
        }
        else
        {
          // in the case of relations, do not transform the name of the relation
          $value = self::underscorePayload($value);
          $payload_array[$name] = $value;
        }
      }
      else unset($payload_array[$name]);
    }

    return $payload_array;
  }

  /**
   * Generates classes and templates in cache.
   *
   * @param array $params The parameters
   *
   * @return string The data to put in configuration cache
   */
  public function generate($params = array())
  {
    $this->validateParameters($params);

    $this->modelClass = $this->params['model_class'];
    $this->table = Doctrine_Core::getTable($this->params['model_class']);

    // generated module name
    $this->setModuleName($this->params['moduleName']);
    $this->setGeneratedModuleName('auto'.ucfirst($this->params['moduleName']));

    // theme exists?
    $theme = isset($this->params['theme']) ? $this->params['theme'] : 'default';
    $this->setTheme($theme);
    $themeDir = $this->generatorManager->getConfiguration()->getGeneratorTemplate('sfDoctrineRestGenerator', $theme, '');
    if (!is_dir($themeDir))
    {
      throw new sfConfigurationException(sprintf('The theme "%s" does not exist.', $theme));
    }

    // configure the model
    $this->configure();

    $this->configuration = $this->loadConfiguration();

    // generate files
    $this->generatePhpFiles($this->generatedModuleName, sfFinder::type('file')->relative()->in($themeDir));

    // move helper file
    if (file_exists($file = $this->generatorManager->getBasePath().'/'.$this->getGeneratedModuleName().'/lib/helper.php'))
    {
      @rename($file, $this->generatorManager->getBasePath().'/'.$this->getGeneratedModuleName().'/lib/Base'.ucfirst($this->moduleName).'GeneratorHelper.class.php');
    }

    return "require_once(sfConfig::get('sf_module_cache_dir').'/".$this->generatedModuleName."/actions/actions.class.php');";
  }

  /**
   * Gets the actions base class for the generated module.
   *
   * @return string The actions base class
   */
  public function getActionsBaseClass()
  {
    return isset($this->params['actions_base_class']) ? $this->params['actions_base_class'] : 'sfActions';
  }

  /**
   * Gets the class name for current model.
   *
   * @return string
   */
  public function getModelClass()
  {
    return $this->modelClass;
  }

  /**
   * Gets the singular name for current model.
   *
   * @return string
   */
  public function getSingularName()
  {
    return isset($this->params['singular']) ? $this->params['singular'] : sfRestInflector::underscore($this->getModelClass());
  }

  /**
   * Gets the plural name for current model.
   *
   * @return string
   */
  public function getPluralName()
  {
    return isset($this->params['plural']) ? $this->params['plural'] : $this->getSingularName().'_list';
  }

  /**
   * Array export. Export array to formatted php code
   *
   * @param array $values
   * @return string $php
   */
  protected function arrayExport($values)
  {
    $php = var_export($values, true);
    $php = str_replace("\n", '', $php);
    $php = str_replace('array (  ', 'array(', $php);
    $php = str_replace(',)', ')', $php);
    $php = str_replace('  ', ' ', $php);
    return $php;
  }

  public function asPhp($variable)
  {
    return str_replace(array("\n", 'array ('), array('', 'array('), var_export($variable, true));
  }

  /**
   * Returns an array of relations.
   *
   * @return array An array of relations
   */
  public function getRelations()
  {
    return $this->table->getRelations();
  }

  /**
   * Returns an array of relations that represents a many to many relationship.
   *
   * @return array An array of relations
   */
  public function getManyToManyRelations()
  {
    $relations = array();
    foreach ($this->table->getRelations() as $relation)
    {
      if (
        Doctrine_Relation::MANY == $relation->getType()
        &&
        isset($relation['refTable'])
        &&
        (null === $this->getParentModel() || !Doctrine_Core::getTable($this->getParentModel())->hasRelation($relation->getAlias()))
      )
      {
        $relations[] = $relation;
      }
    }

    return $relations;
  }

  public function isTableManyToManyRelation($table, $alias)
  {
    if ('sfDoctrineRestGenerator' == get_class($table))
    {
      $table = $table->table;
    }

    if ($relation = $table->getRelation($alias))
    {
      return Doctrine_Relation::MANY == $relation->getType()
        &&
        isset($relation['refTable'])
        &&
        (null === $this->getParentModel($table) || !Doctrine_Core::getTable($this->getParentModel($table))->hasRelation($relation->getAlias()));
    }
    else
    {
       throw new Exception(sprintf('The relation "%s" is not defined.', $alias));
    }
  }

  public function isManyToManyRelation($alias)
  {
    if ($relation = $this->table->getRelation($alias))
    {
      return Doctrine_Relation::MANY == $relation->getType()
        &&
        isset($relation['refTable'])
        &&
        (null === $this->getParentModel() || !Doctrine_Core::getTable($this->getParentModel())->hasRelation($relation->getAlias()));
    }
    else
    {
       throw new Exception(sprintf('The relation "%s" is not defined.', $alias));
    }
  }

  /**
   * Get array of sfDoctrineColumn objects that exist on the current model but not its parent.
   *
   * @return array $columns
   */
  public function getColumns($object = null)
  {
    if (null === $object)
    {
      $object = $this;
    }

    if ('sfDoctrineRestGenerator' == get_class($object))
    {
      $table = $object->table;
    }
    else
    {
      $table = $object;
    }

    $parentModel = $this->getParentModel($object);
    $parentColumns = $parentModel ? array_keys(Doctrine_Core::getTable($parentModel)->getColumns()) : array();
    $columns = array();

    foreach (array_diff(array_keys($table->getColumns()), $parentColumns) as $name)
    {
      $columns[] = new sfDoctrineColumn($name, $table);
    }

    return $columns;
  }

  /**
   * Returns the name of the model class this model extends.
   *
   * @return string|null
   */
  public function getParentModel($object = null)
  {
    if (null === $object)
    {
      $object = $this;
    }

    if ('sfDoctrineRestGenerator' == get_class($object))
    {
      // find the first non-abstract parent
      $model = $object->params['model_class'];
    }
    else
    {
      $model = $object->getTableName();
    }

    $baseClasses = array(
      'Doctrine_Record',
      'sfDoctrineRecord',
    );

    $builderOptions = sfConfig::get('doctrine_model_builder_options', array());
    if (isset($builderOptions['baseClassName']))
    {
      $baseClasses[] = $builderOptions['baseClassName'];
    }

    while ($model = get_parent_class($model))
    {
      if (in_array($model, $baseClasses))
      {
        break;
      }

      $r = new ReflectionClass($model);
      if (!$r->isAbstract())
      {
        return $r->getName();
      }
    }
  }

  /**
   * Returns the primary keys.
   */
  public function getPrimaryKeys()
  {
    $primaryKey = array();

    foreach ($this->getColumns() as $name => $column)
    {
      if ($column->isPrimaryKey())
      {
        $primaryKey[] = $column->getName();
      }
    }

    return $primaryKey;
  }

  /**
   * Gets the i18n catalogue to use for user strings.
   *
   * @return string The i18n catalogue
   */
  public function getI18nCatalogue()
  {
    return isset($this->params['i18n_catalogue']) ? $this->params['i18n_catalogue'] : 'messages';
  }

  /**
   * Configures this generator.
   */
  public function configure() { }

  /**
   * Wraps a content for I18N.
   *
   * @param string $key The configuration key name
   *
   * @return string HTML code
   */
  public function getI18NString($key)
  {
    $value = $this->configuration->getValue($key, '', true);

    $parts = explode('.', $key);
    $context = $parts[0];

    // find %%xx%% strings
    preg_match_all('/%%([^%]+)%%/', $value, $matches, PREG_PATTERN_ORDER);
    $fields = array();
    foreach ($matches[1] as $name)
    {
      $fields[] = $name;
    }

    $vars = array();
    foreach ($this->configuration->getContextConfiguration($context, $fields) as $field)
    {
      $vars[] = '\'%%'.$field->getName().'%%\' => '.$this->renderField($field);
    }

    return sprintf("__('%s', array(%s), '%s')", $value, implode(', ', $vars), $this->getI18nCatalogue());
  }

  /**
   * Validates the basic structure of the parameters.
   *
   * @param array $params An array of parameters
   */
  protected function validateParameters($params)
  {
    foreach (array('model_class', 'moduleName') as $key)
    {
      if (!isset($params[$key]))
      {
        throw new sfParseException(sprintf('sfModelGenerator must have a "%s" parameter.', $key));
      }
    }

    if (!class_exists($params['model_class']))
    {
      throw new sfInitializationException(sprintf('Unable to generate a module for non-existent model "%s".', $params['model_class']));
    }

    $this->config = isset($params['config']) ? $params['config'] : array();

    unset($params['config']);
    $this->params = $params;
  }

  /**
   * Loads the configuration for this generated module.
   */
  protected function loadConfiguration()
  {
    try
    {
      $this->generatorManager->getConfiguration()->getGeneratorTemplate($this->getGeneratorClass(), $this->getTheme(), '../parts/configuration.php');
    }
    catch (sfException $e)
    {
      return null;
    }

    $config = $this->getGeneratorManager()->getConfiguration();
    if (!$config instanceof sfApplicationConfiguration)
    {
      throw new LogicException('The sfModelGenerator can only operates with an application configuration.');
    }

    $basePath = $this->getGeneratedModuleName().'/lib/Base'.ucfirst($this->getModuleName()).'GeneratorConfiguration.class.php';
    $this->getGeneratorManager()->save($basePath, $this->evalTemplate('../parts/configuration.php'));

    require_once $this->getGeneratorManager()->getBasePath().'/'.$basePath;

    $class = 'Base'.ucfirst($this->getModuleName()).'GeneratorConfiguration';
    foreach ($config->getLibDirs($this->getModuleName()) as $dir)
    {
      if (!is_file($configuration = $dir.'/'.$this->getModuleName().'GeneratorConfiguration.class.php'))
      {
        continue;
      }

      require_once $configuration;
      $class = $this->getModuleName().'GeneratorConfiguration';
      break;
    }

    // validate configuration
    foreach ($this->config as $context => $value)
    {
      if (!$value)
      {
        continue;
      }

      throw new InvalidArgumentException(sprintf('Your generator configuration contains some errors for the "%s" context. The following configuration cannot be parsed: %s.', $context, $this->asPhp($value)));
    }

    return new $class();
  }

  public function escapeString($string)
  {
    return str_replace("'", "\\'", $string);
  }

  /**
   * Returns a sfValidator class name for a given column.
   *
   * @param sfDoctrineColumn $column
   * @return string    The name of a subclass of sfValidator
   */
  public function getCreateValidatorClassForColumn($column)
  {
    $class = $this->getValidatorClassForColumn($column);

    if ($column->isPrimaryKey())
    {
      $class = 'sfValidatorRegex';
    }

    return $class;
  }

  /**
   * Returns the default configuration for fields.
   *
   * @return array An array of default configuration for all fields
   */
  public function getDefaultFieldsConfiguration()
  {
    $fields = array();
    $names = array();

    foreach ($this->getColumns() as $name => $column)
    {
      $name = $column->getName();
      $names[] = $name;
      $fields[$name] = isset($this->config['default']['fields'][$name]) ? $this->config['default']['fields'][$name] : array();
    }

    foreach ($this->getManyToManyTables() as $tables)
    {
      $name = sfInflector::underscore($tables['alias']).'_list';
      $names[] = $name;
      $fields[$name] = isset($this->config['default']['fields'][$name]) ? $this->config['default']['fields'][$name] : array();
    }

    if (isset($this->config['default']['fields']))
    {
      foreach ($this->config['default']['fields'] as $name => $params)
      {
        if (in_array($name, $names))
        {
          continue;
        }

        $fields[$name] = is_array($params) ? $params : array();
      }
    }

    unset($this->config['default']['fields']);
    return $fields;
  }

  /**
   * Returns the default configuration for fields.
   *
   * @return array An array of default configuration for all fields
   */
  public function getFiltersConfiguration()
  {
    $fields = array();
    $names = array();

    foreach ($this->getColumns() as $name => $column)
    {
      $name = $column->getName();
      $names[] = $name;
      $fields[$name] = isset($this->config['get']['filters'][$name]) ? $this->config['get']['filters'][$name] : array();
    }

    foreach ($this->getManyToManyTables() as $tables)
    {
      $name = sfInflector::underscore($tables['alias']).'_list';
      $names[] = $name;
      $fields[$name] = isset($this->config['get']['filters'][$name]) ? $this->config['get']['filters'][$name] : array();
    }

    if (isset($this->config['get']['filters']))
    {
      foreach ($this->config['get']['filters'] as $name => $params)
      {
        if (in_array($name, $names))
        {
          continue;
        }

        $fields[$name] = is_array($params) ? $params : array();
      }
    }

    unset($this->config['get']['filters']);
    return $fields;
  }

  /**
   * Returns the configuration for fields in a given context.
   *
   * @param  string $context The Context
   *
   * @return array An array of configuration for all the fields in a given context
   */
  public function getFieldsConfiguration($context)
  {
    $fields = array();
    $names = array();

    foreach ($this->getColumns() as $name => $column)
    {
      $name = $column->getName();
      $names[] = $name;
      $fields[$name] = isset($this->config[$context]['fields'][$name]) ? $this->config[$context]['fields'][$name] : array();
    }

    foreach ($this->getManyToManyTables() as $tables)
    {
      $name = sfInflector::underscore($tables['alias']).'_list';
      $names[] = $name;
      $fields[$name] = isset($this->config[$context]['fields'][$name]) ? $this->config[$context]['fields'][$name] : array();
    }

    if (isset($this->config[$context]['fields']))
    {
      foreach ($this->config[$context]['fields'] as $name => $params)
      {
        if (in_array($name, $names))
        {
          continue;
        }

        $fields[$name] = is_array($params) ? $params : array();
      }
    }

    unset($this->config[$context]['fields']);
    return $fields;
  }

  /**
   * Returns a sfValidator class name for a given column.
   *
   * @param sfDoctrineColumn $column
   * @return string    The name of a subclass of sfValidator
   */
  public function getIndexValidatorClassForColumn($column)
  {
    $filters = $this->configuration->getFilters();
    $class = $this->getValidatorClassForColumn($column);

    if ($column->isPrimaryKey())
    {
      $class = 'sfValidatorRegex';
    }
    elseif ($column->isForeignKey())
    {
      $class = 'sfValidatorInteger';
    }

    if ('sfValidatorInteger' == $class
      && isset($filters[$column->getName()])
      && isset($filters[$column->getName()]['multiple'])
      && $filters[$column->getName()]['multiple'])
    {
      $class = 'sfValidatorRegex';
    }

    return $class;
  }

  /**
   * Returns an array of tables that represents a many to many relationship.
   *
   * A table is considered to be a m2m table if it has 2 foreign keys that are also primary keys.
   *
   * @return array An array of tables.
   */
  public function getManyToManyTables()
  {
    $relations = array();
    foreach ($this->table->getRelations() as $relation)
    {
      if ($relation->getType() === Doctrine_Relation::MANY && isset($relation['refTable']))
      {
        $relations[] = $relation;
      }
    }
    return $relations;
  }

  /**
   * Returns a sfValidator class name for a given column.
   *
   * @param sfDoctrineColumn $column
   * @return string    The name of a subclass of sfValidator
   */
  public function getValidatorClassForColumn($column)
  {
    switch ($column->getDoctrineType())
    {
      case 'boolean':
        $validatorSubclass = 'Boolean';
        break;
      case 'string':
        if ($column->getDefinitionKey('email'))
        {
          $validatorSubclass = 'Email';
        }
        else if ($column->getDefinitionKey('regexp'))
        {
          $validatorSubclass = 'Regex';
        }
        else
        {
          $validatorSubclass = 'String';
        }
        break;
      case 'clob':
      case 'blob':
        $validatorSubclass = 'String';
        break;
      case 'float':
      case 'decimal':
        $validatorSubclass = 'Number';
        break;
      case 'integer':
        $validatorSubclass = 'Integer';
        break;
      case 'date':
        $validatorSubclass = 'Date';
        break;
      case 'time':
        $validatorSubclass = 'Time';
        break;
      case 'timestamp':
        $validatorSubclass = 'DateTime';
        break;
      case 'enum':
        $validatorSubclass = 'Choice';
        break;
      default:
        $validatorSubclass = 'Pass';
    }

    if ($column->isPrimaryKey() || $column->isForeignKey())
    {
      $validatorSubclass = 'DoctrineChoice';
    }

    return sprintf('sfValidator%s', $validatorSubclass);
  }

  /**
   * Returns a PHP string representing options to pass to a validator for a given column.
   *
   * @param sfDoctrineColumn $column
   * @return string    The options to pass to the validator as a PHP string
   */
  public function getCreateValidatorOptionsForColumn($column, $model = null)
  {
    if (null === $model)
    {
      $model = '$this->model';
    }

    $options = array();

    if ($column->isForeignKey())
    {
      $options[] = sprintf('\'model\' => Doctrine_Core::getTable('.$model.')->getRelation(\'%s\')->getAlias()', $column->getRelationKey('alias'));
    }
    else if ($column->isPrimaryKey())
    {
      $options[] = sprintf('\'pattern\' => \'(.+)\', \'must_match\' => false');
    }
    else
    {
      switch ($column->getDoctrineType())
      {
        case 'string':
          if ($column['length'])
          {
            $options[] = sprintf('\'max_length\' => %s', $column['length']);
          }
          if (isset($column['minlength']))
          {
            $options[] = sprintf('\'min_length\' => %s', $column['minlength']);
          }
          if (isset($column['regexp']))
          {
            $options[] = sprintf('\'pattern\' => \'%s\'', $column['regexp']);
          }
          break;
        case 'enum':
          $values = array_combine($column['values'], $column['values']);
          $options[] = "'choices' => " . str_replace("\n", '', $this->arrayExport($values));
          break;
      }
    }

    // If notnull = false, is a primary or the column has a default value then
    // make the widget not required
    if (!$column->isNotNull() || $column->isPrimaryKey() || $column->hasDefinitionKey('default'))
    {
      $options[] = '\'required\' => false';
    }

    return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
  }


  /**
   * Based on a table's model, generates a PHP string representing an indexed
   * array of validators in a smiliar arrangement like the relationships
   * hierarchy.
   */
  protected function getCreateValidatorsArray($table, $level = 0)
  {
    if ($level > 1)
    {
      // do not generate validators for more than two levels
      return null;
    }

    if ('sfDoctrineRestGenerator' == get_class($table))
    {
      $table = $table->table;
    }

    $model_name = '\''.$table->getClassnameToReturn().'\'';
    $spaces = str_repeat('  ', $level + 3);
    $validators = "array(\n";

    foreach ($this->getColumns($table) as $column)
    {
      if (!$column->isPrimaryKey())
      {
        $validators .= $spaces.'\''.$column->getFieldName().'\' => new '.$this->getCreateValidatorClassForColumn($column).'('.$this->getCreateValidatorOptionsForColumn($column, $model_name)."),\n";
      }
    }

    foreach ($table->getRelations() as $alias => $relation)
    {
      if ($this->isTableManyToManyRelation($table, $alias))
      {
        // to do later
      }
      else
      {
        $sub_validators = $this->getCreateValidatorsArray($relation->getTable(), $level + 1);

        if (null != $sub_validators)
        {
          $validators .= $spaces.'\''.$alias.'\' => '.$sub_validators.",\n";
        }
      }
    }

    return $validators.str_repeat('  ', $level + 2).')';
  }


  /**
   * Returns a PHP string representing options to pass to a validator for a given column.
   *
   * @param sfDoctrineColumn $column
   * @return string    The options to pass to the validator as a PHP string
   */
  public function getIndexValidatorOptionsForColumn($column)
  {
    $filters = $this->configuration->getFilters();
    $options = array();

    if ($column->isPrimaryKey())
    {
      $options[] = '\'pattern\' => \'~^[0-9]+(\\'.$this->configuration->getSeparator().'[0-9]+)*$~i\', \'must_match\' => true';
    }
    else
    {
      switch ($column->getDoctrineType())
      {
        case 'string':
          if ($column['length'])
          {
            $options[] = sprintf('\'max_length\' => %s', $column['length']);
          }
          if (isset($column['minlength']))
          {
            $options[] = sprintf('\'min_length\' => %s', $column['minlength']);
          }
          if (isset($column['regexp']))
          {
            $options[] = sprintf('\'pattern\' => \'%s\'', $column['regexp']);
          }
          break;
        case 'enum':
          $values = array_combine($column['values'], $column['values']);
          $options[] = "'choices' => " . str_replace("\n", '', $this->arrayExport($values));
          break;
      }
    }

    if ('sfValidatorRegex' == $this->getIndexValidatorClassForColumn($column)
        && isset($filters[$column->getName()]['multiple'])
        && $filters[$column->getName()]['multiple'])
    {
      $options[] = '\'pattern\' => \'~^[0-9]+(\\'.$this->configuration->getSeparator().'[0-9]+)*$~i\', \'must_match\' => true';
    }

    $options[] = '\'required\' => false';
    return sprintf('array(%s)', implode(', ', $options));
  }

  /**
   * Returns a PHP string representing options to pass to a validator for a given column.
   *
   * @param sfDoctrineColumn $column
   * @return string    The options to pass to the validator as a PHP string
   */
  public function getValidatorOptionsForColumn($column)
  {
    $options = array();

    if ($column->isForeignKey())
    {
      $options[] = sprintf('\'model\' => $this->getRelatedModelName(\'%s\')', $column->getRelationKey('alias'));
    }
    else if ($column->isPrimaryKey())
    {
      $options[] = sprintf('\'model\' => $this->model, \'column\' => \'%s\'', $column->getName());
    }
    else
    {
      switch ($column->getDoctrineType())
      {
        case 'string':
          if ($column['length'])
          {
            $options[] = sprintf('\'max_length\' => %s', $column['length']);
          }
          if (isset($column['minlength']))
          {
            $options[] = sprintf('\'min_length\' => %s', $column['minlength']);
          }
          if (isset($column['regexp']))
          {
            $options[] = sprintf('\'pattern\' => \'%s\'', $column['regexp']);
          }
          break;
        case 'enum':
          $values = array_combine($column['values'], $column['values']);
          $options[] = "'choices' => " . str_replace("\n", '', $this->arrayExport($values));
          break;
      }
    }

    // If notnull = false, is a primary or the column has a default value then
    // make the widget not required
    if (!$column->isNotNull() || $column->isPrimaryKey() || $column->hasDefinitionKey('default'))
    {
      $options[] = '\'required\' => false';
    }

    return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
  }
}
