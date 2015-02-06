<div class="row-fluid">
    <div class="row-fluid">
        <form id="uploadFiles" class="form-inline" enctype="multipart/form-data" method="post">
            <legend><?php echo $resource->getWord("uploadSamplesFile"); ?></legend>
            <div class="form-group">
                <input type="hidden" name="oper" value="load"/>
            </div>
            <div class="form-group">
                <input type="file" id="file" name="file" class="btn btn-default" required="true">
            </div>
            <button id="submit" name="submit" class="btn btn-primary"><?php echo $resource->getWord("accept"); ?></button> 
        </form>
    </div>
    
    <div class="span11">
        <div class="jqGrid">
            <div class="wrap">
                <div id="icon-tools" class="icon32"></div>
                <h2><?php echo $resource->getWord("muestras"); ?></h2>
            </div>
            <div class="span12">
            <table id="muestras"></table>
            <div id="muestrasPager"></div>
            </div>
        </div>
    </div>
</div>
<div id="loading"><p><?php echo $resource->getWord("LoadingFile"); ?></p></div>
<script>
    jQuery(function () {
        
        jQuery("#loading").dialog({
            closeOnEscape: false,
            autoOpen: false,
            modal: true,
            width: 200,
            height: 100
         });
   });
</script>