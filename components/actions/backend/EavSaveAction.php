<?php

namespace cza\base\components\actions\backend;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\HttpException;
use cza\base\behaviors\CmsMediaBehavior;
use cza\base\models\statics\ResponseDatum;

/**
 * Descriptions
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class EavSaveAction extends \yii\rest\Action {

    public $entityIdAttribute = 'entity_id';
    public $view = 'edit';

    public function run() {

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        $params = Yii::$app->request->post();
//        Yii::info($params);

        if (!isset($params[$this->entityIdAttribute])) {
            throw new HttpException(404, Yii::t('cza', "Missing parameter {$this->entityIdAttribute}!"));
        }

        $entityModel = $this->controller->retrieveModel($params[$this->entityIdAttribute]);
        if (is_null($entityModel)) {
            throw new HttpException(404, Yii::t('cza', 'Associated entity not found!'));
        }

        $modelClass = $this->modelClass;
        $model = new $modelClass(['entityModel' => $entityModel]);

        if ($model->load($params)) {
            if ($model->save()) {
                Yii::$app->session->setFlash($model->getMessageName(), [Yii::t('app.c2', 'Saved successful.')]);
            } else {
                Yii::$app->session->setFlash($model->getMessageName(), $model->errors);
            }
        }
        return (Yii::$app->request->isAjax) ? $this->controller->renderAjax($this->view, ['model' => $entityModel,]) : $this->controller->render($this->view, ['model' => $entityModel,]);
    }

}
