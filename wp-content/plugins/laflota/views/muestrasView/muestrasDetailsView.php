<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once $pluginPath . "/helpers/Details.php";
$details = new Details($viewFile);
?>
<br/>
<div class="row-fluid">
    <div class="span11">
        <div class="jqGrid">
            <div class="wrap">
                <div id="icon-tools" class="icon32"></div>
                <h2><?php echo $resource->getWord("cliente"); ?></h2>
            </div>
            <div class="span12">
                <?php $details->renderDetail();?>
            </div>
        </div>
    </div>
    
<script>
    jQuery(function () {
        
        jQuery("#loading").dialog({
            closeOnEscape: false,
            autoOpen: false,
            modal: true,
            width: 200,
            height: 100/*,
            open: function(event, ui) { jQuery(".ui-dialog-titlebar-close").hide(); jQuery(".ui-dialog-titlebar").hide();}*/
         });
      var tab = jQuery('#nonConformityTab li:eq(0) a').attr("href");
      jQuery(tab).css("opacity", 1);
   });
</script>