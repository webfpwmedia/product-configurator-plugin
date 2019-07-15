<?php
/**
 * @var \ARC\ProductConfigurator\View\AppView $this
 * @var \ARC\ProductConfigurator\Model\Entity\Configurator $configurator
 */

$this
    ->assign('title', h($configurator->name))
    ->assign('subtitle', __('Configurator'));
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">
                    <?= __('Details') ?>
                </h6>
            </div>

            <div class="card-body">
                <?= $this->Form->create($configurator) ?>
                <?= $this->Form->control('name') ?>
                <?= $this->Form->control('bootstrap', [
                    'type' => 'json',
                    'label' => __('bootstrap.json'),
                    'templateVars' => [
                        'help' => '<p class="text-muted small">' .
                                  __('List of part numbers to configure immediately upon user selection of this configurator.') .
                                  '</p>',
                    ],
                ]) ?>

                <div class="row">
                    <div class="col-6">
                        <?= $this->Form->control('width', ['label' => __('(W) Native pixel width of original image.')]) ?>
                    </div>

                    <div class="col-6">
                        <?= $this->Form->control('height', ['label' => __('(H) Native pixel height of original image.')]) ?>
                    </div>
                </div>

                <?= $this->Form->submit(__('Save')) ?>
                <?= $this->Form->end() ?>
            </div>

            <div class="card-footer border-top text-right">
                <?= $this->Form->postLink(__('Delete'),
                    ['action' => 'delete', $configurator->id],
                    [
                        'confirm' => __('Click OK to delete this item and all of its associated data.'),
                        'class' => 'btn btn-danger',
                    ]) ?>
            </div>
        </div>
    </div>

    <?php if (!$configurator->isNew()): ?>

        <div class="col-lg-4">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom">
                    <h6 class="m-0 d-flex justify-content-between">
                        <?= __('Steps') ?>

                        <small>
                            <?= $this->Html->link(__('+ Add Step'),
                                ['controller' => 'Steps', 'action' => 'add', '?' => [
                                    'configurator_id' => $configurator->id,
                                ]]) ?>
                        </small>
                    </h6>

                    <p class="text-muted small mt-1 mb-0">
                        <?= __('These display in the public UI, enabling users to navigate the configurator.') ?>
                    </p>
                </div>

                <div class="card-body">
                    <ol>
                        <?php foreach ($configurator->steps as $step): ?>
                            <li>
                                <?= $this->Html->link(__($step->name),
                                    ['controller' => 'Steps', 'action' => 'edit', $step->id]) ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>
