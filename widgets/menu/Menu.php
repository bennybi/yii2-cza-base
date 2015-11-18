<?php

namespace cza\base\widgets\menu;

use Yii;
use yii\base\InvalidCallException;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * Sub Naviation Bar Widget
 * depends on bootstrap & jquery lib, for bootstrap 3.0
 * 
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class Menu extends \yii\widgets\Menu {

    public $labelTemplate = '{icon}<span>{label}</span>';
    public $itemSubmenuLabelTemplate = '<a href="#" class="dropdown-toggle" data-toggle="dropdown">{icon}<span>{label}</span><b class="caret"></b></a>';
    public $linkTemplate = '<a href="{url}">{icon}<span>{label}</span></a>';
    public $itemSubmenuLinkTemplate = '<a href="{url}" class="dropdown-toggle">{icon}<span>{label}</span><b class="caret"></b></a>';
//    public $submenuTemplate = "\n<ul class='dropdown-menu'>\n{items}\n</ul>\n";
//    public $options = ['class' => 'mainnav'];
    public $activateParents = true;
    public $primaryMenuCssClass = 'dropdown';
    public $subMenuCssClass = 'dropdown';

    /*
     * count menu layers
     */
    protected $_layer = 0;

//    public function run() {
//        if ($this->route === null && Yii::$app->controller !== null) {
//            $this->route = Yii::$app->controller->getRoute();
//        }
//        if ($this->params === null) {
//            $this->params = Yii::$app->request->getQueryParams();
//        }
//
//        echo Html::beginTag('div', ['id' => 'subnavbar-container', 'class' => 'subnavbar']) . "\n";
//        echo Html::beginTag('div', ['id' => 'subnavbar-inner', 'class' => 'subnavbar-inner']) . "\n";
//        echo Html::beginTag('div', ['id' => 'subnavbar-inner-container', 'class' => 'container']) . "\n";
//
//        $items = $this->normalizeItems($this->items, $hasActiveChild);
//
//        if (!empty($items)) {
//            $options = $this->options;
//            $tag = ArrayHelper::remove($options, 'tag', 'ul');
//            echo Html::tag($tag, $this->renderItems($items), $options);
//        }
//
//        echo Html::endTag('div') . "\n";
//        echo Html::endTag('div') . "\n";
//        echo Html::endTag('div') . "\n";
//    }

    /**
     * Recursively renders the menu items (without the container tag).
     * @param array $items the menu items to be rendered recursively
     * @return string the rendering result
     */
    protected function renderItems($items) {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];

            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if (!empty($item['items'])) {
                $class[] = ($this->_layer > 0) ? $this->subMenuCssClass : $this->primaryMenuCssClass;
            }
            if (!empty($class)) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }
            $menu = $this->renderItem($item);
            if (!empty($item['items'])) {
                $this->_layer++;
                $menu .= strtr($this->submenuTemplate, [
                    '{items}' => $this->renderItems($item['items']),
                ]);
                $this->_layer--;
            }
            $lines[] = Html::tag($tag, $menu, $options);
        }

        return implode("\n", $lines);
    }

    /**
     * Renders the content of a menu item.
     * Note that the container and the sub-menus are not rendered here.
     * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
     * @return string the rendering result
     */
    protected function renderItem($item) {
        $linkTemplate = empty($item['items']) ? $this->linkTemplate : $this->itemSubmenuLinkTemplate;
        $labelTemplate = empty($item['items']) ? $this->labelTemplate : $this->itemSubmenuLabelTemplate;
        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $linkTemplate);

            $params = [
                '{url}' => Html::encode(Url::to($item['url'])),
                '{label}' => $item['label'],
                '{icon}' => isset($item['icon']) ? "<i class='{$item['icon']}'></i>" : '',
            ];

            return strtr($template, $params);
        } else {
            $template = ArrayHelper::getValue($item, 'template', $labelTemplate);

            return strtr($template, [
                '{label}' => $item['label'],
                '{icon}' => isset($item['icon']) ? "<i class='{$item['icon']}'></i>" : '',
            ]);
        }
    }

    /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     * @param array $item the menu item to be checked
     * @return boolean whether the menu item is active
     */
//    protected function isItemActive($item) {
//        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
//            $route = $item['url'][0];
//            if ($route[0] !== '/' && Yii::$app->controller) {
//                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
//            }
//
//            if (strpos(Yii::$app->request->getUrl(), $item['url'][0]) !== false) {
//                return true;
//            }
//            if (ltrim($route, '/') !== $this->route) {
//                return false;
//            }
//            unset($item['url']['#']);
//            if (count($item['url']) > 1) {
//                foreach (array_splice($item['url'], 1) as $name => $value) {
//                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
//                        return false;
//                    }
//                }
//            }
//
//            return true;
//        }
//
//        return false;
//    }

}
