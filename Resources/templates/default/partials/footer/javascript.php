<!-- Goteo utils: Debug functions, Session keeper -->
<script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/goteo.js"></script>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
<?php
    echo 'goteo.debug = ' . (GOTEO_ENV !== 'real' ? 'true' : 'false') . ';';
    echo 'SRC_URL = "' . SRC_URL . '";';
?>
// @license-end
</script>

<!-- geolocation -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDB5iKBQJQXIjI_h3irKKT2u5_P1UAsqfg&callback=initMap"
  type="text/javascript"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;libraries=places"></script>
<script type="text/javascript" src="<?php echo SRC_URL ?>/assets/js/geolocation.js"></script>
