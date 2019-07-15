<?php
/**
 * @var ARC\ProductConfigurator\View\AppView $this
 * @var ARC\ProductConfigurator\Model\Entity\Component $component
 */

$this
    ->assign('title', h($component->name ?? __('Add')))
    ->assign('subtitle', __('Component'));
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card card-small mb-4">
            <div class="card-body">
                <?= $this->Form->create($component) ?>

                <?= $component->isNew()
                    ? null
                    : $this->Form->control('key', [
                        'value' => $component->id,
                        'readonly' => true,
                        'templateVars' => [
                            'help' => '<p class="text-muted small">' .
                                      __('Use this key to tell the config.json files which component an option set is connected to.') .
                                      '</p>',
                        ],
                    ]) ?>

                <?= $this->Form->control('name', [
                    'templateVars' => [
                        'help' => '<p class="text-muted small">' . __('Used on generated output (i.e. PDF)') . '</p>',
                    ],
                ]) ?>

                <?= $this->Form->control('mask', [
                    'label' => __('Option Mask'),
                    'templateVars' => [
                        'help' => '<p class="text-muted small">' .
                                  __('Use string literals for any section of a part number that the user cannot configure. ') .
                                  __('Use tokenized strings like <code>{{size}}</code> or <code>{{color}}</code> to represent a configurable option. ') .
                                  $this->Html->link(__('Documentation'), '#') .
                                  '</p>',
                    ],
                ]) ?>

                <?= $this->Form->control('image_mask', [
                    'label' => __('Image Mask'),
                    'templateVars' => [
                        'help' => '<p class="text-muted small">' .
                            __('Use string literals for any section of a part number that the user cannot configure. ') .
                            __('Use tokenized strings like <code>{{size}}</code> or <code>{{color}}</code> to represent a configurable option. ') .
                            $this->Html->link(__('Documentation'), '#') .
                            '</p>',
                    ],
                ]) ?>

                <?= $this->Form->control('options', [
                    'type' => 'json',
                    'label' => __('options.json'),
                    'templateVars' => [
                        'help' => '<p class="text-muted small">' .
                            __('Defines user-selectable options and associated values for this component.') .
                            '</p>',
                    ],
                ]) ?>

                <?= $this->Form->submit(__('Save')) ?>
                <?= $this->Form->end() ?>
            </div>

            <?php if (!$component->isNew()): ?>
                <div class="card-footer border-top text-right">
                    <?= $this->Form->postLink(__('Delete'),
                        ['action' => 'delete', $component->id],
                        [
                            'confirm' => __('Click OK to delete this item and all of its associated data.'),
                            'class' => 'btn btn-danger',
                        ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
