<?php
/*
 * Routes for CUrlManager
 */
return array(
    '<controller:\w+>/<id:\d+>'=>'<controller>/view',
    '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
    '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
    '<action:(agreement_avia|agreement_hotel|iata|agreement)>' => 'site/<action>',
);