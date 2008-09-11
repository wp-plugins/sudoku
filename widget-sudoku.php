<?php
/*
Plugin Name: Sudoku
Plugin URI: http://www.bastien.caudan.net/wordpress/widgets/sudoku
Description: Widget permettant de générer des grilles de sudoku dans la sidebar.
Version: 1.4
Author: Bastien Caudan
Author URI: http://www.bastien.caudan.net/
*/
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2, 
    as published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

/* Execution du widget */
function widget_sudoku($args, $widget_args = 1) {
	/* chargement des paramètres */
	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('widget_sudoku');
	if ( !isset($options[$number]) )
		return;

	$title = $options[$number]['title'];
	$niv = $options[$number]['niv'];
	$width = $options[$number]['width'];
	$height = $options[$number]['height'];
	$color = $options[$number]['color'];
	$bcolor = $options[$number]['bcolor'];
	$marge = $options[$number]['marge'];
	$font_size = $options[$number]['font_size'];
?>
		<?php echo $before_widget; ?>
			<?php if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<style type="text/css">
#sudoku {
	margin-left: <?php echo $marge;?>px;
	font-size: <?php echo $font_size;?>pt;
	height: <?php echo $height;?>px;
	width: <?php echo $width;?>px;
	color: <?php echo $color;?>;
}

#sudoku .case input {
	color: <?php echo $color;?>;
}

#sudoku .casedeb, #sudoku .case{
	border-color: <?php echo $bcolor;?>;
}			
			<?php include('./wp-content/plugins/sudoku/sudoku.css'); ?>
			</style>
			<?php include('./wp-content/plugins/sudoku/sudoku.php'); ?>
			
			<!-- Merci de laisser ce lien si vous appréciez ce widget, 
					n'hésitez pas à suggérer des améliorations -->
			<small>Widget par <a href="http://www.bastien.caudan.net/" target="_blank">Bastien Caudan</a></small>
			
		<?php echo $after_widget; ?>
<?php
}

/* interface admin du widget */
function widget_sudoku_control($widget_args) {
	global $wp_registered_widgets;
	static $updated = false;

	/* chargement des paramètres */
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('widget_sudoku');
	if ( !is_array($options) )
		$options = array();

	if ( !$updated && !empty($_POST['sidebar']) ) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) {
			if ( 'widget_sudoku' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				unset($options[$widget_number]);
			}
		}

		/* Sauvegarde des paramètres */
		foreach ( (array) $_POST['widget-sudoku'] as $widget_number => $widget_text ) {
			$title = strip_tags(stripslashes($widget_text['title']));
			$niv = strip_tags(stripslashes($widget_text['niv']));
			$width = strip_tags(stripslashes($widget_text['width']));
			$height = strip_tags(stripslashes($widget_text['height']));
			$color = strip_tags(stripslashes($widget_text['color']));
			$bcolor = strip_tags(stripslashes($widget_text['bcolor']));
			$marge = strip_tags(stripslashes($widget_text['marge']));
			$font_size = strip_tags(stripslashes($widget_text['font_size']));
			$options[$widget_number] = compact( 'title', 'niv', 'width', 'height', 'marge', 'color', 'bcolor', 'font_size');
		}

		update_option('widget_sudoku', $options);
		$updated = true;
	}

	if ( -1 == $number ) {
		$title = 'Sudoku';
		$niv = 2;
		$marge = 0;
		$height = 200;
		$width = 200;
		$font_size = 11;
		$color = '#000';
		$bcolor = '#999';
		$number = '%i%';
	} else {
		$title = attribute_escape($options[$number]['title']);
		$niv = attribute_escape($options[$number]['niv']);
		$width = attribute_escape($options[$number]['width']);
		$height = attribute_escape($options[$number]['height']);
		$font_size = attribute_escape($options[$number]['font_size']);
		$color = attribute_escape($options[$number]['color']);
		$bcolor = attribute_escape($options[$number]['bcolor']);
		$marge = attribute_escape($options[$number]['marge']);
	}
