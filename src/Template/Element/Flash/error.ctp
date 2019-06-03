<?php
/**
 * @var \ARC\ProductConfigurator\View\AppView $this
 * @var string $message
 * @var array $params
 */

if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>

<div class="alert alert-accent alert-dismissible fade show mb-0" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>

    <i class="fa fa-info mx-2"></i>
    <?= $message ?>
</div>
