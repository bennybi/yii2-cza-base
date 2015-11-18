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
class Sidebar extends \yii\widgets\Menu {

    public $labelTemplate = '{icon}<span>{label}</span>';
    public $itemSubmenuLabelTemplate = '<a class="list-group-item" href="{url}" data-toggle="collapse" data-parent="#{parentId}">{icon} {label} <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>';
    public $itemSubmenuLinkTemplate = '<a class="list-group-item" href="{url}" data-toggle="collapse" data-parent="#{parentId}">{icon} {label} <span class="pull-right"><i class="fa fa-chevron-right"></i></span></a>';
    public $linkTemplate = '<a class="list-group-item" href="{url}">{icon} <span>{label}</span></a>';
    public $submenuTemplate = "\n<div class='collapse' id='{id}'>\n{items}\n</div>\n";
    public $activateParents = true;
    public $primaryMenuCssClass = '';
    public $subMenuCssClass = 'collapse';

    /*
     * count menu layers
     */
    protected $_layer = 0;
    // count nodes
    protected $_count = 0;

    public function init() {
        parent::init();
        $defaults = [
            'tag' => 'div',
            'id' => $this->getId(),
        ];
        $this->options = ArrayHelper::merge($defaults, $this->options);
        
    }

    public function run() {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        $items = $this->normalizeItems($this->items, $hasActiveChild);
        if (!empty($items)) {
            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'ul');
            echo Html::tag($tag, Html::tag('div', $this->renderItems($items), ['class' => 'list-group panel']), $options);
        }
    }

    /**
     * Recursively renders the menu items (without the container tag).
     * @param array $items the menu items to be rendered recursively
     * @return string the rendering result
     */
    protected function renderItems($items, $parent = null) {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $this->_count++;
            if (!isset($item['id'])) {
                $item['id'] = $this->getId() . $this->_count;
            }
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
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
            $menu = $this->renderItem($item, $parent);
            if (!empty($item['items'])) {
                $this->_layer++;
                $menu .= strtr($this->submenuTemplate, [
                    '{id}' => $item['id'],
                    '{items}' => $this->renderItems($item['items'], $item),
                ]);
                $this->_layer--;
            }
            $lines[] = $menu;
        }

        return implode("\n", $lines);
    }

    /**
     * Renders the content of a menu item.
     * Note that the container and the sub-menus are not rendered here.
     * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
     * @return string the rendering result
     */
    protected function renderItem($item, $parent = null) {
        if (empty($item['items'])) {
            $linkTemplate = $this->linkTemplate;
            $labelTemplate = $this->labelTemplate;
            $url = isset($item['url']) ? Html::encode(Url::to($item['url'])) : "#";
        } else {
            $linkTemplate = $this->itemSubmenuLinkTemplate;
            $labelTemplate = $this->itemSubmenuLabelTemplate;
            $url = "#" . $item['id'];
        }
        $template = ArrayHelper::getValue($item, 'template', $linkTemplate);

        $params = [
            '{url}' => Html::encode($url),
            '{label}' => $item['label'],
            '{icon}' => isset($item['icon']) ? "<i class='{$item['icon']}'></i>" : '',
            '{parentId}' => is_null($parent) ? $item['id'] : $parent['id'],
        ];

        return strtr($template, $params);
    }

}
