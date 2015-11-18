<?php

namespace cza\base\components\controllers\backend;

use Yii;
use yii\filters\AccessControl;
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
class Controller extends \yii\web\Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'env-settings' => [
                'class' => '\cza\base\components\actions\common\EnvSettingsAction',
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ];
    }

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = []) {
        
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
