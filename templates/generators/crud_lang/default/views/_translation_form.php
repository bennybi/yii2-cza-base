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

use yii\helpers\Html;
use yii\helpers\Url;
use cza\base\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\entity\CmsPage */
/* @var $form yii\widgets\ActiveForm */


?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">
    <?= "<?php\n" ?>
    $form = ActiveForm::begin([
    'options' => ['id' => $model->getTranslationFormName()],
    'action' => ['translation-save'],
    ]);

    $this->registerJs($form->getAjaxJs());
    ?>
    <div class="form-group">
        <?= "<?=" ?> Html::hiddenInput('src_model_id', $model->srcModel->id); ?>
        <?= "<?=" ?> Html::hiddenInput('language', $model->language); ?>
        <?= "<?=" ?> $form->field($model, 'language')->hiddenInput(); ?>
        <?= "<?=" ?> Html::submitButton($model->isNewRecord ? Yii::t('app', 'Save') : Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php
    foreach ($generator->getColumnNames() as $attribute) {
        if (in_array($attribute, $safeAttributes)) {
            $attrField = $generator->generateLanguageActiveField($attribute);
            if ($attrField) {
                echo "    <?= " . $generator->generateLanguageActiveField($attribute) . " ?>\n\n";
            }
        }
    }
    ?>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>