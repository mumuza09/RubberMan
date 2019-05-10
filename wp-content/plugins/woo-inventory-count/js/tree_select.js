 
        //                <![CDATA[  
        jQuery(window).load(function () {  
        jQuery(document).ready(function () {  
        jQuery.extend(jQuery.expr[':'], {  
        unchecked: function (obj) {  
        return ((obj.type == 'checkbox' || obj.type == 'radio') && !jQuery(obj).is(':checked'));  
        }  
        });  
    
        jQuery("#wcic_woopos_categories input:checkbox").live('change', function () {  
        jQuery(this).next('ul').find('input:checkbox').prop('checked', jQuery(this).prop("checked"));  
    
        for (var i = jQuery('#wcic_woopos_categories').find('ul').length - 1; i >= 0; i--) {  
            jQuery('#wcic_woopos_categories').find('ul:eq(' + i + ')').prev('input:checkbox').prop('checked', function () {  
            return jQuery(this).next('ul').find('input:unchecked').length === 0 ? true : false;  
        });  
        }  
        });  
        });  
        });//]]>  

        function wcic_woopos_get_result(page_url,e,d = 'n') {
            jQuery.get(page_url, {method: 'result', download: d},
            function (data, status) {
                if (status == 'success') {
                    jQuery('#woopos_log_result_data').html(data);
                } else {
                    alert("Problem fetching results.");
                }
            }
            );
        }
 