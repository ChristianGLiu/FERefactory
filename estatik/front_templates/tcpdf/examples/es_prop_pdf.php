<?php
//============================================================+
// File name   : example_006.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 006 for TCPDF class
//               WriteHTML and RTL support
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+
/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: WriteHTML and RTL support
 * @author Nicola Asuni
 * @since 2008-03-04
 */
// Include the main TCPDF library (search for installation path).
//echo PATH_DIR.'front_templates/tcpdf/examples/tcpdf_include.php';
//include(PATH_DIR.'front_templates/tcpdf/examples/tcpdf_include.php');
$es_settings = es_front_settings();
ob_start();
			global $wpdb;
			$sql = 'SELECT * FROM '.$wpdb->prefix.'estatik_properties WHERE prop_id = '.$_GET["pdf"].' order by prop_id desc';
			$es_prop_single_result = $wpdb->get_results($sql); 
			
			$es_prop_single = $es_prop_single_result[0];
			
			//print_r($es_prop_single);
			
			$prop_cat = new stdClass;
			$prop_cat_val = $wpdb->get_results( 'SELECT cat_title FROM '.$wpdb->prefix.'estatik_manager_categories WHERE cat_id = '.$es_prop_single->prop_category);
			$prop_cat = $prop_cat_val[0];
			
			$prop_type = new stdClass;
			$prop_type_val = $wpdb->get_results( 'SELECT type_title FROM '.$wpdb->prefix.'estatik_manager_types WHERE type_id = '.$es_prop_single->prop_type);
			$prop_type = $prop_type_val[0];	
			
			$prop_status = new stdClass;
			$prop_status_val = $wpdb->get_results( 'SELECT status_title FROM '.$wpdb->prefix.'estatik_manager_status WHERE status_id = '.$es_prop_single->prop_status);
			$prop_status = $prop_status_val[0];	
			
			if(!empty($es_settings->default_currency)) {
				$prop_currency = new stdClass;
				$prop_currency_val = $wpdb->get_results( 'SELECT currency_title FROM '.$wpdb->prefix.'estatik_manager_currency WHERE currency_title = '.$es_settings->default_currency);
				$prop_currency = $prop_currency_val[0];	
				$currency_sign_ex = explode(",", $es_settings->default_currency);
				if(count($currency_sign_ex)==1){
					$currency_sign = $currency_sign_ex[0];
				}else {
					$currency_sign = $currency_sign_ex[1];	
				}
			}else{
				$currency_sign = "$";
			} 
			
				$es_prop_neigh = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_properties_neighboarhood WHERE prop_id='.$es_prop_single->prop_id );
				
			 	
				
				$es_prop_features = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_properties_features WHERE prop_id='.$es_prop_single->prop_id );	
				$es_prop_appliances = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_properties_appliances WHERE prop_id='.$es_prop_single->prop_id );	
 
				 
				$prop_type = new stdClass;
				$prop_type_val = $wpdb->get_results( 'SELECT type_title FROM '.$wpdb->prefix.'estatik_manager_types WHERE type_id = '.$es_prop_single->prop_type);
				$prop_type = $prop_type_val[0];
				
				$prop_status = new stdClass;
				$prop_status_val = $wpdb->get_results( 'SELECT status_title FROM '.$wpdb->prefix.'estatik_manager_status WHERE status_id = '.$es_prop_single->prop_status);
				$prop_status = $prop_status_val[0];
				
				$image_sql = "SELECT prop_meta_value FROM ".$wpdb->prefix."estatik_properties_meta WHERE prop_id = ".$es_prop_single->prop_id." AND prop_meta_key = 'images'";
				$uploaded_images = $wpdb->get_results($image_sql);
				
				$uploaded_images_obj = "";
				
				$uploaded_images_obj = $uploaded_images[0];
				
				if($uploaded_images_obj!=""){
 
					$upload_image_data = unserialize($uploaded_images_obj->prop_meta_value);
					$upload_dir = wp_upload_dir();
 
					$prop_image = $upload_image_data[0];
					$es_settings = es_front_settings();
					$single_left_image_name = end(explode("/",$prop_image));
					$single_left_image_path = str_replace($single_left_image_name,"",$prop_image);
					$image_url = $single_left_image_path.'single_center_'.$single_left_image_name;
				}
				
				 
$price_format = explode("|",$es_settings->price_format);
$price = '';      
if($es_settings->currency_sign_place=='before'){ 
    $price .= $currency_sign ; 
}
$price .= number_format($es_prop_single->prop_price,
    $price_format[0],
    $price_format[1],
    $price_format[2]);
