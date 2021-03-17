<?php
/**
 * Created by PhpStorm.
 * User: alina
 * Date: 13.01.19
 * Time: 23:02
 */

namespace frontend\controllers;

use frontend\models\Report;
use Yii;
use frontend\models\Payment;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\Response;


/**
 * Class PaymentController
 * @package frontend\controllers
 */
class PaymentController extends Controller
{
    /**
     * Добавить новый платеж
     */
    public function actionAddPayment()
    {
        $payment = new Payment();
        if ($payment->load(Yii::$app->request->get(), '') && $payment->validate()) {
            $payment->save();
        }
    }

    /**
     * Все платежи по определенной сделке
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetByLeadId()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Payment::find()
            ->where(['lead_id' => Yii::$app->request->get('lead_id')])
            ->asArray()
            ->all();

        return $data;
    }

    public function actionPeriodReport()
    {
        $report = new Report();
        $report->dateFrom = Yii::$app->request->get('date_from');
        $report->dateTo = Yii::$app->request->get('date_to');

        $amoService = new AmoController();

        $monthReportProvider = $report->monthReport($amoService->getLeads());

        return $this->render('periodReport', [
            'model' => new Report(),
            'monthReportDataProvider' => $monthReportProvider,
        ]);
    }

    public function actionYearReport()
    {
        $report = new Report();
        $report->year = Yii::$app->request->get('year');

        $amoService = new AmoController();
        $yearReportProvider = $report->yearReport(($amoService->getLeads())); //todo: добавить год

        return $this->render('yearReport', [
            'model' => new Report(),
            'yearReportDataProvider' => $yearReportProvider,
        ]);
    }
}
