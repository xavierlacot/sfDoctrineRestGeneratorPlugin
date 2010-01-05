
  public function getFieldsDefault()
  {
    return array(
<?php foreach ($this->getDefaultFieldsConfiguration() as $name => $params): ?>
      '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
    );
  }

<?php foreach (array('get', 'create', 'update') as $context): ?>
  public function getFields<?php echo ucfirst($context) ?>()
  {
    return array(
<?php foreach ($this->getFieldsConfiguration($context) as $name => $params): ?>
      '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
    );
  }
<?php endforeach; ?>

  public function getFilters()
  {
    return array(
<?php foreach ($this->getFiltersConfiguration() as $name => $params): ?>
      '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
    );
  }
