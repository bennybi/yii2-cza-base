<?php

namespace cza\base\widgets\navigation;

use Yii;
use yii\base\InvalidCallException;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use cza\base\widgets\Widget;

/**
 * Breadcrumbs displays a list of links indicating the position of the current page in the whole site hierarchy.
 *
 * For example, breadcrumbs like "Home / Sample Post / Edit" means the user is viewing an edit page
 * for the "Sample Post". He can click on "Sample Post" to view that page, or he can click on "Home"
 * to return to the homepage.
 *
 * To use Breadcrumbs, you need to configure its [[links]] property, which specifies the links to be displayed. For example,
 *
 * ~~~
 * // $this is the view object currently being used
 * echo Breadcrumbs::widget([
 *     'itemTemplate' => "<li><i>{link}</i></li>\n", // template for all links
 *     'links' => [
 *         [
 *             'label' => 'Post Category',
 *             'icon' => 'fa fa-home',
 *             'url' => ['post-category/view', 'id' => 10],
 *             'template' => "<li><b>{link}</b></li>\n", // template for this link only
 *         ],
 *         ['label' => 'Sample Post', 'url' => ['post/edit', 'id' => 1]],
 *         'Edit',
 *     ],
 * ]);
 * ~~~
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class Breadcrumbs extends \yii\widgets\Breadcrumbs {

    protected function renderItem($link, $template) {
        $encodeLabel = $encode = ArrayHelper::remove($link, 'encode', $this->encodeLabels);
        if (array_key_exists('label', $link)) {
            $label = $encodeLabel ? Html::encode($link['label']) : $link['label'];
        } else {
            throw new InvalidConfigException('The "label" element is required for each link.');
        }
        if (isset($link['template'])) {
            $template = $link['template'];
        }

        $icon = "";
        if (isset($link['icon'])) {
            $icon = "<i class='{$link['icon']}'></i>";
        }

        if (isset($link['url'])) {
            $options = $link;
            unset($options['template'], $options['label'], $options['url'], $options['icon']);
            $link = Html::a($icon . $label, $link['url'], $options);
        } else {
            $link = $icon . $label;
        }


        return strtr($template, ['{link}' => $link]);
    }

}
