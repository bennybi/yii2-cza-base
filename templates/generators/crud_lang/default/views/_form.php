<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator cza\base\templates\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Url;
use yii\helpers\Html;
use cza\base\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin([
                'options' => ['id' => $model->getBaseFormName()],
                'action' => $model->isNewRecord ? 'create' : ['update', 'id' => $model->id],
    ]); ?>

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        $fieldDefinition = $generator->generateActiveField($attribute);
        if(!empty($fieldDefinition)){
            echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
        }
    }
} ?>
    
    <div class="form-group">
        <?= "<?= " ?> Html::submitButton($model->isNewRecord ? Yii::t('app', 'Save') : Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>
    
    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
