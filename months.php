<?php 
if ( !defined( 'ABSPATH' ) ) exit;

if (! current_user_can ('administrator')) {
	die ('Acceso denegado');
}
if(isset($_POST['Submit'])){
	//valida si lo hacemoscon javascript

	global $wpdb;

	$mes = sanitize_text_field($_POST['mes']);
	$interes = sanitize_text_field($_POST['interes']);
	$table_name = $wpdb->prefix . 'woo_cf_calcular_cuotas';
	if ($wpdb->insert(
		$table_name,
			array(
				'meses' => $mes,
				'interes' => $interes
			)
		) == false)
		 {
		wp_die('falló la inserción a la base de datos');
	}else{
		echo "<script>location.reload();</script>";
	}
}
else {
 ?>

         <div class="container" style="width:50%">
 <div class="col s5"></div>
 <div class="col s2">
     <h3>Configuration of months and Interest</h3>
     <h5>Enter the number of months and the interest percentage</h5>
 <form action="" method="POST" id="add">
 	<p><laber>Months</laber>
 		<input type="number" step="0.01" name="mes">
 	</p>
 	<p><laber>Interest</laber>
 		<input type="number" step="0.01" name="interes">
 	</p>
 <p>
 		<input type="submit" id="agregar" name="Submit" value="Add Fee" class="waves-effect waves-light btn">
 	</p>
 </form>
        <table class="highlight">
  <tr>
    <th>Months</th>
    <th>Interest</th>
    <th>Delete</th>
  </tr>
      <?php
      $query = $wpdb->get_results ( "SELECT * FROM {$wpdb->prefix}woo_cf_calcular_cuotas ORDER BY meses ASC" );
      foreach($query as $yupi) {?>
      <tr>
          <form method="POST" action="">
    <td><?= $yupi->meses ?></td>
    <td><?= intval($yupi->interes )?> %</td> 
    <input type="hidden" value="<?= $yupi->interes ?>" name="interes" readonly>
    <input type="hidden" value="<?= $yupi->meses ?>" name="mes" readonly>
        <input type="hidden" value="<?= $yupi->id ?>" name="id">
    <td><button type="submit" class="btn-floating btn-small waves-effect waves-light red" name="submit"><i class="material-icons right">delete</i></button></td>
    </form>
  </tr>
 <?php } ?>
</table>
</div>
<div class="col s5"></div>
</div>
 <?php 
 	} 
if(isset($_POST['id'])){
$id = sanitize_text_field($_POST['id']);
$table = $wpdb->prefix . 'woo_cf_calcular_cuotas';
$wpdb->delete( $table, array( 'id' => $id ) );
echo "<script>location.reload();</script>";
}
?>