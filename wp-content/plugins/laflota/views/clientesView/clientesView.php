<div class="row-fluid">
    <div class="row-fluid">
        <form id="uploadFiles" class="form-inline" enctype="multipart/form-data" method="post">
           <!-- Form Name -->
            <legend><?php echo $resource->getWord("uploadFile"); ?></legend>

            <!-- Text input-->
            <div class="form-group">
                <input type="hidden" name="oper" value="load"/>
            </div>
            
            <!-- Text input-->
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
                <h2 style="float: left;"><?php echo $resource->getWord("clientes"); ?></h2> <a class="btn btn-primary" data-target="#flotaClienteModal" data-toggle="modal" href="#"><?php echo $resource->getWord("verFlotaCliente"); ?></a>
            </div>
            <div class="span12">
            <table id="clientes"></table>
            <div id="clientesPager"></div>
            </div>
        </div>
    </div>
    <br/>
    <div class="span12"></div>
    <div id="tabs" class="span11">
        <ul id="optionsTab" class="nav nav-tabs">
            <li class="active"><a href="#vehiculosTab" data-toggle="tab"><?php echo $resource->getWord("vehiculos"); ?></a></li>
            <li><a href="#usuariosTab" data-toggle="tab"><?php echo $resource->getWord("usuarios"); ?></a></li>
        </ul>
        <div id="TabContent" class="tab-content">
            <div class="tab-pane fade active" id="vehiculosTab">
                <div class="spacer10"></div>
                <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                    <table id="vehiculos"></table>
                    <div id="vehiculosPager"></div>
                </div>
            </div>
            <div class="tab-pane fade active" id="usuariosTab">
                <div class="spacer10"></div>
                <div class="ui-jqgrid ui-widget ui-corner-all clear-margin span12" dir="ltr" style="">
                    <table id="clientesUsuarios"></table>
                    <div id="clientesUsuariosPager"></div>
                </div>
            </div>
        </div>
    </div> 
</div>

<div id="loading"><p><?php echo $resource->getWord("LoadingFile"); ?></p></div>
<div aria-hidden="true" aria-labelledby="largeModal" role="dialog" tabindex="-1" id="flotaClienteModal" class="modal fade" style="display: none;">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            <h4 id="myModalLabel" class="modal-title"></h4>
            
          </div>
          <div class="modal-body">
            <?php  
                $template =  file_get_contents(__DIR__."/../miFlotaUserView/miFlotaUserView.php");
                $template = str_replace("{placa}", $resource->getWord("placa"), $template);
                $template = str_replace("{placasCriticas}", $resource->getWord("placasCriticas"), $template);
                $template = str_replace("{placasExtendidas}", $resource->getWord("placasExtendidas"), $template);
                
                echo $template = str_replace("{buscarPlaca}", $resource->getWord("buscarPlaca"), $template);  
            ?>
          </div>
          <div class="modal-footer">
            <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
          </div>
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
            height: 100
         });
      var tab = jQuery('#tabs li:eq(0) a').attr("href");
      jQuery(tab).css("opacity", 1);
   });
</script>