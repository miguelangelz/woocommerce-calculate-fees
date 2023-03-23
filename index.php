<?php
/*
 * Plugin Name:       Woocommerce Calculate Fees
 * Plugin URI:        https://wordpress.org/plugins/woocommerce-calculate-fees/
 * Description:       Crea una calculadora de cuotas
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Miguel Zhañay
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       woocommerce-calculate-fees
 * Domain Path:       /languages
 */
 

 if ( !defined( 'ABSPATH' ) ) exit;

register_activation_hook( __FILE__, 'woo_cf_createTablea' );
register_uninstall_hook(__FILE__, 'woo_cf_deleteTable' );
 
//hola
function woo_cf_createTablea() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'woo_cf_woo_cf_calcular_cuotas';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		meses int NOT NULL,
		interes float(4,2) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}
function woo_cf_deleteTable()
{
 //obtenemos el objeto $wpdb
    global $wpdb;
 
    //el nombre de la tabla, utilizamos el prefijo de wordpress
    $table_name = $wpdb->prefix . 'woo_cf_calcular_cuotas';
 
    //sql con el statement de la tabla
    $sql = "DROP table IF EXISTS $table_name";
 
 $wpdb->query($sql);
}
// Pinta el select en la página del producto
add_action('woocommerce_before_add_to_cart_form', 'woo_cf_priceByFees', 0);

function estilos() {
    wp_enqueue_style( 'materialize', plugins_url('/assets/css/materialize.min.css', __FILE__),true );
	wp_enqueue_style( 'iconos', plugins_url('/assets/css/icon.css', __FILE__),true );
	wp_enqueue_style( 'estilos', plugins_url('/assets/css/estilos.css', __FILE__),true );
	wp_enqueue_script( 'material', plugins_url( '/assets/js/materialize.min.js', __FILE__ ), true );
}

add_action('admin_enqueue_scripts','estilos' );

function ajax_test_enqueue_scripts() {
	wp_enqueue_script( 'test', plugins_url( '/assets/js/calculate.js', __FILE__ ), array('jquery') );
	wp_localize_script('test', 'woo_cf_nonce',  array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'security' => wp_create_nonce( 'my_ajax_nonce' )));
  }

// 1. Encolamos nuestro script
add_action( 'wp_enqueue_scripts', 'ajax_test_enqueue_scripts' );

// Hook para usuarios no logueados
add_action('wp_ajax_nopriv_woo_cf_buttonCalculator', 'woo_cf_buttonCalculator');
// Hook para usuarios logueados
add_action('wp_ajax_woo_cf_buttonCalculator', 'woo_cf_buttonCalculator');





/* if ( ! wp_verify_nonce(  $_REQUEST['nonce'], 'woo_cf_nonce' ) ) {
	wp_die("Error - Verificación nonce no válida ✋");
} */

add_action('admin_menu', 'menu');
function menu() {
	if (function_exists('add_menu_page')) {
		add_menu_page(
		 'Calcular Cuota',
		 'Calcular Cuota',
		'administrator', 
		'woocommerce-calculate-feeds/months.php',
		'', 'dashicons-admin-generic');
	}
	
}


add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'salcode_add_plugin_page_settings_link');
function salcode_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'admin.php?page=woocommerce-calcular-cuotas/meses.php' ) .
		'">' . __('Settings') . '</a>';
	return $links;
}
function woo_cf_priceByFees() {

$precio = get_post_meta( get_the_ID(), '_sale_price', true);
$precio1 = get_post_meta( get_the_ID(), '_regular_price', true);

global $wpdb;
$query = $wpdb->get_results ( "SELECT * FROM {$wpdb->prefix}woo_cf_calcular_cuotas ORDER BY meses ASC" );
if($precio){?>
<h2>Calcula tus cuotas</h2>


<form>
<select name="combo" id="combo">
    <option value="0">Seleccione meses</option>
    	<?php
    foreach ( $query as $imprimir )   {
    ?>
	<option name="<?= $precio ?>" value="<?= $imprimir->id ?>"><?= $imprimir->meses ?></option>
	<?php }?>  
</select>
</form>
<h2>Costo de cuota : <strong id="txtMessage"></strong></h2>
	<?php 
}else if($precio1){?>
  <h2>Calcula tus cuotas</h2>
  
<form>
<select name="combo" id="combo">
    <option value="0">Seleccione meses</option>
    	<?php
    foreach ( $query as $imprimir )   {
    ?>
	<option name="<?= $precio1 ?>" value="<?= $imprimir->id ?>"><?= $imprimir->meses ?></option>
	<?php }?>  
</select>
</form>
<h2>Costo de cuota : <strong id="txtMessage"></strong></h2>
	<?php 
}
}	

function woo_cf_buttonCalculator(){
 global $wpdb;

        if(isset($_GET['id']) && isset($_GET['precio']))
        {
    
        $id = sanitize_text_field($_GET['id']);
        $precio = sanitize_text_field($_GET['precio']);
    
$resultado = $wpdb->get_results ( "SELECT * FROM {$wpdb->prefix}woo_cf_calcular_cuotas WHERE id = '$id'" );
    
    foreach ( $resultado as $imprimir )   {
        $mes = $imprimir->meses;
        $interes = $imprimir->interes;
        $intereses = ($interes/100)+1;
        $cal = $precio * $intereses;
	    $res = $cal / $mes;
	     printf ("%.2f", $res); 
        die();
}
}
}
?>