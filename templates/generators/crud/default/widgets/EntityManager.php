<?php
echo "<?php\n";
?>

namespace <?= $generator->getWidgetNameSpace() ?>;

 /**
 * EntityManager handles entities user interface
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class EntityManager extends \cza\base\widgets\EntityManager {

    public $dialogOptions = array(
        'width' => 1000, 'height' => 650
//        ,'modal'=>false
    );

    

}
