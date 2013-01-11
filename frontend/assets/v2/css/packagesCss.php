<?php return array(
    'cssFromLess' => array(
        'basePath' => 'frontend.assets.v2.css',
        'css' => array(
            'test.css',
            'test2.css'
        )
    ),
    'appCss' => array(
        'baseUrl' => '/themes/v2/css',
        'css' => array(
            'reset.style.css',
            'style.css',
            'popup.css',
            'popup-photo.css',
            'jslider.css',
            'jslider.round.voyanga.css',
            'jsslidecheck.css',
            'panel.css',
            'voyanga-calendar.css',
            'checkradio.css',
            'load.css',
            'jquery.jscrollpane.css',
            'chosen.css'
        ),
        'depends' => array(
            'cssFromLess'
        )
    ),
);
?>