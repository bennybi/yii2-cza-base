<?php

/*
 * Password helper
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace cza\base\components\utils;

use Yii;

/**
 * Password helper.
 *
 * @author  Ben Bi <bennybi@qq.com>
 */
class Password extends \yii\base\Component {

    public $cost = 10;

    /**
     * Wrapper for yii security helper method.
     *
     * @param $password
     *
     * @return string
     */
    public function hash($password) {
        return Yii::$app->security->generatePasswordHash($password, $this->cost);
    }

    /**
     * Wrapper for yii security helper method.
     *
     * @param $password
     * @param $hash
     *
     * @return bool
     */
    public function validate($password, $hash) {
        return Yii::$app->security->validatePassword($password, $hash);
    }

    /**
     * Generates user-friendly random password containing at least one lower case letter, one uppercase letter and one
     * digit. The remaining characters in the password are chosen at random from those three sets.
     *
     * @see https://gist.github.com/tylerhall/521810
     *
     * @param $length
     *
     * @return string
     */
    public function generate($length) {
        $sets = [
            'abcdefghjkmnpqrstuvwxyz',
            'ABCDEFGHJKMNPQRSTUVWXYZ',
            '23456789',
        ];
        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }

        $password = str_shuffle($password);

        return $password;
    }

    public function hexEncode($input) {
        return bin2hex($input);
    }

    public function hexDecode($input) {
        return pack("H*", $input);
    }

}
