<?php

use yii\helpers\Html;
use kartik\sortinput\SortableInput;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use cza\base\models\statics\OperationEvent;

$theme = $this->theme;
$options = $this->context->options;
$model = $this->context->model;
$attributeName = $model->getPrefixName('files-list');
?>
<?php

$form = ActiveForm::begin([
            'options' => [
                'id' => $model->getPrefixName('files-form'),
            ],
            'fieldConfig' => [
                'template' => "{label}\n{hint}\n{input}\n{error}",
            ],
        ]);
?>

<?php

echo Html::beginTag('p', $options);
echo Html::hiddenInput('entity_id', $model->id);
echo Html::beginTag('div', ['class' => 'row']);
echo SortableInput::widget([
    'name' => $attributeName,
    'sortableOptions' => [
        'type' => \kartik\sortable\Sortable::TYPE_LIST,
        'pluginEvents' => [
            'sortupdate' => "function(e){ $('#" . $options['id'] . "').trigger('" . OperationEvent::SORT . "', {ids:$(\"input[name='{$attributeName}']\" ).val()});\n}",
        ],
    ],
    'value' => implode(',', $ids),
    'items' => $items,
    'hideInput' => true,
    'options' => ['class' => 'form-control', 'readonly' => true]
]);

echo Html::endTag('div');
echo Html::endTag('p');
?>
<?php ActiveForm::end(); ?>

<?php
$js = "";
$js .= "$('#{$model->getPrefixName('files-form')} .kv-file-remove').off('click').on('click', function(){\n"
        . "$('#" . $options['id'] . "').trigger('" . OperationEvent::DELETE_BY_ID . "', {id:$(this).data('id')});\n"
        . "});\n";

$js .= "$('#{$model->getPrefixName('files-form')} .kv-file-edit').off('click').on('click', function(){\n"
        . "$('#" . $options['id'] . "').trigger('" . OperationEvent::EDIT . "', {id:$(this).data('id')});\n"
        . "});\n";
$this->registerJs($js);
?>