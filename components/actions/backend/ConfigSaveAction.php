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
use cza\base\widgets\ui\common\part\EntityDetail;

/**
 * Descriptions
 *
 *
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class ConfigSaveAction extends \yii\rest\Action {

    public $entityIdAttribute = 'entity_id';
    public $view = 'edit';

    public function run() {

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        $params = Yii::$app->request->post();
        if (isset($params[$this->entityIdAttribute])) {
            $model = $this->controller->retrieveModel($params[$this->entityIdAttribute]);
        } else {
            throw new HttpException(404, Yii::t('cza', 'Associated entity not found!'));
        }

        $configFormModel = $model->getConfigForm();

        if ($configFormModel->load($params)) {
            if ($configFormModel->save()) {
                Yii::$app->session->setFlash($configFormModel->getMessageName(), [Yii::t('app.c2', 'Saved successful.')]);
            } else {
                Yii::$app->session->setFlash($configFormModel->getMessageName(), $configFormModel->errors);
            }
        }
        return (Yii::$app->request->isAjax) ? $this->controller->renderAjax($this->view, ['model' => $model, 'showTab' => EntityDetail::TAB_CONFIG]) : $this->controller->render($this->view, ['model' => $model, 'showTab' => EntityDetail::TAB_CONFIG]);
    }

}
