<?php

use yii\db\Migration;
use \yii\base\Exception;

class cza_data001_init extends Migration {

    protected $_dataFilePath;
    public $dataFolder = 'data';
    public $dataFile = 'data001.sql';

    public function init() {
        parent::init();
        $this->_dataFilePath = __DIR__ . DIRECTORY_SEPARATOR . $this->dataFolder . DIRECTORY_SEPARATOR . $this->dataFile;
        if(!\file_exists($this->_dataFilePath)){
            throw new Exception("Data file '{$this->_dataFilePath}' is not avaiable!");
        }
    }

    public function execute($sql, $params = []) {
        echo "    > execute init sql file: {$this->_dataFilePath} ...";
        $time = microtime(true);
        $this->db->createCommand($sql)->bindValues($params)->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->execute(file_get_contents($this->_dataFilePath));
    }

    public function down() {
        $this->dropTable('{{%entity_attachments}}');
    }

}
