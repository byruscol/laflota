<div class="row-fluid">
    <div class="row-fluid">
        <form id="uploadFiles" class="form-inline" enctype="multipart/form-data" method="post">
            <legend><?php echo $resource->getWord("uploadExtensionFile"); ?></legend>
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
                <h2><?php echo $resource->getWord("vehiculos"); ?></h2>
            </div>
            <div class="span12">
            <table id="vehiculos"></table>
            <div id="vehiculosPager"></div>
            </div>
        </div>
    </div>
    <br/>
    <div class="span12"></div>
    <div id="tabs" class="span11">
        <ul id="optionsTab" class="nav nav-tabs">
            <li class="active"><a href="#extensionesTab" data-toggle="tab"><?php echo $resource->getWord("extensiones"); ?></a></li>
        </ul>
        <div id="TabContent" class="tab-content">
            <div class="tab-pane fade active" id="extensionesTab">
                <div class="spacer10"></div>
                <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                    <table id="extensiones"></table>
                    <div id="extensionesPager"></div>
                </div>
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
      var tab = jQuery('#tabs li:eq(0) a').attr("href");
      jQuery(tab).css("opacity", 1);
   });
</script>