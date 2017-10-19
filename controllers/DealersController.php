<?php

namespace app\controllers;

use Yii;
use app\models\gii\Dealers;
use app\models\gii\DealersSearch;
use app\models\gii\GoogleAnalyticsProperties;
use app\models\MultiMod;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\base\Response;

/**
 * DealersController implements the CRUD actions for Dealers model.
 */
class DealersController extends Controller
{
    
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
     * Lists all Dealers models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new DealersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Dealers model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $dealer = $this->findModel($id);
        $sites = new ArrayDataProvider([
            'allModels' => $dealer->gaProperties,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['url', 'type', 'ga_view']
            ],            
        ]);
        return $this->render('view', [
            'dealer' => $dealer,
            'properties' => $sites,
        ]);
    }

    /**
     * Creates a new Dealers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $dealer = new Dealers();
        $properties[] = new GoogleAnalyticsProperties();

        if ($dealer->load(Yii::$app->request->post())) {
            $properties = MultiMod::createMultiple(GoogleAnalyticsProperties::className());
            Model::loadMultiple($properties, Yii::$app->request->post());
            
            // AJAX validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($properties),
                    ActiveForm::validate($dealer)
                );
            }
            
            $valid = $dealer->validate();
            $valid = Model::validateMultiple($properties) && $valid;
            
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $dealer->save(false)) {
                        foreach ($properties as $property) {
                            $property->dealer_id = $dealer->id;
                            if (!($flag = $property->save(false))) {
                                $transaction->rollback();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $dealer->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollback();
                }
            }
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'dealer' => $dealer,
            'properties' => $properties,
        ]);
    }

    /**
     * Updates an existing Dealers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $dealer = $this->findModel($id);
        $properties = $dealer->gaProperties;
        if ($dealer->load(Yii::$app->request->post())) {
            $oldIds = ArrayHelper::map($properties, 'id', 'id');
            $properties = MultiMod::createMultiple(GoogleAnalyticsProperties::className(), $dealer->gaProperties);
            Model::loadMultiple($properties, Yii::$app->request->post());
            $deletedIds = array_diff($oldIds, array_filter(ArrayHelper::map($properties, 'id', 'id')));
            
            // AJAX validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($properties),
                    ActiveForm::validate($dealer)
                );
            }
            
            // Validate all models
            $valid = $dealer->validate();            
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $dealer->save(false)) {
                        if (!empty($deletedIds)) {
                            GoogleAnalyticsProperties::deleteAll(['id' => $deletedIds]);
                        }
                        foreach ($properties as $property) {
                            $property->dealer_id = $dealer->id;
                            if (!($flag = $property->save())) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $dealer->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        return $this->render('update', [
            'dealer' => $dealer,
            'properties' => (empty($properties)) ? [new GoogleAnalyticsProperties()] : $properties,
        ]);
    }

    /**
     * Deletes an existing Dealers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Dealers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Dealers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Dealers::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
