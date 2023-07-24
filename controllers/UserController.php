<?php
// controllers/UserController.php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use app\models\User;

class UserController extends Controller
{
    // Отключаем CSRF-токен для простоты
    public $enableCsrfValidation = false;

    // Добавляем фильтр доступа
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'edit'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [User::ROLE_ADMIN], // Только администратор имеет доступ к этим действиям
                    ],
                ],
            ],
        ];
    }

    // Действие для просмотра списка пользователей (доступно только администратору)
    public function actionIndex()
    {
        // Получаем список всех пользователей
        $users = User::find()->all();

        return $this->render('index', ['users' => $users]);
    }

    // Действие для редактирования данных пользователя (доступно только администратору)
    public function actionEdit($id)
    {
        // Находим пользователя по идентификатору
        $user = User::findOne($id);

        if (!$user) {
            Yii::$app->session->setFlash('error', 'User not found.');
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->post()) {
            // Загружаем данные из формы
            $postData = Yii::$app->request->post('User');

            // Применяем изменения и сохраняем в базе данных
            $user->attributes = $postData;
            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'User data updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Error occurred while saving user data.');
            }

            return $this->redirect(['index']);
        }

        return $this->render('edit', ['user' => $user]);
    }

    // Действие для просмотра профиля текущего пользователя
    public function actionProfile()
    {
        $userId = Yii::$app->user->id;
        $dbName = 'user_' . $userId; // Получаем имя динамической базы данных для пользователя
        $userDb = Yii::$app->dynamicDb->getDb($dbName);

        // Используем $userDb для запросов к динамической базе данных

        $data = $userDb->createCommand('SELECT * FROM data_model')->queryAll();

        return $this->render('profile', ['data' => $data]);
    }
}
