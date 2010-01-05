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

  public function getGlobalAdditionalFields()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['global_additional_fields']) ? $this->config['get']['global_additional_fields'] : array()) ?>;
<?php unset($this->config['get']['global_additional_fields']) ?>
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

<?php include dirname(__FILE__).'/paginationConfiguration.php' ?>

<?php include dirname(__FILE__).'/sortConfiguration.php' ?>
}
