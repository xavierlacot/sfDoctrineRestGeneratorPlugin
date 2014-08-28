  /**
   * Create the query for selecting objects, eventually along with related
   * objects
   *
   * @param   array   $params  an array of criterions for the selection
   */
  public function query($params)
  {
    $q = Doctrine_Query::create()
<?php
$display = $this->configuration->getValue('get.display');
$embed_relations = $this->configuration->getValue('get.embed_relations');

$fields = $display;
foreach ($embed_relations as $relation_name)
{
  $fields[] = $relation_name.'.*';
}
?>
<?php if (count($display) > 0): ?>
<?php $display = implode(', ', $fields); ?>
      ->select('<?php echo $display ?>')
<?php endif; ?>

      ->from($this->model.' '.$this->model)
<?php foreach ($embed_relations as $embed_relation): ?>
<?php if (!$this->isManyToManyRelation($embed_relation)): ?>

      ->leftJoin($this->model.'.<?php echo $embed_relation ?> <?php echo $embed_relation ?>')<?php endif; ?><?php endforeach; ?>;

    $this->queryPagination($q, $params);
<?php $sort_custom = $this->configuration->getValue('get.sort_custom'); ?>
<?php $sort_default = $this->configuration->getValue('get.sort_default'); ?>
<?php if ($sort_default && count($sort_default) == 2): ?>
    $sort = '<?php echo $sort_default[0] ?> <?php echo $sort_default[1] ?>';
<?php endif; ?>
<?php if ($sort_custom): ?>
    if (isset($params['sort_by']))
    {
      $sort = $params['sort_by'];
      unset($params['sort_by']);

      if (isset($params['sort_order']))
      {
        $sort .= ' '.$params['sort_order'];
        unset($params['sort_order']);
      }
    }
<?php endif; ?>

    if (isset($sort))
    {
      $q->orderBy($sort);
    }

<?php $primaryKeys = $this->getPrimaryKeys(); ?>
<?php foreach ($primaryKeys as $primaryKey): ?>
    if (isset($params['<?php echo $primaryKey ?>']))
    {
      $values = explode('<?php echo $this->configuration->getValue('default.separator', ',') ?>', $params['<?php echo $primaryKey ?>']);

      if (count($values) == 1)
      {
        $q->andWhere($this->model.'.<?php echo $primaryKey ?> = ?', $values[0]);
      }
      else
      {
        $q->whereIn($this->model.'.<?php echo $primaryKey ?>', $values);
      }

      unset($params['<?php echo $primaryKey ?>']);
    }

<?php endforeach; ?>
<?php $filters = $this->configuration->getFilters() ?>
<?php foreach ($filters as $name => $filter): ?>
<?php if (isset($filters[$name]['multiple']) && $filters[$name]['multiple']): ?>
    if (isset($params['<?php echo $name ?>']))
    {
      $values = explode('<?php echo $this->configuration->getValue('default.separator', ',') ?>', $params['<?php echo $name ?>']);

      if (count($values) == 1)
      {
        $q->andWhere($this->model.'.<?php echo $name ?> = ?', $values[0]);
      }
      else
      {
        $q->whereIn($this->model.'.<?php echo $name ?>', $values);
      }

      unset($params['<?php echo $name ?>']);
    }
<?php endif; ?>
<?php endforeach; ?>
    foreach ($params as $name => $value)
    {
      $q->andWhere($this->model.'.'.$name.' = ?', $value);
    }

    return $q;
  }
