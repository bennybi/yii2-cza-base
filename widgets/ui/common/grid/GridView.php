<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace cza\base\widgets\ui\common\grid;

use kartik\base\Config;
use kartik\dialog\Dialog;
use kartik\mpdf\Pdf;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\ButtonDropdown;
use yii\grid\Column;
use yii\grid\GridView as YiiGridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Pjax;
use kartik\grid\GridView as KaGridView;
use cza\base\models\statics\OperationEvent;

/**
 * The GridView widget is used to display data in a grid.
 *
 * It provides features like sorting, paging and also filtering the data.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GridView extends KaGridView {

    public function run() {
        parent::run();
        $this->registerDeleteSelectedItemsJs();
        $this->registerRefreshJs();
    }

    public function registerDeleteSelectedItemsJs() {
        $view = $this->getView();
        $js = "";

        $js.= "jQuery(document).off('" . OperationEvent::DELETE_BY_IDS . "', '#{$this->id}').on('" . OperationEvent::DELETE_BY_IDS . "', '#{$this->id}', function(e, data){
                var lib = window['krajeeDialog'];
                var ids = jQuery('#{$this->id}').yiiGridView('getSelectedRows');
                lib.confirm('" . Yii::t('app.c2', 'Are you sure to delete these items?') . "', function (result) {
                    if (!result) {
                        return;
                    }
                    if(ids == 0){
                      lib.alert('" . Yii::t('app.c2', 'Please select at least one item!') . "');
                      return;
                    }
                    jQuery.ajax({
                            url: data.url,
                            data: {ids: ids},
                            success: function(data){
                                jQuery('#{$this->id}').trigger('" . OperationEvent::REFRESH . "');
                            }
                    });
                });
            });";

        $view->registerJs($js);
    }

    public function registerRefreshJs() {
        $view = $this->getView();
        $js = "";

        $js.= "jQuery(document).off('" . OperationEvent::REFRESH . "', '#{$this->id}').on('" . OperationEvent::REFRESH . "', '#{$this->id}', function(e, data){
                jQuery.pjax.reload({container: '#' + '{$this->pjaxSettings['options']['id']}'});
            });";

        $view->registerJs($js);
    }

    /**
     * Initialize grid export.
     */
    protected function initExport() {
        if ($this->export === false) {
            return;
        }
        parent::initExport();
        
        $this->exportConversions = array_replace_recursive(
                [
            ['from' => self::ICON_ACTIVE, 'to' => Yii::t('cza', 'Active')],
            ['from' => self::ICON_INACTIVE, 'to' => Yii::t('cza', 'Inactive')],
                ], $this->exportConversions
        );
        if (!isset($this->export['fontAwesome'])) {
            $this->export['fontAwesome'] = false;
        }
        $isFa = $this->export['fontAwesome'];
        $this->export = array_replace_recursive(
                [
            'label' => '',
            'icon' => $isFa ? 'share-square-o' : 'export',
            'messages' => [
                'allowPopups' => Yii::t(
                        'cza', 'Disable any popup blockers in your browser to ensure proper download.'
                ),
                'confirmDownload' => Yii::t('cza', 'Ok to proceed?'),
                'downloadProgress' => Yii::t('cza', 'Generating the export file. Please wait...'),
                'downloadComplete' => Yii::t(
                        'cza', 'Request submitted! You may safely close this dialog after saving your downloaded file.'
                ),
            ],
            'options' => ['class' => 'btn btn-default', 'title' => Yii::t('cza', 'Export')],
            'menuOptions' => ['class' => 'dropdown-menu dropdown-menu-right '],
                ], $this->export
        );
        if (!isset($this->export['header'])) {
            $this->export['header'] = '<li role="presentation" class="dropdown-header">' .
                    Yii::t('cza', 'Export Page Data') . '</li>';
        }
        if (!isset($this->export['headerAll'])) {
            $this->export['headerAll'] = '<li role="presentation" class="dropdown-header">' .
                    Yii::t('cza', 'Export All Data') . '</li>';
        }
        $title = empty($this->caption) ? Yii::t('cza', 'Grid Export') : $this->caption;
        $pdfHeader = [
            'L' => [
                'content' => Yii::t('cza', 'Grid Export (PDF)'),
                'font-size' => 8,
                'color' => '#333333',
            ],
            'C' => [
                'content' => $title,
                'font-size' => 16,
                'color' => '#333333',
            ],
            'R' => [
                'content' => Yii::t('cza', 'Generated') . ': ' . date("D, d-M-Y g:i a T"),
                'font-size' => 8,
                'color' => '#333333',
            ],
        ];
        $pdfFooter = [
            'L' => [
                'content' => Yii::t('cza', "© CZA Yii2 Extensions"),
                'font-size' => 8,
                'font-style' => 'B',
                'color' => '#999999',
            ],
            'R' => [
                'content' => '[ {PAGENO} ]',
                'font-size' => 10,
                'font-style' => 'B',
                'font-family' => 'serif',
                'color' => '#333333',
            ],
            'line' => true,
        ];
        $defaultExportConfig = [
            self::HTML => [
                'label' => Yii::t('cza', 'HTML'),
                'icon' => $isFa ? 'file-text' : 'floppy-saved',
                'iconOptions' => ['class' => 'text-info'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('cza', 'grid-export'),
                'alertMsg' => Yii::t('cza', 'The HTML export file will be generated for download.'),
                'options' => ['title' => Yii::t('cza', 'Hyper Text Markup Language')],
                'mime' => 'text/html',
                'config' => [
                    'cssFile' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
                ],
            ],
            self::CSV => [
                'label' => Yii::t('cza', 'CSV'),
                'icon' => $isFa ? 'file-code-o' : 'floppy-open',
                'iconOptions' => ['class' => 'text-primary'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('cza', 'grid-export'),
                'alertMsg' => Yii::t('cza', 'The CSV export file will be generated for download.'),
                'options' => ['title' => Yii::t('cza', 'Comma Separated Values')],
                'mime' => 'application/csv',
                'config' => [
                    'colDelimiter' => ",",
                    'rowDelimiter' => "\r\n",
                ],
            ],
            self::TEXT => [
                'label' => Yii::t('cza', 'Text'),
                'icon' => $isFa ? 'file-text-o' : 'floppy-save',
                'iconOptions' => ['class' => 'text-muted'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('cza', 'grid-export'),
                'alertMsg' => Yii::t('cza', 'The TEXT export file will be generated for download.'),
                'options' => ['title' => Yii::t('cza', 'Tab Delimited Text')],
                'mime' => 'text/plain',
                'config' => [
                    'colDelimiter' => "\t",
                    'rowDelimiter' => "\r\n",
                ],
            ],
            self::EXCEL => [
                'label' => Yii::t('cza', 'Excel'),
                'icon' => $isFa ? 'file-excel-o' : 'floppy-remove',
                'iconOptions' => ['class' => 'text-success'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('cza', 'grid-export'),
                'alertMsg' => Yii::t('cza', 'The EXCEL export file will be generated for download.'),
                'options' => ['title' => Yii::t('cza', 'Microsoft Excel 95+')],
                'mime' => 'application/vnd.ms-excel',
                'config' => [
                    'worksheet' => Yii::t('cza', 'ExportWorksheet'),
                    'cssFile' => '',
                ],
            ],
            self::PDF => [
                'label' => Yii::t('cza', 'PDF'),
                'icon' => $isFa ? 'file-pdf-o' : 'floppy-disk',
                'iconOptions' => ['class' => 'text-danger'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('cza', 'grid-export'),
                'alertMsg' => Yii::t('cza', 'The PDF export file will be generated for download.'),
                'options' => ['title' => Yii::t('cza', 'Portable Document Format')],
                'mime' => 'application/pdf',
                'config' => [
                    'mode' => 'UTF-8',
                    'format' => 'A4-L',
                    'destination' => 'D',
                    'marginTop' => 20,
                    'marginBottom' => 20,
                    'cssInline' => '.kv-wrap{padding:20px;}' .
                    '.kv-align-center{text-align:center;}' .
                    '.kv-align-left{text-align:left;}' .
                    '.kv-align-right{text-align:right;}' .
                    '.kv-align-top{vertical-align:top!important;}' .
                    '.kv-align-bottom{vertical-align:bottom!important;}' .
                    '.kv-align-middle{vertical-align:middle!important;}' .
                    '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                    '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                    '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}',
                    'methods' => [
                        'SetHeader' => [
                            ['odd' => $pdfHeader, 'even' => $pdfHeader],
                        ],
                        'SetFooter' => [
                            ['odd' => $pdfFooter, 'even' => $pdfFooter],
                        ],
                    ],
                    'options' => [
                        'title' => $title,
                        'subject' => Yii::t('cza', 'PDF export generated by kartik-v/yii2-grid extension'),
                        'keywords' => Yii::t('cza', 'grid, export, yii2-grid, pdf'),
                    ],
                    'contentBefore' => '',
                    'contentAfter' => '',
                ],
            ],
            self::JSON => [
                'label' => Yii::t('cza', 'JSON'),
                'icon' => $isFa ? 'file-code-o' : 'floppy-open',
                'iconOptions' => ['class' => 'text-warning'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('cza', 'grid-export'),
                'alertMsg' => Yii::t('cza', 'The JSON export file will be generated for download.'),
                'options' => ['title' => Yii::t('cza', 'JavaScript Object Notation')],
                'mime' => 'application/json',
                'config' => [
                    'colHeads' => [],
                    'slugColHeads' => false,
                    'jsonReplacer' => new JsExpression("function(k,v){return typeof(v)==='string'?$.trim(v):v}"),
                    'indentSpace' => 4,
                ],
            ],
        ];

        // Remove PDF if dependency is not loaded.
        if (!class_exists("\\kartik\\mpdf\\Pdf")) {
            unset($defaultExportConfig[self::PDF]);
        }

        $this->exportConfig = self::parseExportConfig($this->exportConfig, $defaultExportConfig);
    }

}
