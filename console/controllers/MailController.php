<?php

namespace console\controllers;

use yii\console\Controller;

require_once __DIR__.'/../../common/utils/phpQuery.php';

//define('TIME_INTERVAL', 10800 * 1000000);
define('TIME_INTERVAL', 60 * 1000000);

class MailController extends Controller {

    public function actionIndex() {
        while (1) {
            $cmd = 'php ' . dirname(\Yii::getAlias('@app')) . '/yii worker';
            exec($cmd);
            echo "begin send\n";
            usleep(TIME_INTERVAL);
        }
    }

}
