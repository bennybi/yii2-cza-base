<?php

use yii\helpers\Html;
?>
<?php echo Html::beginTag('div', $this->context->options); ?>

<div>
    <!-- Menu button for smallar screens -->
    <?php if ($this->context->enables['enableBrand']): ?>
        <div class="navbar-header">
            <a class="navbar-brand" href="<?php echo $this->context->brandOptions['url']; ?>">
                <span>
                    <?php
                    if (isset($this->context->brandOptions['logo']) && $this->context->brandOptions['logo'])
                        echo $this->context->brandOptions['logo'];
                    ?>
                    <?php echo $this->context->brandOptions['label']; ?>
                </span>

            </a>
            <button class="navbar-toggle btn-navbar" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                <span><?=Yii::t('app', 'Menu');?></span>
            </button>
        </div>
    <?php endif ?>



    <!-- Navigation starts -->
    <?php if ($this->context->enables['enableMenu']): ?>
    <!-- Navigation starts -->
    <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation"> 
        <?php
        if (!empty($this->context->menuOptions['items'])) {
            echo cza\base\widgets\navigation\Nav::widget([
                'options' => [
                    'class' => 'nav navbar-nav navbar-right'
                ],
                'items' => $this->context->menuOptions['items'],
            ]);
        }
        ?>
     </nav>
    <?php endif ?>
</div>
<?php echo Html::endTag('div'); ?>