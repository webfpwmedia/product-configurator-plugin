<?php
/**
 * @var \ARC\ProductConfigurator\View\AppView $this
 * @var \ARC\ProductConfigurator\Model\Entity\Image $image
 */

$this
    ->assign('title', h($image->name))
    ->assign('subtitle', __('Edit Image'));
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-small mb-4">
            <div class="card-body">
                <?= $this->Form->create($image) ?>

                <?= $this->Form->control('name', [
                    'readonly' => true,
                    'templateVars' => [
                        'help' => '<p class="text-muted small">' . __('Amazon S3 object key.') . '</p>',
                    ],
                ]) ?>

                <?= $this->Form->control('mask', [
                    'label' => __('Index'),
                ]) ?>

                <?= $this->Form->control('position', [
                    'options' => [
                        'front' => __('Front'),
                        'back' => __('Back'),
                    ]
                ]) ?>

                <?= $this->Form->control('layer') ?>

                <?= $this->Form->submit(__('Save')) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <?= $this->Html->image($image->name) ?>
    </div>
</div>
