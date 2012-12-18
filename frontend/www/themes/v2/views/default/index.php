<script type="text/javascript">
    <?php $eventsRaw = 'window.eventsRaw = '.CJSON::encode($events); ?>
    <?php echo $eventsRaw ?>;
    <?php if ($open): ?>
        $(function(){
            openPopUpLogIn('<?php echo $open ?>');
        });
    <?php endif ?>
    <?php if (isset($_GET['key'])): ?>
        window.pwdKey = '<?php echo $_GET['key']; ?>';
    <?php endif ?>
</script>