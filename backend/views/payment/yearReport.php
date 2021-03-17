<?php
use yii\grid\GridView;
use yii\grid\DataColumn;
use \yii\widgets\ActiveForm;
use \yii\helpers\Html;
use kartik\date\DatePicker;
?>
<?php $this->title = 'Годовой отчет'; ?>

    <h4><?=$this->title;?></h4>

<?= Html::beginForm('', 'get'); ?>

<div class="row form-group">
    <div class="col-lg-1">
        <?= Html::dropDownList('yaer', null, [2020]) ?>
    </div>
    <div class="col-lg-1">
        <?= Html::submitButton('показать', ['class' => 'btn btn-default waves-effect ']) ?>
    </div>
</div>

<?php
$monthList = [
        'Январь',
        'Февраль',
        'Март',
        'Апрель',
        'Май',
        'Июнь',
        'Июль',
        'Август',
        'Сентябрь',
        'Октябрь',
        'Ноябрь',
        'Декабрь',
];
$columns = [];
$columns[] = [
    'attribute' => 'name',
    'format' => 'text',
    'label' => 'Магазины',
];
for ($i = 0; $i < 12; $i++) {
    $columns[] = [
        'attribute' => 'amount_' . $i,
        'format' => ['decimal', 2],
        'header' => $monthList[$i] . ' <p> внесенные',
        'headerOptions' => [
            'style' => 'text-align: center'
        ]
    ];
    $columns[] = [
            'attribute' => 'amount_general_' . $i,
            'format' => ['decimal', 2],
            'header' => $monthList[$i] . ' <p> заключенные',
            'headerOptions' => [
                'style' => 'text-align: center'
        ]
    ];
}
?>

<?= GridView::widget([
    'dataProvider' => $yearReportDataProvider,
    'summary' => false,
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'rowOptions' => function ($model, $key, $index, $grid)
    {
        if(in_array($key, [0, 4, 6])) {
            return ['style' => 'background-color:#99CCFF;'];
        }
    },
    'columns' => $columns,
]); ?>