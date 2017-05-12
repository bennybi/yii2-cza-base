<?php

/**
 * Cza Bootstrap Widget
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base\widgets\ui\common\form;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\Dropdown;

/**
 * Tabs renders a Tab bootstrap javascript component.
 *
 * For example:
 *
 * ```php
 * echo Tabs::widget([
 *     'items' => [
 *         [
 *             'label' => 'One',
 *             'content' => 'Anim pariatur cliche...',
 *             'active' => true
 *         ],
 *         [
 *             'label' => 'Two',
 *             'content' => 'Anim pariatur cliche...',
 *             'headerOptions' => [...],
 *             'options' => ['id' => 'myveryownID'],
 *         ],
 *         [
 *             'label' => 'Dropdown',
 *             'items' => [
 *                  [
 *                      'label' => 'DropdownA',
 *                      'content' => 'DropdownA, Anim pariatur cliche...',
 *                  ],
 *                  [
 *                      'label' => 'DropdownB',
 *                      'content' => 'DropdownB, Anim pariatur cliche...',
 *                  ],
 *             ],
 *         ],
 *     ],
 * ]);
 * ```
 *
 * @see http://getbootstrap.com/javascript/#tabs
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @since 2.0
 */
//class Tabs extends \yii\bootstrap\Tabs {
class Tabs extends \kartik\tabs\TabsX {

    /**
     * @var array list of tabs in the tabs widget. Each array element represents a single
     * tab with the following structure:
     *
     * - label: string, required, the tab header label.
     * - encode: boolean, optional, whether this label should be HTML-encoded. This param will override
     *   global `$this->encodeLabels` param.
     * - headerOptions: array, optional, the HTML attributes of the tab header.
     * - linkOptions: array, optional, the HTML attributes of the tab header link tags.
     * - content: string, optional, the content (HTML) of the tab pane.
     * - options: array, optional, the HTML attributes of the tab pane container.
     * - active: boolean, optional, whether the item tab header and pane should be visible or not.
     * - items: array, optional, can be used instead of `content` to specify a dropdown items
     *   configuration array. Each item can hold three extra keys, besides the above ones:
     *     * enable: boolean, item tab should be enable or not.
     *     * active: boolean, optional, whether the item tab header and pane should be visible or not.
     *     * content: string, required if `items` is not set. The content (HTML) of the tab pane.
     *     * contentOptions: optional, array, the HTML attributes of the tab content container.
     */
    public $items = [];

    /**
     * Renders the widget.
     */
    public function run() {
        echo $this->renderItems();
        $this->registerPlugin('tab');
        $this->registerClientScript();
    }

    public function registerClientScript() {
        $view = $this->getView();
        $js = "";
        $js.= "jQuery('#{$this->options['id']} .dropdown-menu li a').click(function(){
                  var selText = jQuery(this).text();
                  jQuery(this).parents('.dropdown').find('.dropdown-toggle').html(selText+' <span class=\"caret\"></span>');
                });";
        $view->registerJs($js);
    }

    /**
     * Renders tab items as specified on [[items]].
     * @return string the rendering result.
     * @throws InvalidConfigException.
     */
    protected function renderItems() {
        $headers = [];
        $panes = [];

        if (!$this->hasActiveTab() && !empty($this->items)) {
            $this->items[0]['active'] = true;
        }

        foreach ($this->items as $n => $item) {
            if (!array_key_exists('label', $item)) {
                throw new InvalidConfigException("The 'label' option is required.");
            }
            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $headerOptions = array_merge($this->headerOptions, ArrayHelper::getValue($item, 'headerOptions', []));
            $linkOptions = array_merge($this->linkOptions, ArrayHelper::getValue($item, 'linkOptions', []));
            $enable = isset($item['enable']) ? $item['enable'] : true;
            $onlyLabel = isset($item['onlyLabel']) ? $item['onlyLabel'] : false;
            if (isset($item['items'])) {
                $label .= ' <b class="caret"></b>';
                Html::addCssClass($headerOptions, 'dropdown');

                if ($this->renderDropdown($n, $item['items'], $panes)) {
                    Html::addCssClass($headerOptions, 'active');
                }

                if ($onlyLabel) {
                    $header = $label . "\n"
                            . Dropdown::widget(['items' => $item['items'], 'clientOptions' => false, 'view' => $this->getView()]);
                } else {
                    Html::addCssClass($linkOptions, 'dropdown-toggle');
                    if ($enable) {
                        $linkOptions['data-toggle'] = 'dropdown';
                    } else {
                        $linkOptions['title'] = Yii::t('cza', 'Disallow access in create mode');
                    }
                    $header = Html::a($label, "#", $linkOptions) . "\n"
                            . Dropdown::widget(['items' => $item['items'], 'clientOptions' => false, 'view' => $this->getView()]);
                }
            } else {
                $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
                $options['id'] = ArrayHelper::getValue($options, 'id', $this->options['id'] . '-tab' . $n);

                Html::addCssClass($options, 'tab-pane');
                if (ArrayHelper::remove($item, 'active')) {
                    Html::addCssClass($options, 'active');
                    Html::addCssClass($headerOptions, 'active');
                }

                if ($onlyLabel) {
                    $header = $label;
                } else {
                    if ($enable) {
                        $linkOptions['data-toggle'] = 'tab';
                    } else {
                        $linkOptions['title'] = Yii::t('cza', 'Disallow access in create mode');
                    }
                    $header = Html::a($label, '#' . $options['id'], $linkOptions);
                }

                if ($this->renderTabContent) {
                    $panes[] = Html::tag('div', isset($item['content']) ? $item['content'] : '', $options);
                }
            }

            if (!$enable) {
                Html::addCssClass($headerOptions, 'disabled');
            }

            $headers[] = Html::tag('li', $header, $headerOptions);
        }

        return Html::tag('ul', implode("\n", $headers), $this->options)
                . ($this->renderTabContent ? "\n" . Html::tag('div', implode("\n", $panes), ['class' => 'tab-content']) : '');
    }

}
