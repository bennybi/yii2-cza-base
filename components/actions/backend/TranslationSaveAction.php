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
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class TranslationSaveAction extends \yii\rest\Action {

    public $entityIdAttribute = 'entity_id';
    public $view = 'edit';

    public function run() {

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        $params = Yii::$app->request->post();

        if (!isset($params['language'])) {
            throw new HttpException(404, Yii::t('cza', 'Language ({s1}) Not Found!', ['s1' => $params['language']]));
        }

        if (isset($params[$this->entityIdAttribute])) {
            $model = $this->controller->retrieveModel($params[$this->entityIdAttribute]);
        } else {
            throw new HttpException(404, Yii::t('cza', 'Associated entity not found!'));
        }

        $translationModel = $model->getTranslationModel($params['language']);

        // handle cms media fields
        if ($this->controller instanceof \cza\base\components\controllers\backend\CmsController) {
            $cmsFields = $this->controller->getCmsFields();
            $translationModel->attachBehavior('CmsMediaBehavior', [
                'class' => CmsMediaBehavior::className(),
                'fields' => $cmsFields,
                'options' => ['isTranslation' => true],
            ]);
        }

//        if ($translationModel->load($params) && $translationModel->save()) {
//            $responseData = ResponseDatum::getSuccessDatum(['message' => Yii::t('cza', 'Operation completed successfully!')], $_POST);
//        } else {
//            $responseData = ResponseDatum::getErrorDatum(['message' => Yii::t('cza', 'Operation completed successfully!')], $_POST);
//        }
//
//        return \Yii::createObject([
//                    'class' => 'yii\web\Response',
//                    'format' => \yii\web\Response::FORMAT_JSON,
//                    'data' => $responseData,
//        ]);

        if ($translationModel->load($params)) {
            if ($translationModel->save()) {
                Yii::$app->session->setFlash($translationModel->getMessageName(), [Yii::t('app.c2', 'Saved successful.')]);
            } else {
                Yii::$app->session->setFlash($translationModel->getMessageName(), $translationModel->errors);
            }
        }
        return (Yii::$app->request->isAjax) ? $this->controller->renderAjax($this->view, [ 'model' => $model,]) : $this->controller->render($this->view, [ 'model' => $model,]);
    }

}
