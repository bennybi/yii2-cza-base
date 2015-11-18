<?php

namespace cza\base\components\actions\backend;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\HttpException;
use cza\base\behaviors\CmsMediaBehavior;

/**
 * Descriptions
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class TranslationSaveAction extends \yii\rest\Action {

    /**
     * @return array
     */
//    public function run() {
//
//        if ($this->checkAccess) {
//            call_user_func($this->checkAccess, $this->id);
//        }
//
//        $params = Yii::$app->request->post();
////        Yii::info(\yii\helpers\VarDumper::dumpAsString($params));
//        try {
//            if (!isset($params['language'])) {
//                throw new HttpException(404, Yii::t('app', 'Language ({s1}) Not Found!', ['s1' => $params['language']]));
//            }
//
//            if (isset($params['src_model_id'])) {
//                $model = $this->controller->retrieveModel($params['src_model_id']);
//            } else {
//                throw new HttpException(404, Yii::t('app', 'Entity Not Found!'));
//            }
//
//            $translationModel = $model->getTranslation($params['language']);
//            if ($translationModel->load($params) && $translationModel->save()) {
//                Yii::info('ssss1:');
////                $data = $this->controller->render('_translation_form', ['model' => $translationModel, 'ownerModel' => $model]);
//                $data = $this->controller->renderPartial('_translation_form', ['model' => $translationModel, 'ownerModel' => $model]);
//                Yii::info('ssss2:');
//                Yii::info($data);
//                return $data;
////                return $this->controller->renderPartial('_translation_form', ['model' => $translationModel, 'ownerModel' => $model]);
//            }
//        } catch (Exception $ex) {
//            Yii::info('Error: ');
//            Yii::info(\yii\helpers\VarDumper::dumpAsString($translationModel->getErrors()));
//        }
//    }

    public function run() {

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        $params = Yii::$app->request->post();

        if (!isset($params['language'])) {
            throw new HttpException(404, Yii::t('cza', 'Language ({s1}) Not Found!', ['s1' => $params['language']]));
        }

        if (isset($params['src_model_id'])) {
            $model = $this->controller->retrieveModel($params['src_model_id']);
        } else {
            throw new HttpException(404, Yii::t('cza', 'Srouce model not found!'));
        }

        $translationModel = $model->getTranslation($params['language']);

        // handle cms media fields
        if ($this->controller instanceof \cza\base\components\controllers\backend\CmsController) {
            $cmsFields = $this->controller->getCmsFields();
            $translationModel->attachBehavior('CmsMediaBehavior', [
                'class' => CmsMediaBehavior::className(),
                'fields' => $cmsFields,
                'options' => ['isTranslation' => true],
            ]);
        }

        if ($translationModel->load($params) && $translationModel->save()) {
            $responseData = \cza\base\models\statics\ResponseDatum::getSuccessDatum($_POST, array('message' => Yii::t('cza', 'Operation completed successfully!')));
        } else {
            $responseData = \cza\base\models\statics\ResponseDatum::getErrorDatum($_POST, array('message' => $translationModel->getFirstErrors()));
        }

        return \Yii::createObject([
                    'class' => 'yii\web\Response',
                    'format' => \yii\web\Response::FORMAT_JSON,
                    'data' => $responseData,
        ]);
    }

}
