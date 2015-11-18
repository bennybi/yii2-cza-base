<?php
echo "<?php\n";
?>

echo $this->context->getTabWidget([
    'options' => ['id' => $this->context->getTabId()],
    'items' => [
        [
            'label' => Yii::t('app', 'Base Info'),
            'content' => $this->context->controller->renderPartial('_form', ['model' => $this->context->model,]),
            'active' => true,
        ],
        $this->context->getTranslationTabs(!$this->context->model->isNewRecord),
    ],
]);