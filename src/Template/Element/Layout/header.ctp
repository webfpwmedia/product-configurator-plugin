<?php
/**
 * @var App\View\AppView $this
 */

?>

<div class="page-header py-4">
    <span class="text-uppercase page-subtitle">
        <?= h(__($this->fetch('subtitle'))) ?>
    </span>

    <h3 class="page-title">
        <?= h(__($this->fetch('title'))) ?>
    </h3>
</div>
