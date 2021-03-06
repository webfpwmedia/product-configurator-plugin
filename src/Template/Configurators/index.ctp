<?php
/**
 * @var \ARC\ProductConfigurator\View\AppView $this
 * @var \ARC\ProductConfigurator\Model\Entity\Configurator[] $configurators
 */

$this
    ->assign('title', 'Configurators')
    ->assign('subtitle', __('Index Of'));

use Cake\Core\Configure; ?>

<div class="row">
    <div class="col">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <div class="row no-gutters">
                    <div class="col">
                        <h6 class="m-0">
                            <?= __('Active Configurators') ?>
                        </h6>
                    </div>
                    <div class="col text-right">
                        <?= $this->Html->link(__('+ Add Configurator'), ['action' => 'add']) ?>
                    </div>
                </div>
            </div>

            <div class="card-body p-0 pb-3">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="border-0"><?= __(ucfirst('name')) ?></th>
                                <th scope="col" class="border-0"><?= __(ucfirst('created')) ?></th>
                                <th scope="col" class="border-0"><?= __(ucfirst('modified')) ?></th>
                                <th scope="col" class="border-0">&nbsp;</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($configurators as $configurator): ?>
                                <tr>
                                    <td><?= h($configurator->name) ?></td>
                                    <td><?= $configurator->created->setTimezone(Configure::read('ARC.ProductConfigurator.timezone')) ?></td>
                                    <td><?= $configurator->modified->setTimezone(Configure::read('ARC.ProductConfigurator.timezone')) ?></td>
                                    <td class="text-right">
                                        <?= $this->Html->link(__('Details'),
                                            ['action' => 'edit', $configurator->id],
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
