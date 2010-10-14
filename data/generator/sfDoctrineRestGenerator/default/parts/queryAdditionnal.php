<?php
$embed_relations = $this->configuration->getValue('get.embed_relations');
$pk = current($this->getPrimaryKeys());
?>
<?php foreach ($embed_relations as $embed_relation): ?>
<?php if ($this->isManyToManyRelation($embed_relation)): ?>

  /**
   * Loads "<?php echo $embed_relation ?>" objects related to the currently
   * selected objects
   */
  public function embedManyToMany<?php echo $embed_relation ?>()
  {
    // get the list of the object's ids
    // we assume there's only one primary key
    $list = array();

    foreach ($this->objects as $object)
    {
      $value = $object['<?php echo $pk ?>'];

      if ($value !== null)
      {
        $list[] = $value;
      }
    }

    if (0 == count($list))
    {
      return;
    }

    // retrieve the objects related to these primary keys
    $relation_name = '<?php echo $embed_relation ?>';
    $rel = Doctrine::getTable($this->model)->getRelation($relation_name);
    $query = $rel->getTable()->createQuery();
    $dql = $rel->getRelationDql(count($list), 'collection');
    $collection = $query->query($dql, $list, Doctrine_Core::HYDRATE_ARRAY);
    $local_key = $rel->getLocal();
    $related_class = $rel->getClass();
    $related = array();

    // and attach them to the right objects
    foreach ($collection as $relation)
    {
      if (!isset($related[$relation[$local_key]]))
      {
        $related[$relation[$local_key]] = array();
      }

      $related[$relation[$local_key]][] = $relation[$related_class];
    }

    foreach ($this->objects as $key => $object)
    {
      if ($object['<?php echo $pk ?>'] && isset($related[$object['<?php echo $pk ?>']]))
      {
        $this->objects[$key][$relation_name] = $related[$object['<?php echo $pk ?>']];
      }
    }
  }
<?php endif; ?><?php endforeach; ?>
<?php $object_additional_fields = $this->configuration->getValue('get.object_additional_fields'); ?>
<?php foreach ($object_additional_fields as $field): ?>

  /**
   * Allows to embed an additional field "<?php echo $field ?>" in each item
   * of the resultset
   *
   * @param   $item    the index of an item in the resultset
   * @param   $params  the filtered params of the request
   */
//  public function embedAdditional<?php echo $field ?>($item, $params)
//  {
//    $array = $this->objects[$item];
//    put your code here, in order to change the content of $array
//    ...
//    $this->objects[$item] = $array;
//  }
<?php endforeach; ?><?php $global_additional_fields = $this->configuration->getValue('get.global_additional_fields'); ?>
<?php foreach ($global_additional_fields as $field): ?>

  /**
   * Allows to embed an additional field "<?php echo $field ?>" at the root
   * level of the resultset
   *
   * @param   $items   the whole resultset
   * @param   $params  the filtered params of the request
   */
//  public function embedGlobalAdditional<?php echo $field ?>($params)
//  {
//    $array = $this->objects;
//    put your code here, in order to change the content of $array
//    ...
//    $this->objects = $array;
//  }
<?php endforeach; ?>