<?php
/**
 * @var \ARC\ProductConfigurator\View\AppView $this
 * @var \ARC\ProductConfigurator\Model\Entity\Configurator $configurator
 * @var array $context
 */

use ARC\ProductConfigurator\Form\ConfiguratorContext;
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
            <div class="image-stack"></div>
            <?= $this->Form->button(Configure::read('ARC.ProductConfigurator.text.back'), [
                'templateVars' => [
                    'class' => 'toggle-state'
                ]
            ]) ?>
        </div>

        <div class="input-form">
            <?= $this->Form->create(new ConfiguratorContext($this->getRequest(), $context), [
                'class' => 'garlic-persist',
            ]); ?>

            <?php foreach ($configurator->steps as $step) : ?>
                <div class="step" id="step-<?= $step->id ?>">
                    <h2 class="step-header"><?= h($step->name) ?></h2>

                    <div class="step-body">
                        <?php foreach ($step->config as $componentOptions) : ?>
                            <?php
                            $tokenName = str_replace(['{', '}'], '', $componentOptions['token']);
                            $controlName = $componentOptions['component'] . '.' . $tokenName;
                            $requires = null;
                            if (isset($componentOptions['requires'])) {
                                $requires = sprintf(
                                    'data-requires="%s:%s"',
                                    $componentOptions['requires']['component'],
                                    str_replace(['{', '}'], '', $componentOptions['requires']['token'])
                                );
                            }
                            ?>
                            <?php if (isset($componentOptions['options'])) : ?>
                            <fieldset data-component="<?= $componentOptions['component'] ?>" data-token="<?= $tokenName ?>" <?= $requires ?>>
                                <legend><?= h($componentOptions['name']) ?></legend>

                                <?php
                                $options = collection($componentOptions['options'])
                                    ->map(function ($option) use ($componentOptions) {
                                        $radioOptions = [
                                            'value' => $option['code'],
                                            'text' => $option['name'],
                                            'label' => []
                                        ];

                                        if (isset($option['swatch'])) {
                                            $radioOptions['text'] = '';
                                            $radioOptions['label'] += [
                                                'class' => 'swatch',
                                                'style' => "background-image:url('" . $this->Url->image($option['swatch'], ['size' => 'swatch']) . "')",
                                            ];
                                        }

                                        return $radioOptions;
                                    })
                                    ->toList();

                                if (isset($componentOptions['text'])) {
                                    $options[] = [
                                        'value' => $componentOptions['text']['code'],
                                        'text' => 'Custom',
                                        'label' => [
                                            'data-custom' => true,
                                        ],
                                    ];

                                    $customTextMap[$componentOptions['component']] = [$tokenName => $componentOptions['text']['map']];
                                }

                                echo $this->Form->control($controlName, [
                                    'label' => false,
                                    'type' => 'radio',
                                    'options' => $options,
                                    'escape' => false,
                                ]);

                                if (isset($componentOptions['text'])) {
                                    $this->Form->unlockField($componentOptions['component'] . '.' . BuildsTable::CUSTOM_TEXT_INPUT);
                                    echo $this->Form->control($componentOptions['component'] . '.' . BuildsTable::CUSTOM_TEXT_INPUT, [
                                        'label' => false,
                                        'hidden' => true,
                                        'disabled' => true,
                                        'default' => $componentOptions['text']['default'],
                                        'maxlength' => $componentOptions['text']['maxLength'] ?? 25,
                                    ]);
                                }
                                ?>
                            </fieldset>
                            <?php endif; ?>
                            <?php if (isset($componentOptions['inherits'])): ?>
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

            <?= $this->Form->submit(__(Configure::read('ARC.ProductConfigurator.text.submit')), [
                'name' => 'save'
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
            customTextMap: <?= json_encode($customTextMap) ?>
        });
    });
</script>

