<?php

namespace console\controllers;

use yii\console\Controller;

require_once __DIR__.'/../../common/utils/phpQuery.php';

class WorkerController extends Controller {

    public function actionIndex() {
        $this->doAction();
    }

    private function doAction() {
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: zh-cn\r\n" .
                    "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 10_1 like Mac OS X) AppleWebKit/602.2.14 (KHTML, like Gecko) Version/10.0 Mobile/14B72 Safari/602.1\r\n"
            )
        );
        $context = stream_context_create($opts);
        $url = 'http://s.weibo.com/top/summary?cate=realtimehot';
        $page_content = file_get_contents($url, false, $context);

        \phpQuery::newDocumentHtml($page_content);
        $a = pq('body section.list ul li a');

        $baseUrl = 's.weibo.com';
        $data = [];
        $content = '<table>';
        foreach ($a as $b) {
            $num = self::trimall(pq($b)->find('strong')->text());
            $title = self::trimall(pq($b)->find('span')->contents()->not('em')->text());
            $hot = self::trimall(pq($b)->find('span')->find('em')->text());
            $url = $baseUrl . self::trimall(pq($b)->attr('href'));

            $item = [
                'title' => $title,
                'hot' => $hot,
                'url' => $url,
            ];

            array_push($data, $item);

            $content = $content . "<tr><td>$num</td><td><a href='$url'>$title</a></td> <td>$hot</td></tr>";
        }
        $content = $content . '</table>';

//        echo $content;

        self::sendMail($content);
    }

    public static function sendMail($content) {
        $mailer = \Yii::$app->mailer->compose();
        $mailer
            ->setFrom(['cnluocj@sina.com' => 'cnluocj'])
            ->setTo('cnluocj@gmail.com')
            ->setHtmlBody($content)
            ->setSubject('weibo hot');
        if ($mailer->send()) {
            echo "success\n";
        } else {
            echo "failse\n";
        }
    }

    //删除空格
    public static function trimall($str) {
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }
}