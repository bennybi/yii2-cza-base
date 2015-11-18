<?php

namespace cza\base\components\controllers\backend;

use Yii;
use yii\base\InlineAction;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use cza\base\filters\CmsMediaFilter;
use cza\base\behaviors\CmsMediaBehavior;

/**
 * service cms content control
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class ModelController extends \yii\rest\ActiveController {

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
//        $behaviors['contentNegotiator']['languages'] = [ 'en', 'zh-CN',];

        return $behaviors;
    }

    /**
     * inherit
     * @param type $id
     * @return Model
     */
    protected function findModel($id) {
        return null;
    }

    /**
     * 
     * @param type $id
     * @param type $allowReturnNew
     * @return \cza\base\components\controllers\backend\modelClass
     * @throws NotFoundHttpException
     */
    public function retrieveModel($id = null, $allowReturnNew = true) {
        if (!is_null($id)) {
            $model = $this->findModel($id);
        } elseif (!$allowReturnNew) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            $model = new $this->modelClass;
            $model->loadDefaultValues();
        }

        return $model;
    }

    /**
     * @inheritdoc
     */
    protected function verbs() {
        return \yii\helpers\ArrayHelper::merge(parent::verbs(), [
                    'index' => ['GET', 'HEAD'],
                    'view' => ['GET', 'HEAD'],
                    'create' => ['GET', 'POST', 'PUT', 'PATCH'],
                    'update' => ['GET', 'POST', 'PUT', 'PATCH'],
                    'delete' => ['GET', 'POST', 'DELETE'],
        ]);
    }

}
