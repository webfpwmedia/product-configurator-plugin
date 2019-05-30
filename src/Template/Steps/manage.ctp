<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Configurator $configurator
 * @var \App\Model\Entity\Step $step
 */

$this
    ->assign('title', h($step->name ?? __('Add')))
    ->assign('subtitle', __('Step'));

$action = ['controller' => 'Steps', 'action' => 'edit', $step->id];

if ($step->isNew()) {
    $action['action'] = 'add';
    $action['?']['configurator_id'] = $configurator->id;
}

?>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-small mb-4">
            <div class="card-body">
                <?= $this->Form->create($step, ['url' => $action]) ?>
                <?= $this->Form->control('name') ?>
                <?= $this->Form->control('sort') ?>
                <?= $this->Form->control('config', [
                    'type' => 'json',
                    'label' => __('config.json'),
                    'templateVars' => [
                        'help' => '<p class="text-muted small">' .
                                  __('Defines user-selectable options and associated values for this step of the configurator. ') .
                                  $this->Html->link(__('Documentation'), '#') .
                                  '</p>',
                    ],
                ]) ?>
                <?= $this->Form->submit(__('Save')) ?>
                <?= $this->Form->end() ?>
            </div>

            <?php if (!$step->isNew()): ?>
                <div class="card-footer border-top text-right">
                    <?= $this->Form->postLink(__('Delete'),
                        ['action' => 'delete', $step->id],
                        [
                            'confirm' => __('Click OK to delete this item and all of its associated data.'),
                            'class' => 'btn btn-danger',
                        ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">
                    <?= __('Configurator') ?>
                </h6>
            </div>

            <div class="card-body">
                <ul>
                    <li>
                        <?= $this->Html->link(__($configurator->name),
                            ['controller' => 'Configurators', 'action' => 'view', $configurator->id]) ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
