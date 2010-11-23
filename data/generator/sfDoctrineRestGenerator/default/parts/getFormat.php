  protected function getFormat()
  {
    if (!isset($this->format))
    {
      $format = $this->getRequest()->getParameter('sf_format', '<?php echo $this->configuration->getValue('get.default_format') ?>');

      if (!in_array($format, <?php var_export($this->configuration->getValue('default.formats_enabled', array('json', 'xml', 'yaml'))) ?>))
      {
<?php if ($this->configuration->getValue('default.formats_strict')): ?>
        throw new sfException(sprintf('This API does not support the format %s', $format));
<?php else: ?>
        $format = '<?php echo $this->configuration->getValue('get.default_format') ?>';
<?php endif; ?>
      }

      $this->format = $format;
    }

    return $this->format;
  }
