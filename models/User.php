<?php
// models/User.php
namespace app\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord
{
// Константы для ролей
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

// Метод для связи с таблицей ролей
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

// Метод для проверки роли пользователя
    public function isAdmin()
    {
        return $this->role->name === self::ROLE_ADMIN;
    }

// Метод для создания динамической базы данных для пользователя
    public function createDynamicDatabase()
    {
        $dbName = 'user_' . $this->id;

// Проверяем, существует ли уже такая база данных
        $dbExists = Yii::$app->db->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :db", [
            ':db' => $dbName,
        ])->queryScalar();

        if (!$dbExists) {
// Создаем новую базу данных для пользователя
            Yii::$app->db->createCommand("CREATE DATABASE {$dbName}")->execute();

// Выполняем миграции для новой базы данных
            Yii::$app->runAction('migrate/up', ['migrationPath' => '@app/migrations', 'db' => $dbName]);
        }

        return $dbName;
    }
}
