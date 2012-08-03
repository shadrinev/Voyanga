<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Date: 03.08.12 8:02
 */
class expiredNotificationWidget extends CWidget
{
    /**
     * @var time before show notification
     */
    public $time;

    /**
     * @var header of modal to show
     */
    public $header;

    /**
     * @var message to show
     */
    public $message;

    /**
     * @var bool ability to close window
     */
    public $showCancel = false;

    /**
     * @var options for modal window (@link BootModal)
     */
    public $modalOptions;

    /**
     * @var string name of modal widget to show (twitter bootstrap widget by default)
     */
    public $bootstrapModal = 'BootModal';

    public function init()
    {
        /** @var CAssetManager $am */
        $am = Yii::app()->getAssetManager();
        $assets = basename(dirname(__FILE__).'/assets/');
        $path = $am->publish($assets);

        /** @var CClientScript $cs */
        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile($path.'/expiredNotification.js');

        $this->modalOptions['autoOpen'] = false;

        if (!isset($this->modalOptions['options']))
            $this->modalOptions['options'] = array();

        if (!isset($this->modalOptions['events']))
            $this->modalOptions['events'] = array();

        if (!isset($this->modalOptions['htmlOptions']))
            $this->modalOptions['htmlOptions'] = array();

        parent::init();
    }

    public function run()
    {
        $modalId = $this->id;

        $this->render('expiredNotification', array(
            'widget' => $this->bootstrapModal,
            'header' => $this->header,
            'message' => $this->message,
            'showCancel' => $this->showCancel,
            'modalOptions' => $this->modalOptions,
            'modalId' => $modalId,
        ), true);

        $options = array(
            'time' => $this->time,
            'modalId' => '#'.$modalId
        );

        $id = $this->id;

        /** @var CClientScript $cs */
        $cs = Yii::app()->getClientScript();

        $options = CJavaScript::encode($options);
        $cs->registerScript(__CLASS__.'#'.$id, "jQuery('#{$id}').expiredNotification({$options});");
    }
}
