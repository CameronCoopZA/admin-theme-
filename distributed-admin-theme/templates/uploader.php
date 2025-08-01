<?php
function show_uploader($field) {
    $id = get_option($field);
    $img = $id ? wp_get_attachment_url($id) : '';
    echo '<img id="preview_'.$field.'" src="'.esc_url($img).'" style="max-width:150px;'.($img?'':'display:none;').'"><br>';
    echo '<input type="hidden" id="'.$field.'" name="'.$field.'" value="'.esc_attr($id).'">';
    echo '<button type="button" class="button" id="upload_'.$field.'">Upload</button>';
}
?>
