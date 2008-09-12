<?php
/*
Plugin Name: Sudoku
Plugin URI: http://www.bastien.caudan.net/wordpress/widgets/sudoku
Description: Widget permettant de générer des grilles de sudoku dans la sidebar.
Version: 1.5
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
			<?php sudoku_style(); ?>
			</style>
			<?php sudoku(); ?>
			
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

// fonctions pour le sudoku

/* script initial :  Tedheu */
/*********         fonctions                                      *************/
function initialisation(){
  global $Grille ,$Masque ,$loterie ,$collision;
  global $Reg, $Lgn, $Col;
  $loterie = "";
  $collision = 0;
  // iniatisation, grille et masque, d'abord initialisée à '0'
  for ($i=0; $i<=8; $i++){
    for ($j=0; $j<=8; $j++){
      $Grille[$i][$j] = "0";
      $Masque[$i][$j] = 0;
      $loterie = $loterie.chr(97+$i).chr(48+$j);
    }
  }
  // Au début toutes les positions sont possibles
  for ($n=0; $n<=8; $n++){
    for ($m=0; $m<=8; $m++){
      $Reg[$n][$m] = "012345678";
      $Lgn[$n][$m] = "012345678";
      $Col[$n][$m] = "012345678";
    }
  }
}
//
function elimination($i,$j,$m){
  global $Grille ,$Masque ,$loterie ,$collision;
  global $Reg, $Lgn, $Col;
  // case $i,$j , chiffre= $m+1
  // élimination du chiffre en mettant les positions possibles à rien (= "")
  $n = floor($i/3)*3+floor($j/3); // région concernée numéro $n
  $Reg[$n][$m] = "";
  $n = $i; // ligne concernée numéro $n
  $Lgn[$n][$m] = "";
  $n = $j; // Colonne concernée numéro $n
  $Col[$n][$m] = "";
  // élimination sélective des positions possibles  dans les régions voisines
  for($n=0; $n<=8; $n++){
    for ($p=0; $p<=8; $p++){
      // $p est la position d'une case dans une région
      // position de la case en coordonnées 'grille', $ic,$jc
      $ic = floor($n/3)*3+floor($p/3);
      $jc = ($n-floor($n/3)*3)*3+($p-floor($p/3)*3);
      if ($ic==$i OR $jc==$j){
        $posi = Chr(48+$p);
        $Reg[$n][$m] = str_replace($posi,"",$Reg[$n][$m]);
      }
    }
  }
  // élimination sélective des positions possibles  dans les lignes voisines
  $posi = chr(48+$j);
  for($n=0; $n<=8; $n++){
    $Lgn[$n][$m] = str_replace($posi,"",$Lgn[$n][$m]);
  }
  // élimination sélective des positions possibles  dans les colonnes voisines
  $posi = chr(48+$i);
  for($n=0; $n<=8; $n++){
    $Col[$n][$m] = str_replace($posi,"",$Col[$n][$m]);
  }
  // élimination sélective des positions possibles, lignes-colonnes/région
  $n = floor($i/3)*3+floor($j/3); // région concernée numéro $n
  for($p=0; $p<=8; $p++){
    // position de la case en coordonnées 'grille', $ic,$jc
    $ic = floor($n/3)*3+floor($p/3);
    $jc = ($n-floor($n/3)*3)*3+($p-floor($p/3)*3);
    $posi = chr(48+$jc);
    $Lgn[$ic][$m] = str_replace($posi,"",$Lgn[$ic][$m]);
    $posi = chr(48+$ic);
    $Col[$jc][$m] = str_replace($posi,"",$Col[$jc][$m]);
  }
  // élimination de la position occupée quelque soit le chiffre
  for($mc=0; $mc<=8; $mc++){
    // régions (la région concernée, numéro $n)
    $n = floor($i/3)*3+floor($j/3);
    $p = ($i-floor($i/3)*3)*3+($j-floor($j/3)*3);
    $posi = chr(48+$p);
    $Reg[$n][$mc] = str_replace($posi,"",$Reg[$n][$mc]);
    // lignes
    $posi = chr(48+$j);
    $Lgn[$i][$mc] = str_replace($posi,"",$Lgn[$i][$mc]);
    // colonnes
    $posi = chr(48+$i);
    $Col[$j][$mc] = str_replace($posi,"",$Col[$j][$mc]);
  }
}
//
function affectation($i,$j,$m,$D){
  global $Grille ,$Masque ,$loterie ,$collision;
  if ($Grille[$i][$j]== "0"){
    $Grille[$i][$j]= chr(48+$m+1);
    $posi = chr(97+$i).chr(48+$j);
    $loterie = str_replace($posi,"",$loterie);
    elimination($i,$j,$m);
    if ($D== "1") $Masque[$i][$j]= 1;
  }
  else{
    $collision++;
  }
}

