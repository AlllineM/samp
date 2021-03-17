<?php
use yii\grid\GridView;
use yii\grid\DataColumn;
use \yii\widgets\ActiveForm;
use \yii\helpers\Html;
use kartik\date\DatePicker;
?>

<?php $this->title = 'Отчет за период'; ?>

<h4><?=$this->title;?></h4>
<?= Html::beginForm('', 'get'); ?>

<div class="row form-group">
    <div class="col-lg-3">
        <?php
        echo DatePicker::widget([
            'name' => 'date_from',
            'value' => date('01-m-Y'),
            'options' => ['placeholder' => 'Начало периода'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true
            ]
        ]);
        ?>
    </div>
    <div class="col-lg-3">
        <?php
        echo DatePicker::widget([
            'name' => 'date_to',
            'value' => date('t-m-Y'),
            'options' => ['placeholder' => 'Конец периода'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true
            ]
        ]);
        ?>
    </div>
    <div class="col-lg-3">
        <?= Html::submitButton('показать', ['class' => 'btn btn-default waves-effect ']) ?>
    </div>
</div>

<br>
<?= GridView::widget([
    'dataProvider' => $monthReportDataProvider,
    'summary' => false,
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'rowOptions' => function ($model, $key, $index, $grid)
    {
        if(in_array($key, [0, 4, 8])) {
            return ['style' => 'background-color:#99CCFF;'];
        }
    },
    'columns' => [
        [
            'attribute' => 'name',
            'format' => 'text',
            'label' => 'Магазины',
        ],
        [
            'attribute' => 'amount',
            'format' => ['decimal', 2],
            'label' => 'Сумма платежей',
        ],
        [
            'attribute' => 'waiting',
            'format' => ['decimal', 2],
            'label' => 'Ожидает оплаты',
        ],
        [
            'attribute' => 'general',
            'format' => ['decimal', 2],
            'label' => 'Общая сумма заключенных договоров',
        ],
   ],
]); ?>
