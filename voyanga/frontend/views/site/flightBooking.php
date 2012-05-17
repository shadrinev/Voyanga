<div class="form">
    <?php //VarDumper::dump($form);?>
    <?php /*echo CHtml::beginForm(); */?><!--
    <?php /*foreach($items as $i=>$item): */?>
        <td><?php /*echo CHtml::activeTextField($item,"[$i]name"); */?></td>
        <td><?php /*echo CHtml::activeTextField($item,"[$i]price"); */?></td>
        <td><?php /*echo CHtml::activeTextField($item,"[$i]count"); */?></td>
        <td><?php /*echo CHtml::activeTextArea($item,"[$i]description"); */?></td>
    <?php /*endforeach; */?>
    <?php /*echo CHtml::submitButton('Сохранить'); */?>
    --><?php /*echo CHtml::endForm();*/ ?>
    <?php echo $form; ?>
</div>
