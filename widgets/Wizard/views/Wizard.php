<?php

use yii\helpers\Html;
use cza\base\models\statics\OperationEvent;
use yii\widgets\Pjax;
use kartik\widgets\ActiveForm;
use yii\bootstrap\Modal;

$theme = $this->theme;
$options = $this->context->options;
$model = $this->context->model;
$steps = $this->context->steps;
?>

<?php

echo Html::beginTag('div', $options);
?>

<?php

echo Html::beginTag('ul');
foreach ($steps as $key => $step) {
    $label = "{$step['title']}<br /><small>{$step['summary']}</small>";
    echo Html::beginTag('li');
    echo Html::a($label, "#{$key}");
    echo Html::endTag('li');
}
echo Html::endTag('ul');

echo Html::beginTag('div');
foreach ($steps as $key => $step) {
    echo Html::beginTag('div', ['id' => $key]);
    echo $step['content'];
    echo Html::endTag('div');
}
echo Html::endTag('div');
?>

<?php

echo Html::endTag('div');
?>