<?php
/**
 * @var ARC\ProductConfigurator\View\AppView $this
 * @var ARC\ProductConfigurator\Model\Entity\Configurator $configurator
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

            <?= $this->Form->hidden('configurator_id', ['value' => $configurator->id]) ?>

            <?php
            $steps = new StepCollection($configurator->steps);
            ?>

            <?php foreach ($steps as $iStep => $step) : ?>
                <?php /** @var Step $step */ ?>
                <div class="step" id="step-<?= $step->getId() ?>">
                    <h2 class="step-header">
                        <?= $iStep + 1 ?>.
                        <?= h($step->getName()) ?>
                    </h2>

                    <div class="step-body">
                        <?php foreach ($step->getComponents() as $component) : ?>
                            <div class="component" id="component-<?= $component->getId() ?>">
                                <?php if ($component->getConfig('header')): ?>
                                    <h3 class="component-header">
                                        <?= $component->getConfig('header') ?>
                                    </h3>
                                <?php endif; ?>

                                <?php
                                if ($component->getConfig('showToggle')) {
                                    echo $this->Form->control($component->getId() . '.' . BuildsTable::TOGGLE_INPUT, [
                                        'type' => 'checkbox',
                                        'label' => __('Select Component'),
                                        'data-toggle' => true,
                                        'data-component-id' => $component->getId(),
                                    ]);
                                }
                                ?>

                                <div class="component-options" data-component="<?= $component->getId() ?>">
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
                                        $requiresData = null;
                                        if ($requires) {
                                            $requiredComponent = key($requires);
                                            if ($requiredComponent === OptionSet::SELF) {
                                                $requiredComponent = $component;
                                            } else {
                                                $requiredComponent = $step->getStepCollection()->getComponentCollection()->getComponent($requiredComponent);
                                            }
                                            $requiresData = sprintf('data-requires="%s:%s"', $requiredComponent->getId(), current($requires));
                                        }
                                        $inherits = $optionSet->getInherits();
                                        $inheritsData = null;
                                        if ($inherits) {
                                            $inheritsData = sprintf('data-inherits="%s:%s"', $step->getStepCollection()->getComponentCollection()->getComponent(key($inherits))->getId(), current($inherits));
                                        }
                                        if ($optionSet->isCustomizable()) {
                                            $customTextMap[$component->getId()] = $optionSet->getTextMap();
                                        }
                                        ?>

                                        <fieldset id="<?= $component->getId() . '-' . $optionSet->getToken() ?>" data-token="<?= $optionSet->getToken() ?>" <?= $requiresData ?> <?= $inheritsData ?>>
                                            <?php if ($inherits && !$optionSet->getOptions()): ?>
                                                <?= $this->Form->control($controlName, [
                                                    'label' => false,
                                                    'hidden' => true,
                                                    'templates' => ['inputContainer' => '{{content}}'],
                                                ]);
                                                ?>
                                            <?php else: ?>
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
                                                    echo $this->Form->text($component->getId() . '.' . BuildsTable::TEXT_INPUT, [
                                                        'hidden' => true,
                                                    ]);
                                                    echo $this->Form->control($component->getId() . '.' . BuildsTable::CUSTOM_TEXT_INPUT, [
                                                        'label' => false,
                                                        'hidden' => true,
                                                        'disabled' => true,
                                                    ] + $optionSet->getTextOptions());
                                                }
                                                ?>
                                            <?php endif; ?>
                                        </fieldset>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
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

<?= $this->Html->script('ARC/ProductConfigurator.dist/arc-product-configurator.bundle') ?>

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

