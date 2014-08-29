  /**
   * Add pagination to a specified query object
   *
   * @param Doctrine_Query $query  The query to add pagination to
   * @param array &$params  The parameters
   * @return Doctrine_Query  The query, amended with pagination
   */
  protected function queryPagination(Doctrine_Query $query, array &$params)
  {
<?php
$max_items = $this->configuration->getValue('get.max_items');
if ($max_items > 0):
?>
    $limit = <?php echo $max_items; ?>;

<?php endif; ?>
<?php
$pagination_custom_page_size = $this->configuration->getValue('get.pagination_custom_page_size');
$pagination_enabled = $this->configuration->getValue('get.pagination_enabled');
$pagination_page_size = $this->configuration->getValue('get.pagination_page_size'); ?>
<?php if ($pagination_enabled): ?>
    if (!isset($params['page']))
    {
      $params['page'] = 1;
    }

    $page_size = <?php echo $pagination_page_size; ?>;
<?php if ($pagination_custom_page_size): ?>

    if (isset($params['page_size']))
    {
      $page_size = $params['page_size'];
      unset($params['page_size']);
    }

<?php endif; ?>
<?php if ($max_items > 0): ?>
    $limit = min($page_size, $limit);
    $page_size = $limit;
<?php else: ?>
    $limit = $page_size;
<?php endif; ?>
    $q->offset(($params['page'] - 1) * $page_size);
    unset($params['page']);
<?php endif; ?>
<?php if ($max_items > 0 || $pagination_enabled): ?>
    $q->limit($limit);
<?php endif; ?>

    return $q;
  }
