<?php

class sfDoctrineRestGenerateModuleTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('module', sfCommandArgument::REQUIRED, 'The module name'),
      new sfCommandArgument('model', sfCommandArgument::REQUIRED, 'The model class'),
    ));

    $this->namespace = 'doctrine';
    $this->name = 'generate-rest-module';
    $this->briefDescription = 'Generates a REST module';

    $this->detailedDescription = <<<EOF
The [doctrine:generate-rest-module|INFO] task generates a Doctrine module:

  [./symfony doctrine:generate-rest-module frontend article Article|INFO]

The task creates [%article%|COMMENT] module in the [%frontend%|COMMENT]
application for the [%Article%|COMMENT] model.

The task creates routes for you in the application [routing.yml|COMMENT].
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $r = new ReflectionClass($arguments['model']);

    if (!$r->isSubclassOf('Doctrine_Record'))
    {
      throw new sfCommandException(sprintf('"%s" is not a Doctrine class.', $arguments['model']));
    }

    // create a route
    $model = $arguments['model'];
    $module = $arguments['module'];

    $routing = sfConfig::get('sf_app_config_dir').'/routing.yml';
    $content = file_get_contents($routing);
    $routesArray = sfYaml::load($content);

    if (!isset($routesArray[$name]))
    {
      $databaseManager = new sfDatabaseManager($this->configuration);
      $primaryKey = Doctrine_Core::getTable($model)->getIdentifier();
      $content = sprintf(<<<EOF
%s:
  class:   sfObjectRouteCollection
  options:
    model:   %s
    actions: [ create, delete, list, update ]
    module:  %s
    column:  %s
    format:  xml


EOF
      , $module, $model, $module, $primaryKey).$content;

      $this->logSection('file+', $routing);
      file_put_contents($routing, $content);
    }

    return $this->generate($module, $model);
  }

  protected function generate($module, $model)
  {
    $moduleDir = sfConfig::get('sf_app_module_dir').'/'.$module;

    // create basic application structure
    $finder = sfFinder::type('any')->discard('.sf');
    $dirs = $this->configuration->getGeneratorSkeletonDirs('sfDoctrineRestGenerator', $options['theme']);

    foreach ($dirs as $dir)
    {
      if (is_dir($dir))
      {
        $this->getFilesystem()->mirror($dir, $moduleDir, $finder);
        break;
      }
    }

    // move configuration file
    if (file_exists($config = $moduleDir.'/lib/configuration.php'))
    {
      if (file_exists($target = $moduleDir.'/lib/'.$module.'GeneratorConfiguration.class.php'))
      {
        $this->getFilesystem()->remove($config);
      }
      else
      {
        $this->getFilesystem()->rename($config, $target);
      }
    }

    $databaseManager = new sfDatabaseManager($this->configuration);

    $properties = parse_ini_file(sfConfig::get('sf_config_dir').'/properties.ini', true);

    $this->constants = array(
      'PROJECT_NAME'   => isset($properties['symfony']['name']) ? $properties['symfony']['name'] : 'symfony',
      'APP_NAME'       => $arguments['application'],
      'MODULE_NAME'    => $module,
      'UC_MODULE_NAME' => ucfirst($module),
      'MODEL_CLASS'    => $model,
      'AUTHOR_NAME'    => isset($properties['symfony']['author']) ? $properties['symfony']['author'] : 'Your name here',
    );

    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php', '*.yml');
    $this->constants['CONFIG'] = sprintf(<<<EOF
    model_class:           %s

EOF
    ,
      $model
    );
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '##', '##', $this->constants);
  }
}