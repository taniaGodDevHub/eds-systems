<?php
/*echo "<pre>";
print_r($s);
echo "</pre>";*/
?>
    <div class="stat-index">
        <div class="row">
            <div class="col-12">
                <form class="d-flex">
                    <?php
                    $startDate = date('d.m.Y', (int)$datesCurrent['start']);
                    $endDate = date('d.m.Y', (int)$datesCurrent['end']);
                    $defaultDates = "$startDate - $endDate";
                    ?>
                    <?= kartik\daterange\DateRangePicker::widget([
                        'id' => 'dateRangeId',
                        'name' => 'daterange',
                        'attribute' => 'date_range',
                        'convertFormat' => false,
                        'presetDropdown' => true,
                        'value' => $defaultDates,
                        'pluginOptions' => [
                            'locale' => ['format' => 'DD.MM.YYYY'],
                            'separator' => ' - ',
                            'opens' => 'left',
                        ]
                    ]) ?>
                    <span class="input-group-text bg-primary border-primary text-white">
                         <i class="bi bi-calendar-range"></i>
                    </span>
                </form>
            </div>
            <div class="col-12">
                <table class="table table-bordered table-stripped">
                    <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center" colspan="3">Сообщения</th>
                        <th colspan="2" class="text-center">Активность</th>
                    </tr>
                    <tr>
                        <th>Менеджер</th>
                        <th>Всего</th>
                        <th>Ответ</th>
                        <th>Ответ на 1</th>
                        <th>Время</th>
                        <th>Динамика</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($s as $sm): ?>
                        <tr>
                            <td><?= $sm->manager ?></td>
                            <td><?= $sm->count_msg ?></td>
                            <td><?= $sm->average_answer_time_all?></td>
                            <td><?= $sm->previous_full_activity_text ?></td>
                            <td><?= $sm->full_activity_text ?>(<?= $sm->full_activity_percent ?>%)</td>
                            <td><?= $sm->full_activity_dynamic == 'up' ? '<i class="bi bi-arrow-up-short" style="color: green;"></i>' : '<i class="bi bi-arrow-down-short" style="color: red;"></i>' ?><?= $sm->full_activity_dynamic_percent ?>
                                %
                            </td>

                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php

$script = <<< JS
 $(document).on('change', '#dateRangeId', function (ev, picker) {
           let dateString = ev.currentTarget.defaultValue.trim();
           let parts = dateString.split('-').map(part => part.trim());
            console.log('dateString', dateString)
            let startDateStr = parts[0];
            let endDateStr = parts[1]; 
        
           function toUnix(dateStr, endOfDay) {
                let dateParts = dateStr.split('.');
                let day = parseInt(dateParts[0], 10);
                let month = parseInt(dateParts[1], 10) - 1; // месяцы с 0
                let year = parseInt(dateParts[2], 10);
        
                let d = new Date(year, month, day, endOfDay ? 23 : 0, endOfDay ? 59 : 0, endOfDay ? 59 : 0);
                return Math.floor(d.getTime() / 1000); // в секундах
            }

            let startDate = toUnix(startDateStr, false);
            let endDate = toUnix(endDateStr, true);
                
                  let url = new URL(window.location.href);
            url.searchParams.set('start', startDate);
            url.searchParams.set('end', endDate);
        
            window.location.href = url.toString();
        });
JS;
$this->registerJs($script, yii\web\View::POS_READY);