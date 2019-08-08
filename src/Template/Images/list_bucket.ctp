<?php
/**
 * @var \ARC\ProductConfigurator\View\AppView $this
 * @var \ARC\ProductConfigurator\Model\Entity\Image[] $images
 * @var array $allFiles
 */

$this
    ->assign('title', 'Inventory')
    ->assign('subtitle', __('S3 Image'));

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
                        <?= $this->Html->link(__('Image Index'), ['action' => 'index']) ?>
                    </div>
                </div>
            </div>

            <div class="card-body p-0 pb-3">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th scope="col" class="border-0"></th>
                            <th scope="col" class="border-0"><?= __(ucfirst('path')) ?></th>
                            <th scope="col" class="border-0"></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($allFiles as $file): ?>
                            <tr>
                                <td>
                                    <div>
                                        <?= $this->Html->image($file['path'], [
                                            'size' => 'md',
                                            'class' => 'img-fluid',
                                        ]) ?>
                                    </div>
                                </td>
                                <td><?= h($file['path']) ?></td>
                                <td>
                                    <?php
                                    echo $this->Form->create(null, [
                                        'url' => ['action' => 'add', '_ext' => 'json'],
                                        'class' => 'form-horizontal ajax-form',
                                        'onCakeError' => 'showErrors',
                                        'onCakeSuccess' => 'hideRow',
                                    ]);
                                    echo $this->Form->hidden('name', ['value' => $file['path']]);
                                    echo $this->Form->hidden('layer', ['value' => 1]);
                                    echo $this->Form->control('mask', [
                                        'label' => __('Index'),
                                    ]);
                                    echo $this->Form->control('position', [
                                        'options' => [
                                            'front' => __('Front'),
                                            'back' => __('Back'),
                                        ]
                                    ]);
                                    echo $this->Form->submit(__('Index Image'));
                                    echo $this->Form->end();
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
<?php $this->append('jsPostApp'); ?>
<script>
    function showErrors(response) {
        let $form = $(this);
        for (let errorField in response.errors) {
            let $input = $form.find('[name="' + errorField + '"]');
            $input.closest('.form-group').addClass('has-error');
            for (let errorMessage in response.errors[errorField]) {
                $input.closest('.form-group').append('<div class="error-message">' + response.errors[errorField][errorMessage] + '</div>')
            }
        }
    }

    function hideRow(response) {
        let $form = $(this);
        $form.closest('td').html('<div class="alert alert-success">Indexing Successful</div>');
    }
</script>
<?php $this->end(); ?>
