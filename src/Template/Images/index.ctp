<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Image[] $images
 */

$this
    ->assign('title', 'Image Index')
    ->assign('subtitle', __('Configurator'));

?>

<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <div class="row no-gutters">
                    <div class="col">
                        <h6 class="m-0">
                            <?= __('Images') ?>
                        </h6>
                    </div>

                    <div class="col text-right">
                        <?= $this->Html->link(__('S3 Image Inventory'), ['action' => 'listBucket']) ?>
                    </div>
                </div>
            </div>

            <div class="card-body p-0 pb-3">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="border-0"><?= __(ucfirst('name')) ?></th>
                                <th scope="col" class="border-0"><?= __(ucfirst('mask')) ?></th>
                                <th scope="col" class="border-0"><?= __(ucfirst('position')) ?></th>
                                <th scope="col" class="border-0"><?= __(ucfirst('layer')) ?></th>
                                <th scope="col" class="border-0"><?= __(ucfirst('created')) ?></th>
                                <th scope="col" class="border-0"><?= __(ucfirst('modified')) ?></th>
                                <th scope="col" class="border-0"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($images as $image): ?>
                                <tr>
                                    <td><?= h($image->name) ?></td>
                                    <td><?= h($image->mask) ?></td>
                                    <td><?= h($image->position) ?></td>
                                    <td><?= h($image->layer) ?></td>
                                    <td><?= h($image->created) ?></td>
                                    <td><?= h($image->modified) ?></td>
                                    <td>
                                        <?= $this->Html->link(__('Edit'), [
                                            'action' => 'edit',
                                            $image->id
                                        ], [
                                            'class' => 'btn btn-primary',
                                        ]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
