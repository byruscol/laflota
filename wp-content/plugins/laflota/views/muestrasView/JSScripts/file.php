<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once  __DIR__."/../../../pluginConfig.php";
header('Content-type: text/javascript');
?>
    
jQuery('#uploadFiles')
  .submit( function( e ) {
    e.preventDefault();
    var form = new FormData( this );
    jQuery.ajax( {
        url: '<?php echo $pluginURL;?>edit.php?controller=muestras',
        type: 'POST',
        data: form,
        processData: false,
        contentType: false,
        beforeSend: function(jqXHR, settings){
                jQuery("#loading").dialog('open');
            },
        success: function(response, textStatus, jqXHR){
            alert(response)
            /*data = jQuery.parseJSON( response );
            if (data.msg != 'success')
            {
                alert(data.error);
            }
            else
            {
                
            }*/
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log("A JS error has occurred.");
        },
        complete: function(jqXHR, textStatus){
            jQuery("#uploadFiles")[0].reset();
            jQuery("#loading").dialog('close');
        }
    } );
/*
else{
    jQuery("<div>"+jQuery.jgrid.nav.alerttext+"</div>").dialog({
        height: 100,
        width: 200,
        modal: true,
        closeOnEscape: true,
        title: jQuery.jgrid.nav.alertcap
      });
}
*/    
    return false;
  } );

