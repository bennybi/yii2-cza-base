<?php

/*
 * Wechat helper
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace cza\base\components\utils;

use Yii;

/**
 * Wechat helper.
 *
 * @author  Ben Bi <bennybi@qq.com>
 */
class WechatHelper extends \yii\base\Component {

    const CODE_OK = 0;
    const CODE_ValidateSignatureError = -40001;
    const CODE_ParseXmlError = -40002;
    const CODE_ComputeSignatureError = -40003;
    const CODE_IllegalAesKey = -40004;
    const CODE_ValidateAppidError = -40005;
    const CODE_EncryptAESError = -40006;
    const CODE_DecryptAESError = -40007;
    const CODE_IllegalBuffer = -40008;
    const CODE_EncodeBase64Error = -40009;
    const CODE_DecodeBase64Error = -40010;
    const CODE_GenReturnXmlError = -40011;

    public function getSHA1($token, $timestamp, $nonce, $encrypt_msg) {
        try {
            $array = array($encrypt_msg, $token, $timestamp, $nonce);
            sort($array, SORT_STRING);
            $str = implode($array);
//            return array(self::CODE_OK, sha1($str));
            return ['status' => self::CODE_OK, 'encryptStr' => sha1($str)];
        } catch (Exception $e) {
            //print $e . "\n";
//            return array(self::CODE_ComputeSignatureError, null);
            return ['status' => self::CODE_ComputeSignatureError, 'encryptStr' => null];
        }
    }

}
