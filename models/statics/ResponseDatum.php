<?php

namespace cza\base\models\statics;

use Yii;

/**
 * Description of ResponseDatum
 *    data format type
 *
 * @author ben
 */
class ResponseDatum {

    CONST JSON = "json";
    CONST XML = "xml";
    CONST TEXT = "text";
    CONST TXT = "txt";
    CONST HTML = "html";
    CONST CSV = "csv";
    CONST PDF = "pdf";

    public static function getDataFormats() {
        return array(
            self::CSV, self::XML, self::HTML, self::JSON, self::TEXT, self::TXT, self::PDF,
        );
    }

    public static function isSupport($df) {
        return in_array($df, self::getDataFormats());
    }

    /**
     * standardize the datum format
     * @param array $data - return data fields
     * @param array $metaData - meta data
     * @return ['_data'=>[], '_meta'=[]]
     */
    public static function getSuccessDatum($data = null, $meta = array(), $html = false) {
        $defaultMeta = array(
            'result' => OperationResult::SUCCESS,
            'type' => OperationResult::getType(OperationResult::SUCCESS),
            'time' => date('Y-m-d H:i:s'),
            'message' => Yii::t('cza', 'Operation completed.')
        );
        
        if (isset($meta['message']) && is_array($meta['message'])) {
            $meta['message'] = self::formatMsg($meta['message']);
        }

        $result = array(
            '_data' => $data, '_meta' => array_merge($defaultMeta, $meta),
        );

        if ($html)
            $result['_meta']['message'] = '<div class=\'alert-box done\'><i class=\'fa-check-circle\'></i> ' . $result['_meta']['message'] . '</div>';
        return $result;
    }

    /**
     * standardize the datum format
     * @param array $data - return data fields
     * @param array $metaData - meta data
     */
    public static function getErrorDatum($data, $meta = array(), $html = false) {
        $defaultMeta = array(
            'result' => OperationResult::ERROR,
            'type' => OperationResult::getType(OperationResult::ERROR),
            'time' => date('Y-m-d H:i:s'),
            'message' => Yii::t('cza', 'Operation failed.')
        );

        if (isset($meta['message']) && is_array($meta['message'])) {
            $meta['message'] = self::formatMsg($meta['message']);
        }

        $result = array(
            '_data' => $data, '_meta' => array_merge($defaultMeta, $meta),
        );
        if ($html)
            $result['_meta']['message'] = '<div class=\'alert-box attention\'><i class=\'fa-exclamation-circle\'></i> ' . $result['_meta']['message'] . '</div>';
        return $result;
    }

    public static function formatMsg($messages) {
        $str = "";
        foreach ($messages as $message) {
            $str.="- " . $message . "<br/>\n";
        }
        return $str;
    }

}
