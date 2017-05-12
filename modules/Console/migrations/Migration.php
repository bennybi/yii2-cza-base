<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\rbac\migrations;

use dektrium\rbac\components\DbManager;
use yii\base\Component;
use yii\db\MigrationInterface;
use yii\di\Instance;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\rbac\Rule;

/**
 * Migration for applying new RBAC items.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Migration extends Component implements MigrationInterface
{
    /**
     * Initializes the migration.
     * This method will set [[authManager]] to be the 'authManager' application component, if it is `null`.
     */
    public function init()
    {
        parent::init();

//        $this->authManager = Instance::ensure($this->authManager, DbManager::className());
    }

    /**
     * This method contains the logic to be executed when applying this migration.
     * Child classes should not override this method, but use safeUp instead.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function up()
    {
        $transaction = $this->authManager->db->beginTransaction();

        try {
            if ($this->safeUp() === false) {
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();
            $this->authManager->invalidateCache();
            return true;
        } catch (\Exception $e) {
            echo "Rolling transaction back\n";
            echo 'Exception: ' . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")\n";
            echo $e->getTraceAsString() . "\n";
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * The default implementation throws an exception indicating the migration cannot be removed.
     * Child classes should not override this method, but use safeDown instead.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function down()
    {
        $transaction = $this->authManager->db->beginTransaction();
        try {
            if ($this->safeDown() === false) {
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();
            $this->authManager->invalidateCache();
            return true;
        } catch (\Exception $e) {
            echo "Rolling transaction back\n";
            echo 'Exception: ' . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")\n";
            echo $e->getTraceAsString() . "\n";
            $transaction->rollBack();
            return false;
        }
    }

}
