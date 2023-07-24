<?php
// controllers/SiteController.php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use app\models\User;
use app\models\LoginForm;
use app\models\RegisterForm;

class SiteController extends Controller
{
    // Отключаем CSRF-токен для простоты
    public $enableCsrfValidation = false;

    // Добавляем фильтр доступа
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['dashboard', 'add-data', 'edit-data'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Пользователь должен быть авторизован
                    ],
                ],
            ],
        ];
    }

    // Экшен для вывода формы авторизации
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard']);
        }

        $model = new LoginForm();
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            if ($model->login()) {
                return $this->redirect(['dashboard']);
            } else {
                Yii::$app->session->setFlash('error', 'Invalid username or password.');
            }
        }

        return $this->render('login', ['model' => $model]);
    }

    // Экшен для регистрации нового пользователя
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard']);
        }

        $model = new RegisterForm();
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            if ($user = $model->register()) {
                Yii::$app->user->login($user);
                Yii::$app->session->setFlash('success', 'Registration successful. You are now logged in.');
                return $this->redirect(['dashboard']);
            }
        }

        return $this->render('register', ['model' => $model]);
    }

    // Экшен для вывода панели управления
    public function actionDashboard()
    {
        $currentUser = Yii::$app->user->identity;

        // Получаем данные для пользователя в зависимости от его роли
        $userData = [];
        if ($currentUser->role === User::ROLE_ADMIN) {
            // Выводим данные всех пользователей
            $userData = User::find()->all();
        } else {
            // Выводим данные только для текущего пользователя
            $userData = [$currentUser];
        }

        return $this->render('dashboard', ['userData' => $userData]);
    }

    // Экшен для выхода из аккаунта
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['login']);
    }

    // Экшен для добавления данных (доступен только администратору)
    public function actionAddData()
    {
        if (Yii::$app->user->identity->role === User::ROLE_ADMIN) {

            // Обрабатываем POST-запрос с данными для добавления и сохраняем в базу данных
            if (Yii::$app->request->post()) {
                // Получаем данные из формы
                $data = Yii::$app->request->post('DataModel');
                // Обработка и сохранение данных...
                Yii::$app->session->setFlash('success', 'Data added successfully.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'You are not authorized to perform this action.');
        }

        return $this->redirect(['dashboard']);
    }

    // Экшен для редактирования данных (доступен только администратору)
    public function actionEditData($id)
    {
        if (Yii::$app->user->identity->role === User::ROLE_ADMIN) {
            // Получаем данные из базы данных по идентификатору $id
            $data = DataModel::findOne($id);

            if (!$data) {
                Yii::$app->session->setFlash('error', 'Data not found.');
                return $this->redirect(['dashboard']);
            }

            if (Yii::$app->request->post()) {
                // Получаем данные из формы
                $postData = Yii::$app->request->post('DataModel');
                // Применяем изменения и сохраняем в базу данных
                $data->attributes = $postData;
                if ($data->save()) {
                    Yii::$app->session->setFlash('success', 'Data edited successfully.');
                } else {
                    Yii::$app->session->setFlash('error', 'Error occurred while saving data.');
                }
                return $this->redirect(['dashboard']);
            }

            return $this->render('edit', ['data' => $data]);
        } else {
            Yii::$app->session->setFlash('error', 'You are not authorized to perform this action.');
            return $this->redirect(['dashboard']);
        }
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
