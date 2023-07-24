<?php
// controllers/DataController.php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\DataModel;
use app\models\User;
use app\models\Role;

class DataController extends Controller
{
    // Экшен для отображения формы добавления данных
    public function actionAddData()
    {
        // Проверяем, авторизован ли пользователь
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']); // Перенаправляем на страницу авторизации, если пользователь не авторизован
        }

        $model = new DataModel();

        // Проверяем, есть ли у текущего пользователя роль "Admin"
        $currentUser = Yii::$app->user->identity;
        $isAdmin = Role::findOne(['name' => 'Admin']);
        if (!$isAdmin || !$currentUser->hasRole($isAdmin->id)) {
            Yii::$app->session->setFlash('error', 'You are not authorized to add data.');
            return $this->redirect(['site/dashboard']); // Перенаправляем на страницу панели управления, если пользователь не является администратором
        }

        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {
                // Получаем текущего пользователя и устанавливаем его ID в поле user_id
                $model->user_id = $currentUser->id;

                // Дополнительная логика, если необходимо, перед сохранением данных в базу

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Data added successfully.');
                    return $this->redirect(['site/dashboard']); // Перенаправляем на страницу панели управления после успешного добавления данных
                } else {
                    Yii::$app->session->setFlash('error', 'Error occurred while adding data.');
                }
            }
        }

        return $this->render('add-data', ['model' => $model]);
    }

    // Экшен для отображения формы редактирования данных
    public function actionEditData($id)
    {
        // Проверяем, авторизован ли пользователь
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']); // Перенаправляем на страницу авторизации, если пользователь не авторизован
        }

        $model = DataModel::findOne($id);

        // Проверяем, есть ли у текущего пользователя роль "Admin"
        $currentUser = Yii::$app->user->identity;
        $isAdmin = Role::findOne(['name' => 'Admin']);
        if (!$isAdmin || !$currentUser->hasRole($isAdmin->id)) {
            Yii::$app->session->setFlash('error', 'You are not authorized to edit data.');
            return $this->redirect(['site/dashboard']); // Перенаправляем на страницу панели управления, если пользователь не является администратором
        }

        if (!$model) {
            Yii::$app->session->setFlash('error', 'Data not found.');
            return $this->redirect(['site/dashboard']); // Перенаправляем на страницу панели управления, если данные не найдены
        }

        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {
                // Дополнительная логика, если необходимо, перед сохранением данных в базу

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Data edited successfully.');
                    return $this->redirect(['site/dashboard']); // Перенаправляем на страницу панели управления после успешного редактирования данных
                } else {
                    Yii::$app->session->setFlash('error', 'Error occurred while editing data.');
                }
            }
        }

        return $this->render('edit-data', ['model' => $model]);
    }
}
