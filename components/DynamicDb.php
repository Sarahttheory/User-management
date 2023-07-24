<?php
// components/DynamicDb.php
namespace app\components;

use Yii;
use yii\base\Component;

class DynamicDb extends Component
{
    private $_db;

    public function getDb($dbName)
    {
        if (!$this->_db) {
            $templateDb = Yii::$app->getDb(); // Получаем подключение к шаблонной базе данных
            $templateDbName = $templateDb->createCommand('SELECT DATABASE()')->queryScalar();

            // Создаем новую базу данных на основе шаблона
            $newDbName = 'user_' . $dbName;
            $templateDb->createCommand("CREATE DATABASE IF NOT EXISTS `$newDbName`")->execute();
            $this->_db = Yii::createObject([
                'class' => 'yii\db\Connection',
                'dsn' => str_replace($templateDbName, $newDbName, $templateDb->dsn),
                'username' => $templateDb->username,
                'password' => $templateDb->password,
                'charset' => $templateDb->charset,
            ]);
        }
        return $this->_db;
    }
}
