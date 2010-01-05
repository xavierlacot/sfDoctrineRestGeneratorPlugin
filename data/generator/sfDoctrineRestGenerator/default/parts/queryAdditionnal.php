<?php $embed_relations = $this->configuration->getValue('get.embed_relations'); ?>
<?php foreach ($embed_relations as $embed_relation): ?>
<?php if ($this->isManyToManyRelation($embed_relation)): ?>

  /**
   * Loads related obect to the currently selected objects
   */
  public function embedManyToMany<?php echo $embed_relation ?>()
  {
    $this->objects->loadRelated('<?php echo $embed_relation ?>');
  }
<?php endif; ?><?php endforeach; ?>
<?php $object_additional_fields = $this->configuration->getValue('get.object_additional_fields'); ?>
<?php foreach ($object_additional_fields as $field): ?>

  public function embedAdditional<?php echo $field ?>($item, $params)
  {
  }
<?php endforeach; ?><?php $global_additional_fields = $this->configuration->getValue('get.global_additional_fields'); ?>
<?php foreach ($global_additional_fields as $field): ?>

  public function embedAdditional<?php echo $field ?>($items, $params)
  {
  }
<?php endforeach; ?>