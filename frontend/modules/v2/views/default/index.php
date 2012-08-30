<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 28.08.12
 * Time: 12:27
 * To change this template use File | Settings | File Templates.
 */
?>
<br />
<br />
<br />
<br />
<div style="width: 200px; margin-left: 100px;">
<input id="Slider2" type="slider" name="price" value="480;1020" />
</div>

<script type="text/javascript">
    var sl = {
        from: 480,
        to: 1080,
        step: 15,
        dimension: '',
        skin: 'round_voyanga',
        scale: false,
        limits: false,
        minInterval: 60,
        value: "480;1020",
        calculate: function( value ){
            var hours = Math.floor( value / 60 );
            var mins = ( value - hours*60 );
            return (hours < 10 ? "0"+hours : hours) + ":" + ( mins == 0 ? "00" : mins );
        },
        onstatechange: function( value ){
            //console.dir( this );
            //console.log(value);
            return false;
        }
    }
    jQuery("#Slider2").slider(sl);
</script>