<script type="text/javascript">
    <?php $eventsRaw = 'window.eventsRaw = '.CJSON::encode($events); ?>
    <?php echo $eventsRaw ?>
    <?php if ($openLogin): ?>
        $(function(){
            openPopUpLogIn('enter')
        });
    <?php endif ?>
</script>