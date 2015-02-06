function responsive_jqgrid(jqgrid) {
    jqgrid.find(".ui-jqgrid").addClass("clear-margin span12").css("width", "");
    jqgrid.find(".ui-jqgrid-view").addClass("clear-margin span12").css("width", "");
    jqgrid.find(".ui-jqgrid-view > div").eq(1).addClass("clear-margin span12").css("width", "").css("min-height", "0");
    jqgrid.find(".ui-jqgrid-view > div").eq(2).addClass("clear-margin span12").css("width", "").css("min-height", "0");
    jqgrid.find(".ui-jqgrid-sdiv").addClass("clear-margin span12").css("width", "");
    jqgrid.find(".ui-jqgrid-pager").addClass("clear-margin span12").css("width", "");
}

function setTextAreaForm(form, id){
    
    $tr = form.find("#"+id), 
    $label = $tr.children("td.CaptionTD"),
    $data = $tr.children("td.DataTD");
    $data.attr("colspan", "3");
    $data.children("textarea").css("width", "100%");
    var textAreaId = $data.children("textarea").attr('id')
    tinymce.editors = new Array();
    jQuery('#'+textAreaId).tinymce({
        mode : "none",
        theme : "modern",
        plugins: "table code",
        tools: "inserttable"
    });
}

function noHTMLTags(string){return string.replace(/(<([^>]+)>)/ig,'');}

function imageExist(url) 
{
   var img = new Image();
   img.src = url;
   return (img.height != 0)? true : false;
}

function ajaxFileUpload(id, url, elementId, oper, parentRelationShip, gridId) 
{
    if(jQuery('#'+elementId).val() != ""){
        jQuery("#loading")
        .ajaxStart(function () {
            jQuery(this).show();
        })
        .ajaxComplete(function () {
            jQuery(this).hide();
        });

        jQuery.ajaxFileUpload
        (
            {
                url: url,
                secureuri: false,
                fileElementId: elementId,
                dataType: 'json',
                data: { parentId: id, oper: oper, parentRelationShip: parentRelationShip },
                success: function (data, status) {

                    if (typeof (data.msg) != 'undefined') {
                        if (data.msg == "success") {
                            return;
                        } else {
                            alert(data.error);
                        }
                    }
                    else {
                        return alert('Failed to upload file!');
                    }
                },
                complete: function(response){
                    jQuery('#'+gridId).jqGrid().trigger('reloadGrid');
                },
                error: function (data, status, e) {
                    return alert('Failed to upload file!');
                }
            }
        ) 
    }
 }  
 
function getFormData(id, params){
    jQuery.ajax({   type: "POST"
                    ,url: "admin-ajax.php"
                    ,data: params
                }).done(function(data){
                        var objJson = jQuery.parseJSON(data);
                        if(data == "[]"){
                            jQuery('#'+id).find('#oper').val("add");
                        }
                        else{
                            jQuery('#'+id).find('#oper').val("edit");
                            setformData(id, objJson);
                        }
                    });
}

function reSetformData(id){
    jQuery('#'+id).trigger("reset");
}

function setformData(id, obj){
    for(xx in obj[0]){
        if(jQuery("#"+xx))
            jQuery("#"+xx).val(obj[0][xx]);
    }
}

function disableElements(el) {
    for (var i = 0; i < el.length; i++) {
        el[i].disabled = true;

        disableElements(el[i].children);
    }
}

function enableElements(el) {
    for (var i = 0; i < el.length; i++) {
        el[i].disabled = false;

        enableElements(el[i].children);
    }
}

function searchDataByPlaca(e) {
    var val = "";
    if(jQuery("#"+this.id).attr("class") == "aPlaca")
        val = jQuery("#"+this.id).text();
    else
        val = jQuery("#placaInput").val();
    filters = '';
    if(val != ""){
        filters = '{"groupOp":"AND","rules":[{"field":"placa","op":"cn","data":"'+val+'"}]}';
    }
    
    postDataObj = jQuery("#miFlota").jqGrid("getGridParam","postData");
    postDataObj["filters"] = filters;
    	
    jQuery("#miFlota").jqGrid("setGridParam",{postData: postDataObj})
                    .trigger("reloadGrid");
}

function extendedCritial(response){
    var jsonObj = jQuery.parseJSON(response);
    //alert(response)
    var cList = jQuery("#criticasDiv");
    for(xx in jsonObj["criticas"]){
         var li = jQuery('<li/>')
                .addClass('placa')
                .appendTo(cList);

        var aaa = jQuery('<a/>', {
                            href: '#',
                            title: jsonObj["criticas"][xx].Q,
                            name: jsonObj["criticas"][xx].placa,
                            id: jsonObj["criticas"][xx].placa,
                            html: jsonObj["criticas"][xx].placa,
                            class: "aPlaca"
                        }).bind('click',searchDataByPlaca).appendTo(li);
    }
    
    cList = jQuery("#extendidasDiv");
    for(xx in jsonObj["extendidas"]){
         var li = jQuery('<li/>')
                .addClass('placa')
                .appendTo(cList);

        var aaa = jQuery('<a/>', {
                            href: '#',
                            title: jsonObj["extendidas"][xx].kilometraje,
                            name: jsonObj["extendidas"][xx].placa,
                            id: jsonObj["extendidas"][xx].placa,
                            html: jsonObj["extendidas"][xx].placa,
                            class: "aPlaca"
                        }).bind('click',searchDataByPlaca).appendTo(li);
    }
}

jQuery(window).load(function() {
    jQuery('.flexslider').flexslider({
            animation: "slide",
            animationLoop: false,
            controlNav: false,
            directionNav: true,
            itemWidth: 96,
            itemMargin: 5,
            prevText: "",           //String: Set the text for the "previous" directionNav item
            nextText: "",
            slideshow: false
    });
});
