<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator cza\base\templates\generators\crud\Generator */

echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'controllerClass');
//echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'baseControllerClass')->dropDownList([
    '\cza\base\components\controllers\backend\Controller' => '\cza\base\components\controllers\backend\Controller (fit to backend place)',
    '\cza\base\components\controllers\frontend\Controller' => '\cza\base\components\controllers\frontend\Controller (light, caching funcs)',
]);
echo $form->field($generator, 'moduleID');
echo $form->field($generator, 'indexWidgetType')->dropDownList([
    'grid' => 'GridView',
    'list' => 'ListView',
]);
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
