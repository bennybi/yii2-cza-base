<?php

namespace cza\base\components\actions\common;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\HttpException;
use cza\base\behaviors\CmsMediaBehavior;
use cza\base\models\statics\ResponseDatum;
use dosamigos\qrcode\QrCode;

/**
 * Descriptions
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class QrcodeGenerateAction extends \yii\rest\Action {

    public $idAttribute = 'id';
    public $contentAttribute = 'content';
    public $ownerRelationship = 'owner';
    public $view = 'edit';
    public $qrOptions = [
        'level' => 0,
        'size' => 8,
        'margin' => 4,
    ];
    protected $_model;

    public function getUploadPath() {
        return Yii::getAlias(Yii::$app->params['config']['feUser']['uploadPath']) . '/' . $this->_model->user_id;
    }

    public function run() {

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        $params = Yii::$app->request->post();

        $modelClass = $this->modelClass;
        if (isset($params[$this->idAttribute])) {
            $this->_model = $modelClass::findOne($params[$this->idAttribute]);
            if (!is_null($this->_model)) {
                $this->_model->generateQrCode($params[$this->contentAttribute]);
            }
        } else {
            throw new HttpException(404, Yii::t('cza', 'Associated entity not found!'));
        }

        return (Yii::$app->request->isAjax) ? $this->controller->renderAjax($this->view, [ 'model' => $this->_model->{$this->ownerRelationship},]) : $this->controller->render($this->view, [ 'model' => $this->_model->{$this->ownerRelationship},]);
    }

}
