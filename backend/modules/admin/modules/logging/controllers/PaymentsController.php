<?php
class PaymentsController extends Controller
{
    public function actionIndex()
    {
        $dataProvider=new EMongoDocumentDataProvider('PaymentLog',array('sort'=>array('defaultOrder'=>'timestamp desc')));
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
    }

    private function prettyJson($json) {
        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = '  ';
        $newLine     = "\n";
        $prevChar    = '';
        $outOfQuotes = true;

        for ($i=0; $i<=$strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }
        return $result;
    }

    public function actionGetInfo($id)
    {
        $model = PaymentLog::model()->findByPk(new MongoID($id));
        $retArr = array();
        $widget = new CTextHighlighter();
        $widget->language = 'javascript';
        $retArr['methodName'] = $model->methodName;
        $retArr['request'] = $widget->highlight($this->prettyJson($model->request));
        if($model->response)
        {
            $retArr['response'] = $widget->highlight($this->prettyJson($model->response));
        }
        $retArr['timestamp'] = date("Y-m-d H:i:s",$model->timestamp);
        $retArr['executionTime'] = Yii::app()->format->formatNumber($model->executionTime);
        $retArr['errorDescription'] = $model->errorDescription;

        //$retArr['responseXml'] = $model->responseXml;


        //echo $model->requestXml);
        echo json_encode($retArr);
        exit;
    }

}
