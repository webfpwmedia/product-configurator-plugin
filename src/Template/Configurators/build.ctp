<?php
/**
 * @var \ARC\ProductConfigurator\View\AppView $this
 * @var \ARC\ProductConfigurator\Model\Entity\Configurator $configurator
 */

use ARC\ProductConfigurator\Form\ConfiguratorContext;
use Cake\Core\Configure;

$this
    ->assign('title', h($configurator->name))
    ->assign('subtitle', __('Configurator'));

?>

<div class="arc configurator">
    <ol class="nav-steps">
        <?php foreach ($configurator->steps as $step): ?>
            <li>
                <a href="#step-<?= $step->id ?>"><?= h($step->name) ?></a>
            </li>
        <?php endforeach; ?>
    </ol>

    <div class="configuration">
        <div class="output-ui">
            <div class="image-stack"></div>
        </div>

        <div class="input-form">
            <?= $this->Form->create(new ConfiguratorContext($this->getRequest(), $configurator->bootstrap), [
                'class' => 'garlic-persist',
            ]); ?>

            <?php foreach ($configurator->steps as $step) : ?>
                <div class="step" id="step-<?= $step->id ?>">
                    <h2 class="step-header"><?= h($step->name) ?></h2>

                    <div class="step-body">
                        <?php foreach ($step->config as $componentOptions) : ?>
                            <?php
                            $controlName = $componentOptions['component'] . '.' . str_replace(['{', '}'], '', $componentOptions['token']);
                            ?>
                            <?php if (isset($componentOptions['options'])) : ?>
                                <legend><?= h($componentOptions['name']) ?></legend>

                                <?=
                                $this->Form->control($controlName, [
                                    'label' => false,
                                    'type' => 'radio',
                                    'options' => collection($componentOptions['options'])
                                        ->map(function ($option) {
                                            return [
                                                'value' => $option['code'],
                                                'text' => $option['name'],
                                                'data-swatch' => $option['swatch'] ?? null,
                                            ];
                                        })
                                        ->toList()
                                ]);
                                ?>
                            <?php elseif (isset($componentOptions['inherits'])): ?>
                                <?=
                                $this->Form->hidden($controlName, [
                                    'value' => sprintf(
                                        'inherits:%s:%s',
                                        $componentOptions['inherits']['component'],
                                        str_replace(['{', '}'], '', $componentOptions['inherits']['token'])
                                    )
                                ]);
                                ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?= $this->Form->submit(__(Configure::read('ARC.text.submit')), [
                    'name' => 'submit'
            ]); ?>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>

<?= $this->Html->script('ARC/ProductConfigurator.dist/app.bundle') ?>

<script>
    const configurator = new Configurator($('.arc.configurator'), {
        imageBaseUrl: '<?= Configure::read('ARC.ProductConfigurator.imageBaseUrl') ?>'
    });
</script>

