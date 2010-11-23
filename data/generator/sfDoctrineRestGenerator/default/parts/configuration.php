[?php

/**
 * <?php echo $this->getModuleName() ?> module configuration.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: configuration.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class Base<?php echo ucfirst($this->getModuleName()) ?>GeneratorConfiguration extends sfDoctrineRestGeneratorConfiguration
{
  public function getAdditionalParams()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['additional_params']) ? $this->config['get']['additional_params'] : array()) ?>;
<?php unset($this->config['get']['additional_params']) ?>
  }

  public function getDefaultFormat()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['default_format']) ? $this->config['get']['default_format'] : 'json') ?>;
<?php unset($this->config['get']['default_format']) ?>
  }

  public function getDisplay()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['display']) ? $this->config['get']['display'] : array()) ?>;
<?php unset($this->config['get']['display']) ?>
  }

  public function getEmbedRelations()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['embed_relations']) ? $this->config['get']['embed_relations'] : array()) ?>;
<?php unset($this->config['get']['embed_relations']) ?>
  }

  public function getEmbeddedRelationsHide()
  {
    $embedded_relations_hide = <?php echo $this->asPhp(isset($this->config['get']['embedded_relations_hide']) ? $this->config['get']['embedded_relations_hide'] : array()) ?>;

    foreach ($embedded_relations_hide as $relation_name => $hidden_fields)
    {
      $embedded_relations_hide[$relation_name] = array_flip($hidden_fields);
    }

    return $embedded_relations_hide;
<?php unset($this->config['get']['embedded_relations_hide']) ?>
  }

  public function getFormatsEnabled()
  {
    return <?php echo $this->asPhp(isset($this->config['default']['formats_enabled']) ? $this->config['default']['formats_enabled'] : array('json', 'xml', 'yaml')) ?>;
<?php unset($this->config['default']['formats_enabled']) ?>
  }

  public function getFormatsStrict()
  {
    return <?php echo $this->asPhp(isset($this->config['default']['formats_strict']) ? $this->config['default']['formats_strict'] : true) ?>;
<?php unset($this->config['default']['formats_strict']) ?>
  }

<?php include dirname(__FILE__).'/fieldsConfiguration.php' ?>

  public function getGlobalAdditionalFields()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['global_additional_fields']) ? $this->config['get']['global_additional_fields'] : array()) ?>;
<?php unset($this->config['get']['global_additional_fields']) ?>
  }

  public function getHide()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['hide']) ? $this->config['get']['hide'] : array()) ?>;
<?php unset($this->config['get']['hide']) ?>
  }

  public function getMaxItems()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['max_items']) ? $this->config['get']['max_items'] : 0) ?>;
<?php unset($this->config['get']['max_items']) ?>
  }

  public function getObjectAdditionalFields()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['object_additional_fields']) ? $this->config['get']['object_additional_fields'] : array()) ?>;
<?php unset($this->config['get']['object_additional_fields']) ?>
  }

  public function getSeparator()
  {
    return <?php echo $this->asPhp(isset($this->config['default']['separator']) ? $this->config['default']['separator'] : ',') ?>;
<?php unset($this->config['default']['separator']) ?>
  }

<?php include dirname(__FILE__).'/paginationConfiguration.php' ?>

<?php include dirname(__FILE__).'/sortConfiguration.php' ?>
}
