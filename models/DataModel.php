<?php
// models/DataModel.php
namespace app\models;

use yii\db\ActiveRecord;

class DataModel extends ActiveRecord
{
    // Определяем имя таблицы в базе данных
    public static function tableName()
    {
        return 'data_model';
    }

    // Определяем связь с моделью User (один ко многим)
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    // Определяем связь с моделью Role (один ко многим)
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

}
