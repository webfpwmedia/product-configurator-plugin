<?php
/**
 * @var \ARC\ProductConfigurator\View\AppView $this
 * @var \ARC\ProductConfigurator\Model\Entity\Component[] $components
 */

$this
    ->assign('title', 'Components')
    ->assign('subtitle', __('Index Of'));

use Cake\Core\Configure; ?>

<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <div class="row no-gutters">
                    <div class="col">
                        <h6 class="m-0">
                            <?= __('Components') ?>
                        </h6>
                    </div>
                    <div class="col text-right">
                        <?= $this->Html->link(__('+ Add Component'), ['action' => 'add']) ?>
                    </div>
                </div>
            </div>

            <div class="card-body p-0 pb-3">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="border-0"><?= __(ucfirst('name')) ?></th>
                                <th scope="col" class="border-0"><?= __('Alias') ?></th>
                                <th scope="col" class="border-0"><?= __('Option Mask') ?></th>
                                <th scope="col" class="border-0"><?= __(ucfirst('created')) ?></th>
                                <th scope="col" class="border-0"><?= __(ucfirst('modified')) ?></th>
                                <th scope="col" class="border-0">&nbsp;</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($components as $component): ?>
                                <tr>
                                    <td>
                                        <?php
                                        echo h($component->name);
                                        echo $this->Html->tag('p', $component->id, ['class' => 'text-muted'])
                                        ?>
                                    </td>
                                    <td><?= h($component->alias) ?></td>
                                    <td><?= h($component->mask) ?></td>
                                    <td><?= $component->created->setTimezone(Configure::read('ARC.ProductConfigurator.timezone')) ?></td>
                                    <td><?= $component->modified->setTimezone(Configure::read('ARC.ProductConfigurator.timezone')) ?></td>
                                    <td class="text-right">
                                        <?= $this->Html->link(__('Details'),
                                            ['action' => 'edit', $component->id],
                                            ['class' => 'btn btn-primary']);
                                        ?>
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
