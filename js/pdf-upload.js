jQuery(document).ready(function($) {
var formfield = null;
$('#upload_pdf_button').click(function() {
$('html').addClass('Image');
formfield = $('#edsdb_ccmb_pdf').attr('name');
tb_show('','media-upload.php?type=image&TB_iframe=true');
return false;
});
// user inserts file into post.
//only run custom if user started process using the above process
// window.send_to_editor(html)is how wp normally handle the received data
window.original_send_to_editor = window.send_to_editor;
window.send_to_editor = function(html){
var fileurl;
if (formfield != null) {
fileurl = jQuery(html).attr('href');
$('#edsdb_ccmb_pdf').val(fileurl);
tb_remove();
$('html').removeClass('Image');
formfield = null;
} else {
window.original_send_to_editor(html);
}
};
});