<?php 
return array(
    'title'=>'Пожалуйста, представьтесь',
 
    'elements'=>array(
        'first_name'=>array(
            'type'=>'text',
            'maxlength'=>32,
        ),
        'last_name'=>array(
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
        'document_type_id'=>array(
            'type'=>'dropdownlist',
            'items'=>array(1=>'Пасспорт РФ',2=>'Загран паспорт', 3=>'св-во о рожд'),
    		'prompt'=>'Тип документа:',
        ),
        'gender_id'=>array(
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