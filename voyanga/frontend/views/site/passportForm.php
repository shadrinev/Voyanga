<?php 
return array(
    'title'=>'Пожалуйста, представьтесь',
 
    'elements'=>array(
        'firstName'=>array(
            'type'=>'text',
            'maxlength'=>32,
        ),
        'lastName'=>array(
            'type'=>'text',
            'maxlength'=>32,
        ),
        'number'=>array(
            'type'=>'text',
            'maxlength'=>32,
        ),
        'birthday'=>array(
            'type'=>'text',
            'maxlength'=>32,
        ),
        'documentTypeId'=>array(
            'type'=>'dropdownlist',
            'items'=>array(1=>'Пасспорт РФ',2=>'Загран паспорт', 3=>'св-во о рожд'),
    		'prompt'=>'Тип документа:',
        ),
        'genderId'=>array(
            'type'=>'dropdownlist',
            'items'=>array(1=>'Мужской',2=>'Женский'),
    		'prompt'=>'Пол:',
        )
    ),
 
    'buttons'=>array(
        'smb'=>array(
            'type'=>'submit',
            'label'=>'OK',
        ),
    ),
);