function sudoku(){
/*********         les variables                                  *************/
global $Grille ,$Masque ,$loterie ,$collision;
global $Reg, $Lgn, $Col;
// grille 'SuDoku', caractères de "1" à "9", case $i,$j
$Grille = array();
// masque de la grille: 0 = non masquable, 1 = la case peut être masquée
$Masque = array();
// loterie est une chaine de caractères pour le tirage des cases restantes
$loterie = "";
// variable collision pour tester le cas où on tirerais la même case
$collision = 0; 
// la grille est divisée en 9 régions, 9 lignes et 9 colonnes
// les tableaux $Reg,$Lgn,$Col sont indexés 'numéro,chiffre' ($n,$m m=chiffre-1)
// les valeurs sont des chaines de caractères indiquant les positions possibles
$Reg = array();
$Lgn = array();
$Col = array();
//

/*********         boucle                                         *************/
$tentative = 0;
$tentative_max = 100;
initialisation();
while (strlen($loterie)>0 AND $tentative<$tentative_max){
$tentative++;
initialisation();
$iter = 0;
$itermax = 82;
while ($loterie<>"" AND $iter<$itermax){
  $iter++;
  // placement déterministe d'un chiffre dans une case
  $bingo = 0;
  for ($n=0; $n<=8; $n++){
    for ($m=0; $m<=8; $m++){
      // en premier les régions
      if (strlen($Reg[$n][$m])== 1){
        $p = ord($Reg[$n][$m])-48;
        $i = floor($n/3)*3+floor($p/3);
        $j = ($n-floor($n/3)*3)*3+($p-floor($p/3)*3);
        affectation($i,$j,$m,"1");
        $bingo = 1;
        break;
      }
      // en second les lignes
      if (strlen($Lgn[$n][$m])== 1){
        $p = ord($Lgn[$n][$m])-48;
        $i = $n;
        $j = $p;
        affectation($i,$j,$m,"1");
        $bingo = 1;
        break;
      }
      // en troisième les colonnes
      if (strlen($Col[$n][$m])== 1){
        $p = ord($Col[$n][$m])-48;
        $i = $p;
        $j = $n;
        affectation($i,$j,$m,"1");
        $bingo = 1;
        break;
      }
    }
    if ($bingo== 1) break;
  }
  // placement par tirage au sort, si le placement déterministe n'a pas abouti
  if ($bingo== 0){
    // tirage d'une case $i,$j parmis les cases libres
    $ncase2 = strlen($loterie);
    $ncase = strlen($loterie)/2;
    $index = rand(0,$ncase-1)*2;
    $posi = substr($loterie,$index,2);
    // détermination des coordonnées de la case: $i,$j
    $i = ord(substr($posi,0))-97;
    $j = ord(substr($posi,1))-48;
    // tirage d'un chiffre parmis les chiffres libres
    $liste = "";
    for ($m=0; $m<=8; $m++){
      $libre = 1;
      // régions
      $n = floor($i/3)*3+floor($j/3);
      if ($Reg[$n][$m]== "") $libre = 0;
      // lignes
      $n = $i;
      if ($Lgn[$n][$m]== "") $libre = 0;
      // colonnes
      $n = $j;
      if ($Col[$n][$m]== "") $libre = 0;
      // concaténation
      if ($libre== 1) $liste = $liste.chr(48+$m);
    }
    if (strlen($liste)>0){
      $m = ord(substr($liste,floor(rand(0,strlen($liste)-1))))-48;
      affectation($i,$j,$m,"0");
    }
  }
}
}
// Affichage de la grille
switch ($niv)
{
	case 1:
		$niv = 5;
	break;
	
	case 2:
		$niv = 10;
	break;
	
	case 3:
		$niv = 60;
	break;
			
	default:
		$niv = 10;
	break;
}

$nalea = 0;
$count = 0;
?>
<div id="sudoku">
<?php
  for ($ri=0; $ri<=2; $ri++){?>
	<div class="reglig">
  <?php
    for ($rj=0; $rj<=2; $rj++){?>
		<div class="region">
  <?php
        for ($ii=0; $ii<=2; $ii++){?>
      <div class="ligne">
  <?php
          for ($jj=0; $jj<=2; $jj++){
            // calcul de ($i,$j)
            $count ++;
            $i = $ri*3+$ii;
            $j = $rj*3+$jj;
            If ($Grille[$i][$j]== "0"){?>
       	<div class="casedeb"><?php echo $Grille[$i][$j];?></div>
   <?php    }
            else{
              If ($Masque[$i][$j]==  1){
              	if($count % $niv){?>
       	<div class="case"><input type="text" autocomplete="off" value=""/></div>
   <?php 				} else {?>
       	<div class="casedeb"><?php echo $Grille[$i][$j];?></div>
   <?php 				}
              }
              else{
                $nalea++;?>
       	<div class="casedeb"><?php echo $Grille[$i][$j];?></div>
   <?php 			}
            }
          }?>
       	</div>
   <?php } ?>
  	</div>
  <?php
    }?>
  </div>

  <?php
  }
?>
</div>
<?php
}

function sudoku_style(){
?>
#sudoku{
text-align: center;
}

#sudoku .reglig{
height: 31%;
}

#sudoku .region{
width: 32%;
float: left;
height: 100%;
margin: 0;
padding: 0;
}


#sudoku .ligne{
width: 100%;
height: 29%;
margin: 0;
padding: 0;
}

#sudoku .casedeb, #sudoku .case{
border-width: 1px;
border-style: solid;
float: left;
width: 28%;
height: 100%;
margin: 0;
padding: 0;
font-weight: bold;
font-size: inherit;
}

#sudoku .case input {
border: none;
background-color: transparent;
width: 98%;
height: 98%;
text-align: center;
margin: 0;
padding: 0;
font-weight: normal;
font-size: inherit;
}


<?php
}

?>
