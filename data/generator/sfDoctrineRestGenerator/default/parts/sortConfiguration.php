  public function getSortCustom()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['sort_custom']) ? $this->config['get']['sort_custom'] : false) ?>;
<?php unset($this->config['get']['sort_custom']) ?>
  }

  public function getSortDefault()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['sort_default']) ? $this->config['get']['sort_default'] : array()) ?>;
<?php unset($this->config['get']['sort_default']) ?>
  }