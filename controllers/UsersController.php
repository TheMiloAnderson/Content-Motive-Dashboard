<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\UsersWithDealers;
use app\models\DealersWithProperties;
use app\models\gii\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller {
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return ((!Yii::$app->user->isGuest) && Yii::$app->user->identity->isAdmin());
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new UsersWithDealers();
//        if ($model->load(Yii::$app->request->post())) {
//            $model->setPassword($model->password);
//            if ($model->save()) {
//                $model->saveDealers();
//                return $this->redirect(['index']);
//            }
//            $model->unsetPassword();
//        }
        if (!Yii::$app->request->post()) {
            return $this->render('create', [
                'model' => $model,
                'allDealers' => DealersWithProperties::getAvailableDealers(),
            ]);
        } else {
            return $this->render('../dashboard/nodata', []);
        }
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post('UsersWithDealers');
//        if ($post['newPassword'] && $post['newPassword'] !== '') {
//            $model->setPassword($post['newPassword']);
//        }
//        if (Yii::$app->request->post() && $model->save()) {
//            $model->saveDealers();
//            return $this->redirect(['index']);
//        }
//        $model->unsetPassword();
        if (!Yii::$app->request->post()) {
            return $this->render('update', [
                'model' => $model,
                'allDealers' => DealersWithProperties::getAvailableDealers(),
            ]);
        } else {
            return $this->render('../dashboard/nodata', []);
        }
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
//        $this->findModel($id)->delete();
//        return $this->redirect(['index']);
        return $this->render('../dashboard/nodata', []);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = UsersWithDealers::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
