<?php
/**
 * @var \ARC\ProductConfigurator\View\AppView $this
 * @var \ARC\ProductConfigurator\Model\Entity\Configurator $configurator
 * @var array $context
 */

use ARC\ProductConfigurator\Form\ConfiguratorContext;
use ARC\ProductConfigurator\Model\Json\Bootstrap;
use ARC\ProductConfigurator\Model\Json\OptionSet;
use ARC\ProductConfigurator\Model\Json\Step;
use ARC\ProductConfigurator\Model\Json\StepCollection;
use ARC\ProductConfigurator\Model\Table\BuildsTable;
use Cake\Core\Configure;

$this
    ->assign('title', h($configurator->name))
    ->assign('subtitle', __('Configurator'));

$customTextMap = [];
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
            <div class="stage">
                <div class="image-stack"></div>

                <?= $this->Form->button(Configure::read('ARC.ProductConfigurator.text.back'), [
                    'templateVars' => [
                        'class' => 'toggle-state'
                    ]
                ]) ?>
            </div>
        </div>

        <div class="input-form">
            <?= $this->Form->create(new ConfiguratorContext($this->getRequest(), Bootstrap::fromArray($context))); ?>

            <?php
            $steps = new StepCollection($configurator->steps);
            ?>

            <?php foreach ($steps as $step) : ?>
                <?php /** @var Step $step */ ?>
                <div class="step" id="step-<?= $step->getId() ?>">
                    <h2 class="step-header"><?= h($step->getName()) ?></h2>

                    <div class="step-body">
                        <?php foreach ($step->getComponents() as $component) : ?>

                            <?php
                            if ($component->getConfig('showToggle')) {
                                echo $this->Form->control($component->getId() . '.' . BuildsTable::TOGGLE_INPUT, [
                                    'type' => 'checkbox',
                                    'label' => false,
                                    'data-toggle' => true,
                                ]);
                            }
                            ?>

                            <div class="step-component">
                                <?php if ($component->getConfig('showQty')) : ?>
                                    <?= $this->Form->control($component->getId() . '.' . BuildsTable::QTY_INPUT, [
                                        'label' => 'Quantity',
                                        'type' => 'number',
                                    ]) ?>
                                <?php endif; ?>


                                <?php foreach ($component->getOptions() as $optionSet) : ?>
                                    <?php
                                    $controlName = $component->getId() . '.' . $optionSet->getToken();
                                    $requires = $optionSet->getRequires();
                                    if ($requires) {
                                        $requiredComponent = key($requires);
                                        if ($requiredComponent === OptionSet::SELF) {
                                            $requiredComponent = $component->getId();
                                        } else {
                                            $requiredComponent = $steps->getIdFromAlias($requiredComponent);
                                        }
                                        $requires = sprintf('data-requires="%s:%s"', $requiredComponent, current($requires));
                                    }
                                    $inherits = $optionSet->getInherits();
                                    ?>

                                    <?php if ($inherits): ?>
                                        <?=
                                        $this->Form->hidden($controlName, [
                                            'value' => sprintf('inherits:%s:%s', $steps->getIdFromAlias(key($inherits)), current($inherits))
                                        ]);
                                        ?>
                                    <?php else: ?>
                                        <fieldset data-component="<?= $component->getId() ?>" data-token="<?= $optionSet->getToken() ?>" <?= $requires ?>>
                                            <legend><?= h($optionSet->getLabel()) ?></legend>

                                            <?php
                                            echo $this->Form->control($controlName, [
                                                'label' => false,
                                                'type' => 'radio',
                                                'options' => $optionSet->getOptions(),
                                                'escape' => false,
                                            ]);

                                            if ($optionSet->isCustomizable()) {
                                                $this->Form->unlockField($component->getId() . '.' . BuildsTable::CUSTOM_TEXT_INPUT);
                                                echo $this->Form->control($component->getId() . '.' . BuildsTable::CUSTOM_TEXT_INPUT, [
                                                    'label' => false,
                                                    'hidden' => true,
                                                    'disabled' => true,
                                                ] + $optionSet->getTextOptions());
                                            }
                                            ?>
                                        </fieldset>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?= $this->elementHook('buildFormPost') ?>

            <?= $this->Form->submit(__(Configure::read('ARC.ProductConfigurator.text.submit')), [
                'name' => 'extra[save]'
            ]); ?>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>

<?= $this->Html->script('ARC/ProductConfigurator.dist/app.bundle') ?>

<script>
    $(document).ready(function () {
        const configurator = new Configurator($('.arc.configurator'), {
            originalImageSize: {
                width: <?= $configurator->width ?>,
                height: <?= $configurator->height ?>,
            },
            imageBaseUrl: '<?= Configure::read('ARC.ProductConfigurator.imageBaseUrl') ?>',
            imageQueryString: '<?= http_build_query(Configure::read('ARC.ProductConfigurator.imgix.md')) ?>',
            frontLabel: '<?= h(Configure::read('ARC.ProductConfigurator.text.front')) ?>',
            backLabel: '<?= h(Configure::read('ARC.ProductConfigurator.text.back')) ?>',
            customTextMap: <?= json_encode($customTextMap) ?>,
            layerDirection: '<?= Configure::read('ARC.ProductConfigurator.layerDirection') ?>'
        });
    });
</script>

