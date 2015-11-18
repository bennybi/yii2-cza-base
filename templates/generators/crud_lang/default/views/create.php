<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator cza\base\templates\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->getWidgetNameSpace() . "\\EntityDetailManager" ?>;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = <?= $generator->generateString('Create {modelClass}', ['modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))]) ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">

    <?= "<?php" ?>
    Pjax::begin(['id' => $model->getDetailPjaxName(), 'formSelector'=>$model->getBaseFormName(true)]);
    echo EntityDetailManager::widget([
    'model' => $model,
    ]);
    Pjax::end();
    ?>

</div>
