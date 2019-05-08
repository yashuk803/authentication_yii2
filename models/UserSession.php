<?php

namespace app\models;

use Carbon\Carbon;
use Yii;

class UserSession
{
    const COUNT_FAILED = 5;
    const SECONDS = 60;
    const MINUTE = 5;

    private $session;

    public function __construct()
    {
        $this->session = Yii::$app->session;
    }

    /**
     * Count failed attempts in system.
     * Max 3 attempts.
     * This count save in session by key ip adresses.
     *
     */
    public function setFailedAttempts(): void
    {

        if ($this->session[Yii::$app->request->userIP.'.count']) {

            $count = $this->session->get(Yii::$app->request->userIP.'.count');

            $this->session->set(Yii::$app->request->userIP.'.count', $count + 1);

        } else {
            $this->session[Yii::$app->request->userIP.'.count'] = 1;
        }

    }

    /**
     *  Set timer, when count attempts more 3.
     */
    public function setTimer(): void
    {
        $this->session[Yii::$app->request->userIP.'.time'] = time();
    }


    /**
     * Clear session by key ip adress
     */
    public function delete(): void
    {

        unset($this->session[Yii::$app->request->userIP.'.count']);
        unset($this->session[Yii::$app->request->userIP.'.time']);
    }

    public function getFailedAttempts(): int
    {

        if ($this->session[Yii::$app->request->userIP.'.count']) {

            return $this->session[Yii::$app->request->userIP.'.count'];
        } else {
            return 0;
        }
    }

    public function getTimer()
    {
        return $this->session[Yii::$app->request->userIP.'.time'];
    }

    public function isRound()
    {
        if ($this->getTimeMinute() > self::MINUTE) {
            $this->delete();
            return true;
        }

        return false;
    }

    /**
     * Watch time came after max count failed attempts
     * @return bool|\DateInterval
     */
    public function timeHasPassed()
    {

        return Carbon::now()->diff(Carbon::parse($this->getTimer()));
    }

    /**
     * Get the number of seconds to enter
     * @return float|int
     */
    public function getTimeSecond()
    {
        if($this->getTimer()) {

            return   self::MINUTE*self::SECONDS - ($this->timeHasPassed()->i * self::SECONDS + $this->timeHasPassed()->s);
        }
        return 0;

    }

    public function getTimeMinute()
    {
        if($this->getTimer()) {
            return  $this->timeHasPassed()->i;
        }
        return 0;

    }
}