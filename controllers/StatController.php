<?php

namespace app\controllers;

use app\controllers\AccessController;
use app\models\Activity;
use app\models\AuthAssignment;
use app\models\ChatMessage;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\ManagerToChat;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\SignupForm;
use app\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class StatController extends AccessController
{
    public function actionIndex()
    {
        if($this->request->get('start')){
            $currentMonthStart = $this->request->get('start');
            $currentMonthEnd = $this->request->get('end');
        }else{
            // Получаем начало и конец текущего месяца
            $currentMonthStart = strtotime(date('Y-m-01')); // Начало текущего месяца
            $currentMonthEnd = time(); // Конец текущего месяца (сегодняшний день)
        }

        // Вычисляем аналогичный диапазон для прошлого месяца
        $previousMonthStart = strtotime('-1 month', $currentMonthStart); // Начало прошлого месяца
        $previousMonthEnd = strtotime('-1 month', $currentMonthEnd);     // Конец прошлого месяца

        // Формируем две пары дат для передачи в getCount...
        $datesCurrent = [
            'start' => $currentMonthStart,
            'end' => $currentMonthEnd,
        ];

        $datesPrevious = [
            'start' => $previousMonthStart,
            'end' => $previousMonthEnd,
        ];

        $manager_list = User::getListByRole('user');

        $s = [];
        foreach ($manager_list as $km => $m) {

            $d = new \stdClass();

            $d->manager = $manager_list[$km];

            $messages = ChatMessage::find()
                ->joinWith('managerToChat')
                ->where(['manager_to_chat.manager_id' => $km])
                ->andWhere(['>=', 'date_add', $datesCurrent['start']])
                ->andWhere(['<=', 'date_add', $datesCurrent['end']])
                ->all();
            //сего сообщений
            $d->count_msg = count($messages);

            $previous_messages = ChatMessage::find()
                ->joinWith('managerToChat')
                ->where(['manager_to_chat.manager_id' => $km])
                ->andWhere(['>=', 'date_add', $datesPrevious['start']])
                ->andWhere(['<=', 'date_add', $datesPrevious['end']])
                ->all();
            $d->previous_count_msg = count($previous_messages);

            //Всего активного времени
            $currentActivity = Activity::find()
                ->where(['user_id' => $km])
                ->andWhere(['>=', 'start_date', $datesCurrent['start']])
                ->andWhere(['<=', 'start_date', $datesCurrent['end']])
                ->all();
            $previousActivity = Activity::find()
                ->where(['user_id' => $km])
                ->andWhere(['>=', 'start_date', $datesPrevious['start']])
                ->andWhere(['<=', 'start_date', $datesPrevious['end']])
                ->all();

            $d->full_activity = 0;
            foreach ($currentActivity as $c) {
                echo "$c->end_date $c->start_date: " . ($c->end_date - $c->start_date). "<br>";
                $d->full_activity += $c->end_date - $c->start_date;
            }

            $d->full_activity_text = $this->formatSeconds($d->full_activity);

            $d->full_period = $datesCurrent['end'] - $datesCurrent['start'];
            $d->full_activity_percent = round(($d->full_activity/$d->full_period)*100, 2);

            $d->previous_full_activity = 0;
            foreach ($previousActivity as $c) {
                //echo "end_date $c->end_date - start_date $c->start_date";
                $d->previous_full_activity += $c->end_date - $c->start_date;
            }

            $d->previous_full_activity_text = $this->formatSeconds($d->previous_full_activity);

            $d->previous_full_period = $datesPrevious['end'] - $datesPrevious['start'];
            $d->previous_full_activity_percent = round(($d->previous_full_activity/$d->previous_full_period)*100, 2);

            $dynamic = $this->getDynamics($d->full_activity, $d->previous_full_activity);
            $d->full_activity_dynamic = $dynamic['dynamic'];
            $d->full_activity_dynamic_percent = $dynamic['percent'];

            //Среднее время ответа на первое сообщение
            $mtc = ManagerToChat::find()
                ->where(['manager_id' => $km])
                ->joinWith('firstTwoMessages')
                ->andWhere(['>=', 'date_add', $datesCurrent['start']])
                ->andWhere(['<=', 'date_add', $datesCurrent['end']])
                ->all();

            $answerTime = [];
            foreach ($mtc as $m) {

                $answerTime[] = isset($m->firstTwoMessages[1]) ? ($m->firstTwoMessages[1]->date_add - $m->firstTwoMessages[0]->date_add) : null;
            }
            echo "answerTime <pre>".print_r($answerTime). "</pre>";
            $d->all_answer_time = $answerTime;
            if(count($d->all_answer_time)) {
                $d->average_answer_time = $this->formatSeconds(array_sum($d->all_answer_time) / count($d->all_answer_time));
            } else {
                $d->average_answer_time = false;
            }

            $previousMtc = ManagerToChat::find()
                ->where(['manager_id' => $km])
                ->joinWith('firstTwoMessages')
                ->andWhere(['>=', 'date_add', $datesPrevious['start']])
                ->andWhere(['<=', 'date_add', $datesPrevious['end']])
                ->all();

            $answerTime = [];
            foreach ($previousMtc as $m) {

                $answerTime[] = isset($m->firstTwoMessages[1]) ? ($m->firstTwoMessages[1]->date_add - $m->firstTwoMessages[0]->date_add) : null;
            }
            $d->previous_all_answer_time = $answerTime;

            if(count($d->previous_all_answer_time)) {
                $d->previous_average_answer_time = $this->formatSeconds(array_sum($d->previous_all_answer_time) / count($d->previous_all_answer_time));
            } else {
                $d->previous_average_answer_time = false;
            }

            $d->average_answer_time_all = ChatMessage::calculateAverageResponseTime([$km], $datesCurrent['start'], $datesCurrent['end']);

            $s[$km] = $d;
        }

        //echo "<pre>";
        //print_r($s);die;
        return $this->render('index', [
            'manager_list' => $manager_list,
            's' => $s,
            'datesCurrent' => $datesCurrent
        ]);
    }

    /**
     * Форматирует кол-во секунд в человекочитаемое время 4 ч. 15 мин.
     * @param $seconds
     * @return string
     */
    private function formatSeconds($seconds) {

        if ($seconds < 60) {
            return "$seconds сек.";
        } elseif ($seconds < 3600) { // меньше часа
            $minutes = floor($seconds / 60);
            $remaining_seconds = $seconds % 60;

            if ($remaining_seconds === 0) {
                return "$minutes мин.";
            } else {
                return "$minutes мин. $remaining_seconds сек.";
            }
        } elseif ($seconds < 86400) { // меньше суток
            $hours = floor($seconds / 3600);
            $remaining_minutes = floor(($seconds % 3600) / 60);
            $remaining_seconds = $seconds % 60;

            if ($remaining_minutes === 0 && $remaining_seconds === 0) {
                return "$hours ч.";
            } elseif ($remaining_seconds === 0) {
                return "$hours ч. $remaining_minutes мин.";
            } else {
                return "$hours ч. $remaining_minutes мин.";
            }
        } else {
            return "Больше суток";
        }
    }

    /**
     * Возвращает динамику в %
     * @param $current
     * @param $previous
     * @return array
     */
    private function getDynamics($current, $previous): array
    {
        $result = [
            'percent' => 0,
            'dynamic' => 'up'
        ];
        if ($current > $previous) {


            $difference = abs($previous - $current);
            $result['percent'] = round((($difference / $current) * 100), 2);

            $result['dynamic'] = 'up';
        } elseif ($current < $previous) {

            $difference = abs($current - $previous);
            $result['percent'] = round(($previous == 0 ? 0 :($difference / $previous) * 100), 2);

            $result['dynamic'] = 'down';
        } elseif ($current == $previous) {

            $result['percent'] = 0;
            $result['dynamic'] = 'up';
        }

        return $result;
    }
}