?>
		<p>
			<label for="sudoku-title-<?php echo $number; ?>">Titre&nbsp;:&nbsp;</label>
			<input class="widefat" id="sudoku-title-<?php echo $number; ?>" name="widget-sudoku[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" />
			
			<br/><br/>
			
			<label for="sudoku-niv">Niveau de difficulté&nbsp;:&nbsp;</label>
			<select id="sudoku-niv" name="widget-sudoku[<?php echo $number; ?>][niv]">
			<?php for($i=1;$i<=3;$i++) { 
							if ($i == $niv) {
								echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
							} else {
								echo '<option value="'.$i.'">'.$i.'</option>';
							}
						} ?>
			</select>
			
			<br/>
			
			<p>Grille minimum 150x150</p>
			
			
			<label>Largeur de la grille&nbsp;:&nbsp;<input type="text" size="4" name="widget-sudoku[<?php echo $number; ?>][width]" value="<?php echo $width;?>"/>px</label>
			
			
			<br/>

			<label>Hauteur de la grille&nbsp;:&nbsp;<input type="text" size="4" name="widget-sudoku[<?php echo $number; ?>][height]" value="<?php echo $height;?>"/>px</label>
			
	
			<br/>
			
			<label>Marge gauche de la grille&nbsp;:&nbsp;<input type="text" size="4" name="widget-sudoku[<?php echo $number; ?>][marge]" value="<?php echo $marge;?>"/>px</label>
			
	
			<br/>

			<label>Couleur de la grille&nbsp;:&nbsp;<input type="text" size="7" name="widget-sudoku[<?php echo $number; ?>][bcolor]" value="<?php echo $bcolor;?>"/></label>
					
			<br/><br/>
			
			<label>Hauteur des chiffres&nbsp;:&nbsp;<input type="text" size="2" name="widget-sudoku[<?php echo $number; ?>][font_size]" value="<?php echo $font_size;?>"/>pt</label>
			
			<br/>
			
			<label>Couleur des chiffres&nbsp;:&nbsp;<input type="text" size="7" name="widget-sudoku[<?php echo $number; ?>][color]" value="<?php echo $color;?>"/></label>
			<br/><br/>			
			<!-- name = "widget-sudoku[numéro du widget][param] -->
			<input type="hidden" id="sudoku-submit-<?php echo $number; ?>" name="sudoku-submit-<?php echo $number; ?>" value="1" />
		</p>
<?php
}

/* chargement du widget */
function widget_sudoku_register() {

	// API nécessaires
	if ( !function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control') )
		return;

	if ( !$options = get_option('widget_sudoku') )
		$options = array();
	$widget_ops = array('classname' => 'widget_sudoku', 'description' => 'Widget permettant de générer des grilles de sudoku dans la sidebar');
	$control_ops = array('width' => 300, 'height' => 300, 'id_base' => 'sudoku');
	$name = 'Sudoku';

	$id = false;
	/* Pour chaque widget */
	foreach ( array_keys($options) as $o ) {
		// Vérifications d'un option
		if ( !isset($options[$o]['title']) )
			continue;
		$id = "sudoku-$o"; 
		/* chargement de chaque widget avec son numéro */
		wp_register_sidebar_widget($id, $name, 'widget_sudoku', $widget_ops, array( 'number' => $o ));
		wp_register_widget_control($id, $name, 'widget_sudoku_control', $control_ops, array( 'number' => $o ));
	}
	
	/* Si pas encore de widget on charge le premier */
	if ( !$id ) {
		wp_register_sidebar_widget( 'sudoku-1', $name, 'widget_sudoku', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'sudoku-1', $name, 'widget_sudoku_control', $control_ops, array( 'number' => -1 ) );
	}
	
}

add_action( 'widgets_init', 'widget_sudoku_register' );

?>