if($es_settings->currency_sign_place=='after'){ 
  $price .= $currency_sign; 
}
require_once('tcpdf_include.php');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Estatik');
$pdf->SetTitle($es_prop_single->prop_title);
$pdf->SetSubject('Estatik');
$pdf->SetKeywords('TCPDF');
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    ob_end_clean();
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}
// ---------------------------------------------------------
$pdf->setFontSubsetting(true);
// set font
$pdf->SetFont('freeserif', '', 12);
// add a page
$pdf->AddPage();
// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
// create some HTML content
$html = '<table cellpadding="5" cellspacing="0" style="font-size:12px;">
 	<tr>
    	<td colspan="2"><h1>'.$es_prop_single->prop_address.'</h1> </td>
    </tr>
	<tr>
    	<td colspan="2"><img src="'.$upload_dir['baseurl'].$image_url.'" alt="" /></td>
    </tr>
	<tr>
    	<td><strong style="font-size:18px">' . __('Category', 'es-plugin') . ': </strong> '.$prop_cat->cat_title.'</td>
		<td><strong style="font-size:18px">' . __('Price', 'es-plugin') . ': </strong> '.$price.'</td>
    </tr>
	<tr>
    	<td colspan="2"><hr></td>
    </tr>
	<tr>
    	<td colspan="2"><h2 style="font-size:18px">' . __('Basic Facts', 'es-plugin') . '</h2> </td>
    </tr>
	<tr>
    	<td><strong>' . __('Date added', 'es-plugin') . ': </strong>'.date("d/m/Y",$es_prop_single->prop_date_added).'</td>
		<td><strong>' . __('Area size', 'es-plugin') . ': </strong>'.$es_prop_single->prop_area.'</td>
    </tr>
	<tr>
    	<td><strong>' . __('Lot size', 'es-plugin') . ': </strong>'.$es_prop_single->prop_lotsize.'</td>
		<td><strong>' . __('Type', 'es-plugin') . ': </strong>'.$prop_type->type_title.'</td>
    </tr>
	<tr>
    	<td><strong>' . __('Status', 'es-plugin') . ': </strong>'.$prop_status->status_title.'</td>
		<td><strong>' . __('Bedrooms', 'es-plugin') . ': </strong>'.$es_prop_single->prop_bedrooms.'</td>
    </tr>
	<tr>
    	<td><strong>' . __('Bathrooms', 'es-plugin') . ': </strong>'.$es_prop_single->prop_bathrooms.'</td>
		<td><strong>' . __('Floors', 'es-plugin') . ': </strong>'.$es_prop_single->prop_floors.'</td>
    </tr>
	<tr>
    	<td><strong>' . __('Built In', 'es-plugin') . ': </strong>'.$es_prop_single->prop_builtin.'</td>
		<td></td>
    </tr>
	<tr>
    	<td colspan="2"><hr></td>
    </tr>
	<tr>
    	<td colspan="2"><h2 style="font-size:18px">' . __('Description', 'es-plugin') . ': </h2> </td>
    </tr>
	<tr>
    	<td colspan="2">'.$es_prop_single->prop_description.'</td>
    </tr>';
		if(!empty($es_prop_neigh)){
	$html .= '
		<tr>
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td colspan="2"><h2 style="font-size:18px">' . __('Neighbourhood', 'es-plugin') . ': </h2> </td>
		</tr>
	';
			foreach($es_prop_neigh as $prop_neigh) {
				$es_prop_neigh_reult = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_manager_neighboarhood WHERE neigh_id='.$prop_neigh->neigh_id );
				$es_prop_neigh_val = $es_prop_neigh_reult[0];
				$html .= '<tr>
			<td colspan="2">
					<strong>'.$es_prop_neigh_val->neigh_title.': </strong>'.$prop_neigh->neigh_distance.'
				</td></tr>';
			}
		}
		if(!empty($es_prop_features) || !empty($es_prop_appliances)){
			$html .= '
			<tr>
				<td colspan="2"><hr></td>
			</tr>
			<tr><td colspan="2"><h2 style="font-size:18px">' . __('Features', 'es-plugin') . ': </h2> </td></tr><tr>';
			if(!empty($es_prop_features)){
				$html .= '<td><h3>' . __('Features', 'es-plugin') . ': </h3>';
				foreach($es_prop_features as $es_prop_feature) {
					 $es_prop_feature_result = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_manager_features WHERE feature_id='.$es_prop_feature->feature_id );
                     $es_prop_feature_val = $es_prop_feature_result[0];
					$html .= ''.'<img src="'.DIR_URL.'front_templates/images/es_feature_tick.png" alt="#"  />  '.$es_prop_feature_val->feature_title.'<br>';
				}
				$html .= '</td>';
			}
			if(!empty($es_prop_appliances)){
				$html .= '<td><h3>' . __('Amenities', 'es-plugin') . ': </h3>';
				foreach($es_prop_appliances as $es_prop_appliance) {
					 $es_prop_appliance_result = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_manager_appliances WHERE appliance_id='.$es_prop_appliance->appliance_id );
                     $es_prop_appliance_val = $es_prop_appliance_result[0];
					$html .= ''.'<img src="'.DIR_URL.'front_templates/images/es_feature_tick.png" alt="#"  />  '.$es_prop_appliance_val->appliance_title.'<br>';
				}
				$html .= '</td>';
			}
			$html .= '</tr>';
		}
$html .= '</table>';
                   
 
  
// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');
// ---------------------------------------------------------
//Close and output PDF document
ob_end_clean();
$pdf->Output($es_prop_single->prop_title.'.pdf', 'I');
//============================================================+
// END OF FILE
//============================================================+
