  public function getPaginationCustomPageSize()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['pagination_custom_page_size']) ? $this->config['get']['pagination_custom_page_size'] : false) ?>;
<?php unset($this->config['get']['pagination_custom_page_size']) ?>
  }

  public function getPaginationEnabled()
  {
    return <?php echo $this->asPhp(isset($this->config['get']['pagination_enabled']) ? $this->config['get']['pagination_enabled'] : false) ?>;
<?php unset($this->config['get']['pagination_enabled']) ?>
  }

  public function getPaginationPageSize()
  {
    return <?php echo isset($this->config['get']['pagination_page_size']) ? (integer) $this->config['get']['pagination_page_size'] : 100 ?>;
<?php unset($this->config['get']['pagination_page_size']) ?>
  }