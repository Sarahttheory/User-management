<?php// models/Role.php

namespace app\models;

use yii\db\ActiveRecord;

class Role extends ActiveRecord
{
    // Определяем имя таблицы в базе данных
    public static function tableName()
    {
        return 'role';
    }

    // Определяем связь между моделями User и Role
    public function getUsers()
    {
        return $this->hasMany(User::class, ['role_id' => 'id']);
    }
}
