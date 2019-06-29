<?php
/**
 * @var AppView $this
 * @var Image $image
 */

use ARC\ProductConfigurator\View\AppView;
use ARC\ProductConfigurator\Model\Entity\Image;
use Cake\Core\Configure;

$this
    ->assign('title', h($image->name))
    ->assign('subtitle', __('Edit Image'));
?>

<p class="mb-4">
    <?= $this->Html->link('<i class="material-icons">arrow_back</i> ' . __('Image Index'),
        ['action' => 'index'],
        ['escape' => false]) ?>
</p>

<div class="row">
    <div class="col-lg-6">
        <div class="card card-small mb-4">
            <div class="card-body">
                <?= $this->Form->create($image, [
                    'templates' => [
                         'submitContainer' => '{{content}}'
                    ]
                ]) ?>

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
                <?= $this->Form->postLink(__('Delete'), [
                    'action' => 'delete', $image->id
                ], [
                    'class' => 'btn btn-danger',
                    'confirm' => __('Are you sure you want to remove this image from the index?'),
                    'block' => 'imagePostLink',
                ]) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6 text-center">
        <a href="<?= Configure::read('ARC.ProductConfigurator.imageBaseUrl') . $image->name ?>" target="_blank">
            <?= $this->Html->image($image->name, ['size' => 'md', 'class' => 'img-fluid']) ?>
        </a>
    </div>
</div>

<?= $this->fetch('imagePostLink') ?>
