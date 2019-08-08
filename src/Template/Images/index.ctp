<?php
/**
 * @var \ARC\ProductConfigurator\View\AppView $this
 * @var Cake\ORM\Query $images
 */

$this
    ->assign('title', 'Image Index')
    ->assign('subtitle', __('Configurator'));

?>

<div class="row mb-4">
    <div class="col">
        <h6 class="m-0">
            <?= __('{0} images in all configurators.', $images->count()) ?>
        </h6>
    </div>

    <div class="col text-right">
        <?= $this->Html->link(__('S3 Image Inventory'), ['action' => 'listBucket']) ?>
    </div>
</div>

<div class="row">
    <?php foreach ($images as $image): ?>
        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
            <div class="card mb-3">
                <?= $this->Html->image($image->name, [
                    'size' => 'md',
                    'class' => 'img-fluid card-img-top',
                    'url' => ['action' => 'edit', $image->id],
                ]) ?>

                <div class="card-body p-0 px-3 pb-3">
                    <p class="lead mb-0">
                        <?= h($image->mask) ?>
                    </p>

                    <span class="badge badge-outline-secondary small text-uppercase mb-2">
                        <?= __('{0} Layer {1}', h($image->position), $image->layer) ?>
                    </span>

                    <p class="small text-muted"><?= h($image->name) ?></p>

                    <?= $this->Html->link(__('Edit'), [
                        'action' => 'edit',
                        $image->id
                    ], [
                        'class' => 'btn btn-primary btn-block',
                    ]) ?>
                </div>

                <div class="card-footer">
                    <small class="text-muted">
                        <?= __('Last Modified') ?>
                        <?= h($image->modified) ?>
                    </small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
