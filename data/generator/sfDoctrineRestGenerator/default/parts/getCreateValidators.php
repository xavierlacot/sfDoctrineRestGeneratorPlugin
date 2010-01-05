  /**
   * Returns the list of validators for a create request.
   * @return  array
   */
  public function getCreateValidators()
  {
  	$validators = array();
<?php foreach ($this->getColumns() as $column): ?>
<?php if (!$column->isPrimaryKey()): ?>
    $validators['<?php echo $column->getFieldName() ?>'] = new <?php echo $this->getCreateValidatorClassForColumn($column) ?>(<?php echo $this->getCreateValidatorOptionsForColumn($column) ?>);
<?php endif; ?>
<?php endforeach; ?>
<?php foreach ($this->getManyToManyRelations() as $relation): ?>
    $validators['<?php echo $this->underscore($relation['alias']) ?>s'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => '<?php echo $relation['table']->getOption('name') ?>', 'required' => false));
<?php endforeach; ?>

    return $validators;
  }
