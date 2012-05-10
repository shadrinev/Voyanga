<div class="well">

    <?php
    $this->widget("packages.users.portlets.AUserImagePortlet", array(
        "user" => $model,
    ));
    $this->beginWidget("packages.users.portlets.AUserDetailsPortlet", array(
        "user" => $model
    ));
    ?>
    <h1>Your Account</h1>

    <p>Your account details:</p>
    <?php
    $this->endWidget();
    ?>
</div>