<?php
/**
 * @package EspecialistaWPComprimir
 * @version 1.0
 */
/*
Plugin Name: Especialista WP Comprimir HTML
Plugin URI: https://especialistawp.com/especialistas-wordpress-comprimir-html/
Description: Este plugin permite comprimir el HTML de tu web quitando espacios innecesarios, saltos de linea, comentarios, ... 
Version: 1.0
Author: especialistawp.com
Author URI: https://especialistawp.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function especialista_wp_comprimir_header () {
	ob_start();
}
add_action('init', 'especialista_wp_comprimir_header');

function especialista_wp_comprimir_footer () {
	$content = ob_get_clean(); 
	if(get_option("_especialistas_wp_text") == '1') {
		$content = preg_replace("/type=['\"]text\/css['\"]/", "", $content); //Quitamos el text/css
		$content = preg_replace("/type=['\"]text\/javascript['\"]/", "", $content); //Quitamos el text/javascript
	}
	if(get_option("_especialistas_wp_compress") == '1') {
		$content = preg_replace("/(\t|\n)+/", "", $content); //Quitamos saltos de linea y tabulaciones
		$content = preg_replace('/\s\s+/', " ", $content); //Quitamos dobles espacios en blanco
		$content = str_replace(" />", "/>", $content); //Quitamos espacios en blanco innecesarios
	}
	if(get_option("_especialistas_wp_domain") == '1')$content = str_replace(get_site_url()."/", "/", $content); //Quitamos el dominio de las URLs
	if(get_option("_especialistas_wp_comments") == '1')$content = preg_replace('/<!--(.|\s)*?-->/', '', $content); //Quitamos comentarios
	echo $content; 
}
add_action('wp_footer', 'especialista_wp_comprimir_footer', 10000);

function especialista_wp_quitar_clases_menu($var) {
    return is_array($var) ? array_intersect($var, array('current-menu-item', 'current-menu-parent')) : '';
}



if(get_option("_especialistas_wp_menus") == '1') {
	add_filter( 'nav_menu_item_id', 'especialista_wp_quitar_clases_menu', 100, 1);
	add_filter( 'nav_menu_css_class', 'especialista_wp_quitar_clases_menu', 100, 1);
}


//Administrador 
add_action( 'admin_menu', 'especialista_wp_comprimir_plugin_menu' );
function especialista_wp_comprimir_plugin_menu() {
	add_options_page( __('Compresor HTML', 'especialista_wp_comprimir'), __('Compresor HTML', 'especialista_wp_comprimir'), 'manage_options', 'especialista_wp_comprimir', 'especialista_wp_comprimir_page_settings');
}

function especialista_wp_comprimir_page_settings() { 
	?><h1><?php _e("Compresor HTML", "especialista_wp_comprimir"); ?></h1><?php 
	if(isset($_REQUEST['send']) && $_REQUEST['send'] != '') { 
		?><p style="border: 1px solid green; color: green; text-align: center;"><?php _e("Datos guardados correctamente.", "especialista_wp_comprimir"); ?></p><?php
		update_option('_especialistas_wp_compress', sanitize_text_field( $_POST['_especialistas_wp_compress'] ));
		update_option('_especialistas_wp_text', sanitize_text_field( $_POST['_especialistas_wp_text'] ));
		update_option('_especialistas_wp_domain', sanitize_text_field( $_POST['_especialistas_wp_domain'] ));
		update_option('_especialistas_wp_comments', sanitize_text_field( $_POST['_especialistas_wp_comments'] ));
		update_option('_especialistas_wp_menus', sanitize_text_field( $_POST['_especialistas_wp_menus'] ));
	} ?>
	<h2><?php _e("Opciones", "especialista_wp_comprimir"); ?></h2>
	<form method="post">
		<input type="checkbox" name="_especialistas_wp_compress" value="1"<?php if(get_option("_especialistas_wp_compress") == '1') echo " checked='checked'"; ?> /> <?php _e("Comprimir HTML.", "especialista_wp_comprimir"); ?></br></br>
		<input type="checkbox" name="_especialistas_wp_menus" value="1"<?php if(get_option("_especialistas_wp_menus") == '1') echo " checked='checked'"; ?> /> <?php _e("Quitar classes de los menús.", "especialista_wp_comprimir"); ?></br></br>
		<input type="checkbox" name="_especialistas_wp_text" value="1"<?php if(get_option("_especialistas_wp_text") == '1') echo " checked='checked'"; ?> /> <?php _e("Quitar 'text/css' y 'text/javascript'.", "especialista_wp_comprimir"); ?></br></br>
		<input type="checkbox" name="_especialistas_wp_domain" value="1"<?php if(get_option("_especialistas_wp_domain") == '1') echo " checked='checked'"; ?> /> <?php _e("Quitar dominio de los enlaces y llamadas.", "especialista_wp_comprimir"); ?></br></br>
		<input type="checkbox" name="_especialistas_wp_comments" value="1"<?php if(get_option("_especialistas_wp_comments") == '1') echo " checked='checked'"; ?> /> <?php _e("Quitar comentarios.", "especialista_wp_comprimir"); ?></br></br>
		<input type="submit" name="send" class="button button-primary" value="<?php _e("Guardar", "especialista_wp_comprimir"); ?>" />
	</form>
	<?php
}

?>
