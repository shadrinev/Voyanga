<div class="well">

    <?php
    $this->widget("packages.users.portlets.AUserImagePortlet", array(
        "user" => $model,
    ));
    $this->beginWidget("packages.users.portlets.AUserDetailsPortlet", array(
        "user" => $model
    ));
    ?>
    <h1>Твоя учётная запись</h1>

    <p>Информация:</p>
    <?php
    $this->endWidget();
    ?>
</div>