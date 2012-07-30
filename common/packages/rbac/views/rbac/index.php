<?php
/**
 * Shows links to the various role based access control actions
 */
$this->breadcrumbs = array(
    'RBAC'
);
$this->beginWidget("AAdminPortlet", array(
    "sidebarMenuItems" => array(
        array(
            "label" => "Roles",
            "url" => array("role/index"),
        ),
        array(
            "label" => "Tasks",
            "url" => array("task/index"),
        ),
        array(
            "label" => "Operations",
            "url" => array("operation/index"),
        ),
    ),
    "title" => "Role Based Access Control"
));
?>
<p>Role based access control is an access control system based on the concepts of roles, tasks and operations.</p>
<p>Each user can have one or many roles, each role consists of one or many tasks and each task consists of one of many
    operations.</p>
<p>Operations are the lowest level in the authorisation hierarchy, they represent particular actions that can be
    performed on the site, e.g. posting a blog post.</p>
<?php
$this->endWidget();
?>