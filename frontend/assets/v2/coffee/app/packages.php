<?php return array(
    //App supporting vendor modules package
    'vendor' => array(
        'basePath' => 'frontend.assets.v2.js.vendor',
        'js' => array(
            'knockout-2.1.0.js',
            'knockout-repeat.js',
            'knockout-deferred-updates.js',
            'underscore.js',
            'backbone.js',
            'jquery-ui-1.8.22.custom.min.js',
            'jquery.easing.1.3.js',
            'jquery.autocomplete.js',
            'jquery.dotdotdot-1.5.1.js',
            'moment.js',
            'scroll/jquery.mousewheel.js',
            'scroll/jquery.jscrollpane.min.js',
        ),
        'depends' => array('jquery')
    ),
    'mapkup' => array(
        'basePath' => 'frontend.assets.v2.js.markup',
        'js' => array(
            //! Markup related scripts and modules
            'resize-new.js',
            'jquery.color.js',
            'popup.js',
            'popup-photo.js',
            'tickets.js',
            'panel-new.js',
            'helpers.js',
            'voyanga-calendar.js',
            'timeline-calendar.js',
            'jquery.select.slider.js',
            'jquery.slider.lib.js',
            'jquery.slider.js',
            'photoslider.js',
            'loader.js',
            'index.js',
        )
    ),
    'common' => array(
        'basePath' => 'frontend.assets.v2.js.app.common',
        'js' => array(
            //! Our application logic
            'calendar.js',
            'API.js',
            'genericpopup.js',
            'photobox.js',
            'module.slider.js',
            'utils.js', 
            'ko.extenders.js',
            
            // custom bindings
            'ko.bindings.timeslider.js',
            'ko.bindings.priceslider.js',
            'ko.bindings.singleslider.js',
            'ko.bindings.slider.js',
            'ko.bindings.checkbox.js',

            'travellers.js',
            'searchParams.js',
            'ko.rangeobservable.js',
            'filters.js',
            'searchPanel.js',
            'autocomplete.js',
        )
    ),
    'avia' => array(
        'basePath' => 'frontend.assets.v2.js.app.avia',
        'js' => array(
            'panel.js',
            'models.js',
            'controllers.js',
            'module.js',
        )
    ),
    'hotels' => array(
        'basePath' => 'frontend.assets.v2.js.app.hotels',
        'js' => array(
            'panel.js',
            'models.js',
            'controllers.js',
            'module.js',
        )
    ),
    'tours' => array(
        'basePath' => 'frontend.assets.v2.js.app.tours',
        'js' => array(
            'panel.js',
            'models.js',
            'controllers.js',
            'module.js',
        )
    ),
    'events' =>  array(
        'basePath' => 'frontend.assets.v2.js.app.events',
        'js' => array(
            'models.js',
            'controllers.js',
            'module.js',
        )
    ),
    'appJs' => array(
        'basePath' => 'frontend.assets.v2.js.app',
        'js' => array(
            'app.js',
        ),
        'depends' => array(
            'vendor',
            'common',
            'avia',
            'tours',
            'hotels',
            'events'
        )
    )
);
?>