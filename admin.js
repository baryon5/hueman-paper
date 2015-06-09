
jQuery(function(){
    function htmlEncode(html) {
      return document.createElement('a').appendChild(document.createTextNode(html)).parentNode.innerHTML;
    }
    function get_sc(no_html) {
	var name = jQuery("#credit_popup_name").val();
	var position = jQuery("#credit_popup_position").val();
	var type = jQuery("#credit_popup_type").val();
	no_html = (no_html || type == "byline");
	var s = no_html?"":'<span class="credit-editor-only-helper">';
	s += '[credit name="' + (no_html?name:htmlEncode(name)) + '" position="' +
	    (no_html?position:htmlEncode(position)) + '" type="' + type + '"]';
	if (!no_html) { s += "</span>"; }
	return s;
    }

    jQuery("#credit_popup_create").click(function(e){
	var ed = tinymce.editors[0];
	var s = get_sc(false);
	ed.insertContent(s);
	jQuery("#TB_closeWindowButton").click();
    });

    jQuery("#credit_popup_name, #credit_popup_position, #credit_popup_type").on("keyup click", function(e){
	var s = get_sc(true);
	jQuery("#credit_popup_shortcode").val(s);
    });
});
