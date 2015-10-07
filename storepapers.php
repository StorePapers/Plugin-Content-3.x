<?php
 
 /**
 *
 * Plugin StorePapers for Joomla! 3.4+
 * Version: 1.5
 *
 * Copyright (C) 2008-2015  Francisco Ruiz (contact@storepapers.com)
 *
 * StorePapers is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *
 * StorePapers is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */

//Comprobación de seguridad en Joomla!
defined( '_JEXEC' ) or die;
jimport( 'joomla.plugin.plugin' );

class plgContentStorepapers extends JPlugin{	
	
	public function plgContentStorepapers( &$subject, $params )
	{
		parent::__construct( $subject, $params );		
	}
	
	public function onContentPrepare($context, &$article, &$params, $page = 0){	
	
		global $mainframe;		
		
		$this->botStorepapers(true, $article, $params, $page = 0);
 	}

	private function botStorepapers($published, &$row, &$params, $page=0) {
		
		if(!$published) {
			$row->text = preg_replace('/{storepapersauthorcategory:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersauthorcategory:.+?:.+?:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersauthorcategory:.+?:.+?:.+?:.+?:.+?:.+?}/', '', $row->text);
			
			$row->text = preg_replace('/{storepapersauthorcategorypriority:.+?:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersauthorcategorypriority:.+?:.+?:.+?:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersauthorcategorypriority:.+?:.+?:.+?:.+?:.+?:.+?:.+?}/', '', $row->text);
			
			$row->text = preg_replace('/{storepapersauthorcategoryyear:.+?:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersauthorcategoryyear:.+?:.+?:.+?:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersauthorcategoryyear:.+?:.+?:.+?:.+?:.+?:.+?:.+?}/', '', $row->text);
			
			$row->text = preg_replace('/{storepaperscategory:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepaperscategory:.+?:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepaperscategory:.+?:.+?:.+?:.+?:.+?}/', '', $row->text);
			
			$row->text = preg_replace('/{storepapersyearcategory:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersyearcategory:.+?:.+?:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersyearcategory:.+?:.+?:.+?:.+?:.+?:.+?}/', '', $row->text);
			
			$row->text = preg_replace('/{storepaperspublication:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepaperspublication:.+?:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepaperspublication:.+?:.+?:.+?:.+?:.+?}/', '', $row->text);
			
			// Versión 1.22
			$row->text = preg_replace('/{storepapersauthorpublications:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersauthorpublications:.+?:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersauthorpublications:.+?:.+?:.+?:.+?:.+?}/', '', $row->text);
			
			$row->text = preg_replace('/{storepapersauthorpublicationspriority:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersauthorpublicationspriority:.+?:.+?:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepapersauthorpublicationspriority:.+?:.+?:.+?:.+?:.+?:.+?}/', '', $row->text);
			
			$row->text = preg_replace('/{storepaperspublications:}/', '', $row->text);
			$row->text = preg_replace('/{storepaperspublications:.+?:.+?}/', '', $row->text);
			$row->text = preg_replace('/{storepaperspublications:.+?:.+?:.+?:.+?}/', '', $row->text);
			return;
		}	

		$database = JFactory::getDBO();

		//Coger parametros de la BD
		$content = $row->text;
		$matches = array ();
		
		// Consigue ID´s de {storepapersauthorcategory: ID AUTOR : ID CATEGORIA} 
		// Como mucho puede tener 4 campos más, 6 en total.
		if ((preg_match_all('/{storepapersauthorcategory:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) || 
			(preg_match_all('/{storepapersauthorcategory:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) || 
			(preg_match_all('/{storepapersauthorcategory:.+?:.+?:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ) {

			//Get IDS
			foreach ($matches as $fmatch) {
				foreach ($fmatch as $match) {				
					$match = str_replace("{storepapersauthorcategory:", "", $match);				
					$arrayMatch = explode(':',$match);
					
					//Linea de prueba
					//$output = "Identificador autor: ".$arrayMatch[0].", Identificador categoria: ".$arrayMatch[1]." Campo 3: ".$arrayMatch[2]." Campo 4: ".$arrayMatch[3]." Número de campos: ".$numArrayMatch;
					
					$numArrayMatch = count($arrayMatch);
					//Elimino el parentesis final de la consulta
					if($numArrayMatch > 0)
						$arrayMatch[$numArrayMatch - 1] = strtok(($arrayMatch[$numArrayMatch - 1]), "}");
					
					$list = "DEFAULT";
					$orderbydate = "DESC";
					if($numArrayMatch >= 2){					
						for($i = 2; $i < $numArrayMatch; $i += 1){					
							switch ($arrayMatch[$i]) {
								case "LIST" 		:	if( ($i + 1) <  $numArrayMatch){
															$list = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;
								case "ORDERBYDATE"	:	if( ($i + 1) <  $numArrayMatch){
															$orderbydate = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;
							}
						}
						$output = $this->getAuthorCategory($arrayMatch[0], $arrayMatch[1], $list, $orderbydate);					
					}
					//Caso de error
					else{
						$output = "Error, StorePapers, storepapersauthorcategory.";
					}
					//Estas lineas se encargan de eliminar el nombre de la consulta del texto a visualizar.				
					$content = $this->quitarConsultaDelArticulo("/{storepapersauthorcategory", $arrayMatch, $numArrayMatch, $output, $content);				
				}
			}
			unset($matches);
		}
		
		// Consigue ID´s de {storepapersauthorcategorypriority : ID AUTOR : ID CATEGORIA : PRIORITY}
		// Como mucho puede tener 4 campos más, 7 en total.
		if ((preg_match_all('/{storepapersauthorcategorypriority:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepapersauthorcategorypriority:.+?:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepapersauthorcategorypriority:.+?:.+?:.+?:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ) {

			//Get IDS
			foreach ($matches as $fmatch) {
				foreach ($fmatch as $match) {
					$match = str_replace("{storepapersauthorcategorypriority:", "", $match);				
					$arrayMatch = explode(':',$match);
					
					//Linea de prueba
					//$output = "Identificador autor: ".$arrayMatch[0].", Identificador categoria: ".$arrayMatch[1]." Prioridad: ".$arrayMatch[2]." Campo 4: ".$arrayMatch[3]." Campo 5: ".$arrayMatch[4]." Número de campos: ".$numArrayMatch;
					
					$numArrayMatch = count($arrayMatch);
					//Elimino el parentesis final de la consulta
					if($numArrayMatch > 0)
						$arrayMatch[$numArrayMatch - 1] = strtok(($arrayMatch[$numArrayMatch - 1]), "}");
					
					$list = "DEFAULT";
					$orderbydate = "DESC";
					if($numArrayMatch >= 3){					
						for($i = 3; $i < $numArrayMatch; $i += 1){					
							switch ($arrayMatch[$i]) {
								case "LIST" 		:	if( ($i + 1) <  $numArrayMatch){
															$list = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;
								case "ORDERBYDATE"	:	if( ($i + 1) <  $numArrayMatch){
															$orderbydate = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;							
							}
						}					
						$output = $this->getAuthorCategoryPriority($arrayMatch[0], $arrayMatch[1], $arrayMatch[2], $list, $orderbydate);					
					}
					//Caso de error
					else{
						$output = "Error, StorePapers, storepapersauthorcategorypriority.";
					}
					//Estas lineas se encargan de eliminar el nombre de la consulta del texto a visualizar.				
					$content = $this->quitarConsultaDelArticulo("/{storepapersauthorcategorypriority", $arrayMatch, $numArrayMatch, $output, $content);
				}
			}
			unset($matches);
		}
		
		// Consigue ID´s de {storepaperscategory: ID CATEGORIA}
		// Como mucho puede tener 4 campos más, 5 en total.
		if ((preg_match_all('/{storepaperscategory:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepaperscategory:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepaperscategory:.+?:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ) {

			//Get IDS
			foreach ($matches as $fmatch) {
				foreach ($fmatch as $match) {				
					$match = str_replace("{storepaperscategory:", "", $match);				
					$arrayMatch = explode(':',$match);				
					
					$numArrayMatch = count($arrayMatch);
					//Elimino el parentesis final de la consulta
					if($numArrayMatch > 0)
						$arrayMatch[$numArrayMatch - 1] = strtok(($arrayMatch[$numArrayMatch - 1]), "}");
						
					//Linea de prueba
					//$output = "Identificador categoria: ".$arrayMatch[0]." Campo 2: ".$arrayMatch[1]." Campo 3: ".$arrayMatch[2];
					
					$list = "DEFAULT";
					$orderbydate = "DESC";
					if($numArrayMatch >= 1){					
						for($i = 1; $i < $numArrayMatch; $i += 1){					
							switch ($arrayMatch[$i]) {
								case "LIST" 		:	if( ($i + 1) <  $numArrayMatch){
															$list = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;
								case "ORDERBYDATE"	:	if( ($i + 1) <  $numArrayMatch){
															$orderbydate = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;							
							}
						}					
						$output = $this->getCategory($arrayMatch[0], $list, $orderbydate);					
					}
					//Caso de error
					else{
						$output = "Error, StorePapers, storepaperscategory.";
					}
					//Estas lineas se encargan de eliminar el nombre de la consulta del texto a visualizar.				
					$content = $this->quitarConsultaDelArticulo("/{storepaperscategory", $arrayMatch, $numArrayMatch, $output, $content);
				}
			}
			unset($matches);
		}
		
		// Consigue ID´s de {storepapersyearcategory: ID AÑO : ID CATEGORIA}
		// Como mucho puede tener 4 campos más, 6 en total.	
		if ((preg_match_all('/{storepapersyearcategory:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) || 
			(preg_match_all('/{storepapersyearcategory:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) || 
			(preg_match_all('/{storepapersyearcategory:.+?:.+?:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ) {
			
			//Get IDS
			foreach ($matches as $fmatch) {
				foreach ($fmatch as $match) {	
					$match = str_replace("{storepapersyearcategory:", "", $match);				
					$arrayMatch = explode(':',$match);
					
					//Linea de prueba
					//$output = "Identificador año: ".$arrayMatch[0]." Identificador categoria: ".$arrayMatch[1]." Campo 3: ".$arrayMatch[2]." Campo 4: ".$arrayMatch[3];
					
					$numArrayMatch = count($arrayMatch);
					//Elimino el parentesis final de la consulta
					if($numArrayMatch > 0)
						$arrayMatch[$numArrayMatch - 1] = strtok(($arrayMatch[$numArrayMatch - 1]), "}");
					
					$list = "DEFAULT";
					$orderbydate = "DESC";				
					if($numArrayMatch >= 2){					
						for($i = 2; $i < $numArrayMatch; $i += 1){					
							switch ($arrayMatch[$i]) {
								case "LIST" 		:	if( ($i + 1) <  $numArrayMatch){
															$list = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;
								case "ORDERBYDATE"	:	if( ($i + 1) <  $numArrayMatch){
															$orderbydate = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;						
							}
						}
						$output = $this->getYearCategory($arrayMatch[0], $arrayMatch[1], $list, $orderbydate);					
					}
					//Caso de error
					else{
						$output = "Error, StorePapers, storepapersyearcategory.";
					}
					//Estas lineas se encargan de eliminar el nombre de la consulta del texto a visualizar.				
					$content = $this->quitarConsultaDelArticulo("/{storepapersyearcategory", $arrayMatch, $numArrayMatch, $output, $content);
				}
			}
			unset($matches);
		}
		
		// Consigue ID´s de {storepaperspublication: ID PUBLICACION}
		// Como mucho puede tener 2 campos más, 5 en total.	
		if ((preg_match_all('/{storepaperspublication:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepaperspublication:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepaperspublication:.+?:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ) {

			//Get IDS
			foreach ($matches as $fmatch) {
				foreach ($fmatch as $match) {				
					$match = str_replace("{storepaperspublication:", "", $match);				
					$arrayMatch = explode(':',$match);
					
					//Linea de prueba
					//$output = "Identificador publicación: ".$arrayMatch[0]." Campo 2: ".$arrayMatch[1]." Campo 3: ".$arrayMatch[2];
					
					$numArrayMatch = count($arrayMatch);
					//Elimino el parentesis final de la consulta
					if($numArrayMatch > 0)
						$arrayMatch[$numArrayMatch - 1] = strtok(($arrayMatch[$numArrayMatch - 1]), "}");
					
					$list = "DEFAULT";			
					if($numArrayMatch >= 1){					
						for($i = 1; $i < $numArrayMatch; $i += 1){					
							switch ($arrayMatch[$i]) {
								case "LIST" 		:	if( ($i + 1) <  $numArrayMatch){
															$list = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;							
							}
						}
						$output = $this->getPublication($arrayMatch[0], $list);					
					}
					//Caso de error
					else{
						$output = "Error, StorePapers, storepaperspublication.";
					}
					//Estas lineas se encargan de eliminar el nombre de la consulta del texto a visualizar.				
					$content = $this->quitarConsultaDelArticulo("/{storepaperspublication", $arrayMatch, $numArrayMatch, $output, $content);
				}
			}
			unset($matches);
		}
		
		// ------------
		// Versión 1.22
		// ------------
		
		// Consigue ID´s de {storepapersauthorpublications: ID AUTOR } 
		// Como mucho puede tener 4 campos más, 5 en total.
		if ((preg_match_all('/{storepapersauthorpublications:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) || 
			(preg_match_all('/{storepapersauthorpublications:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) || 
			(preg_match_all('/{storepapersauthorpublications:.+?:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ) {

			//Get IDS
			foreach ($matches as $fmatch) {
				foreach ($fmatch as $match) {				
					$match = str_replace("{storepapersauthorpublications:", "", $match);				
					$arrayMatch = explode(':',$match);
					
					//Linea de prueba
					//$output = "Identificador autor: ".$arrayMatch[0].", Identificador categoria: ".$arrayMatch[1]." Campo 3: ".$arrayMatch[2]." Campo 4: ".$arrayMatch[3]." Número de campos: ".$numArrayMatch;
					
					$numArrayMatch = count($arrayMatch);
					//Elimino el parentesis final de la consulta
					if($numArrayMatch > 0)
						$arrayMatch[$numArrayMatch - 1] = strtok(($arrayMatch[$numArrayMatch - 1]), "}");
					
					$list = "DEFAULT";
					$orderbydate = "DESC";				
					if($numArrayMatch >= 1){					
						for($i = 1; $i < $numArrayMatch; $i += 1){					
							switch ($arrayMatch[$i]) {
								case "LIST" 		:	if( ($i + 1) <  $numArrayMatch){
															$list = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;
								case "ORDERBYDATE"	:	if( ($i + 1) <  $numArrayMatch){
															$orderbydate = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;							
							}
						}
						$output = $this->getAuthorPublications($arrayMatch[0], $list, $orderbydate);					
					}
					//Caso de error
					else{
						$output = "Error, StorePapers, storepapersauthorpublications.";
					}
					//Estas lineas se encargan de eliminar el nombre de la consulta del texto a visualizar.				
					$content = $this->quitarConsultaDelArticulo("/{storepapersauthorpublications", $arrayMatch, $numArrayMatch, $output, $content);				
				}
			}
			unset($matches);
		}
		
		// Consigue ID´s de {storepapersauthorpublicationspriority : ID AUTOR : PRIORITY}
		// Como mucho puede tener 4 campos más, 6 en total.
		if ((preg_match_all('/{storepapersauthorpublicationspriority:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepapersauthorpublicationspriority:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepapersauthorpublicationspriority:.+?:.+?:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ) {

			//Get IDS
			foreach ($matches as $fmatch) {
				foreach ($fmatch as $match) {
					$match = str_replace("{storepapersauthorpublicationspriority:", "", $match);				
					$arrayMatch = explode(':',$match);
					
					//Linea de prueba
					//$output = "Identificador autor: ".$arrayMatch[0].", Identificador categoria: ".$arrayMatch[1]." Prioridad: ".$arrayMatch[2]." Campo 4: ".$arrayMatch[3]." Campo 5: ".$arrayMatch[4]." Número de campos: ".$numArrayMatch;
					
					$numArrayMatch = count($arrayMatch);
					//Elimino el parentesis final de la consulta
					if($numArrayMatch > 0)
						$arrayMatch[$numArrayMatch - 1] = strtok(($arrayMatch[$numArrayMatch - 1]), "}");
					
					$list = "DEFAULT";
					$orderbydate = "DESC";
					if($numArrayMatch >= 2){					
						for($i = 2; $i < $numArrayMatch; $i += 1){					
							switch ($arrayMatch[$i]) {
								case "LIST" 		:	if( ($i + 1) <  $numArrayMatch){
															$list = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;
								case "ORDERBYDATE"	:	if( ($i + 1) <  $numArrayMatch){
															$orderbydate = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;							
							}
						}
						$output = $this->getAuthorPublicationsPriority($arrayMatch[0], $arrayMatch[1], $list, $orderbydate);					
					}
					//Caso de error
					else{
						$output = "Error, StorePapers, storepapersauthorpublicationspriority.";
					}
					//Estas lineas se encargan de eliminar el nombre de la consulta del texto a visualizar.				
					$content = $this->quitarConsultaDelArticulo("/{storepapersauthorpublicationspriority", $arrayMatch, $numArrayMatch, $output, $content);
				}
			}
			unset($matches);
		}
		
		// Consigue ID´s de {storepaperspublications}
		// Como mucho puede tener 4 campos más, 6 en total.	
		if ((preg_match_all('/{storepaperspublications:}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepaperspublications:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepaperspublications:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ) {		
			
			//Get IDS
			foreach ($matches as $fmatch) {
				foreach ($fmatch as $match) {				
					$match = str_replace("{storepaperspublications:", "", $match);				
					$arrayMatch = explode(':',$match);
					
					//Linea de prueba
					//$output = "No tiene identificador de publicación: Campo 1: ".$arrayMatch[0]." Campo 2: ".$arrayMatch[1];
						
					$numArrayMatch = count($arrayMatch);
					
					//Elimino el parentesis final de la consulta
					if($numArrayMatch > 0)
						$arrayMatch[$numArrayMatch - 1] = strtok(($arrayMatch[$numArrayMatch - 1]), "}");
					
					$list = "DEFAULT";
					$orderbydate = "DESC";
					if($numArrayMatch >= 0){					
						for($i = 0; $i < $numArrayMatch; $i += 1){					
							switch ($arrayMatch[$i]) {
								case "LIST" 		:	if( ($i + 1) <  $numArrayMatch){
															$list = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;
								case "ORDERBYDATE"	:	if( ($i + 1) <  $numArrayMatch){
															$orderbydate = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;								
							}
						}
						$output = $this->getPublications($list, $orderbydate);					
					}
					//Caso de error
					else{
						$output = "Error, StorePapers, storepaperspublications.";
					}
					//Estas lineas se encargan de eliminar el nombre de la consulta del texto a visualizar.				
					$content = $this->quitarConsultaDelArticulo("/{storepaperspublications", $arrayMatch, $numArrayMatch, $output, $content);
				}
			}
			unset($matches);
		}
		
		// ------------
		// Versión 1.41
		// ------------
		
		// Consigue ID´s de {storepapersauthorcategoryyear : ID AUTOR : ID CATEGORIA : YEAR}
		// Como mucho puede tener 4 campos más, 7 en total.
		if ((preg_match_all('/{storepapersauthorcategoryyear:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepapersauthorcategoryyear:.+?:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ||
			(preg_match_all('/{storepapersauthorcategoryyear:.+?:.+?:.+?:.+?:.+?:.+?:.+?}/', $content, $matches, PREG_PATTERN_ORDER)) ) {

			//Get IDS
			foreach ($matches as $fmatch) {
				foreach ($fmatch as $match) {
					$match = str_replace("{storepapersauthorcategoryyear:", "", $match);				
					$arrayMatch = explode(':',$match);
					
					//Linea de prueba
					//$output = "Identificador autor: ".$arrayMatch[0].", Identificador categoria: ".$arrayMatch[1]." Año: ".$arrayMatch[2]." Campo 4: ".$arrayMatch[3]." Campo 5: ".$arrayMatch[4]." Número de campos: ".$numArrayMatch;
					
					$numArrayMatch = count($arrayMatch);
					//Elimino el parentesis final de la consulta
					if($numArrayMatch > 0)
						$arrayMatch[$numArrayMatch - 1] = strtok(($arrayMatch[$numArrayMatch - 1]), "}");
					
					$list = "DEFAULT";
					$orderbydate = "DESC";
					if($numArrayMatch >= 3){					
						for($i = 3; $i < $numArrayMatch; $i += 1){					
							switch ($arrayMatch[$i]) {
								case "LIST" 		:	if( ($i + 1) <  $numArrayMatch){
															$list = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;
								case "ORDERBYDATE"	:	if( ($i + 1) <  $numArrayMatch){
															$orderbydate = $arrayMatch[$i + 1];
															$i += 1;
														}
														break;							
							}
						}					
						$output = $this->getAuthorCategoryYear($arrayMatch[0], $arrayMatch[1], $arrayMatch[2], $list, $orderbydate);					
					}
					//Caso de error
					else{
						$output = "Error, StorePapers, storepapersauthorcategoryyear.";
					}
					//Estas lineas se encargan de eliminar el nombre de la consulta del texto a visualizar.				
					$content = $this->quitarConsultaDelArticulo("/{storepapersauthorcategoryyear", $arrayMatch, $numArrayMatch, $output, $content);
				}
			}
			unset($matches);
		}
		
		$row->text = $content;
		return true;
	}
	/* 
	ORDEN de las funciones.

	(1) StorePapers Author Category
	(2) Storepapers Author Category Priority
	(3) StorePapers Category
	(4) StorePapers Year Category
	(5) StorePapers Publication

	Versión 1.22

	(6) StorePapers Author publications
	(7) StorePapers Author publications Priority
	(8) StorePapers Publications
	
	Versión 1.41
	
	(8) StorePapers Author Category Year
	*/
	/*
		{storepapersauthorcategory: ID AUTOR : ID CATEGORIA} 
		Esta función recibe dos parametros, el identificador del autor de la publicación y la categoria de la publicación.
		Devuelve el texto de la publicación.
	*/
	
	
	private function getAuthorCategory($idAutor, $idCategoria, $list, $orderbydate){	

		$database =& JFactory::getDBO();
		
		$output				= $this->outputPaso1Consulta($list);
		$orderbydate		= $this->orderByDate($orderbydate);
		$orderbydate		= $this->searchPublicationOrder($orderbydate);
		
		$condicionAutor1	= $this->extraerIdentificadores('-', "#__storepapers_autorpubli.ida=",		$idAutor);
		$condicionAutor2	= $this->extraerIdentificadores('-', "#__storepapers_autores.id=",			$idAutor);
		$condicionCategoria	= $this->extraerIdentificadores('-', "#__storepapers_publicaciones.idc=",	$idCategoria);
		
		//Recogiendo publicación de la BD
		$database->setQuery("SELECT DISTINCT #__storepapers_publicaciones.texto
							FROM #__storepapers_autores, #__storepapers_autorpubli, #__storepapers_publicaciones
							WHERE $condicionAutor1 
								AND $condicionCategoria 
								AND #__storepapers_publicaciones.id = #__storepapers_autorpubli.idp
								AND $condicionAutor2 
								AND #__storepapers_publicaciones.published = 1
							ORDER BY $orderbydate");
		$rows = null;
		$rows = $database->loadObjectList();
		//Recorro la consulta obtenida escribiendo los resultados.
		foreach ( $rows as $row ) {		
			$output .= $this->outputPaso2Consulta($list, $row->texto);
		}	
		$output .= $this->outputPaso3Consulta($list);	
		return($output);
	}
	/*
		{storepapersauthorcategorypriority: ID AUTOR : ID CATEGORIA : PRIORITY} 
		Esta función recibe tres parametros, el identificador del autor de la publicación, la categoria de la publicación 
		y la prioridad.
		Devuelve el texto de la publicación.
	*/
	private function getAuthorCategoryPriority($idAutor, $idCategoria, $prioridad, $list, $orderbydate){	

		$database =& JFactory::getDBO();
		
		$output 			= $this->outputPaso1Consulta($list);
		$orderbydate		= $this->orderByDate($orderbydate);
		$orderbydate		= $this->searchPublicationOrder($orderbydate);
		
		$condicionAutor1	= $this->extraerIdentificadores('-', "#__storepapers_autorpubli.ida=",			$idAutor);
		$condicionAutor2	= $this->extraerIdentificadores('-', "#__storepapers_autores.id=",				$idAutor);
		$condicionCategoria = $this->extraerIdentificadores('-', "#__storepapers_publicaciones.idc=",		$idCategoria);
		$condicionPrioridad	= $this->extraerIdentificadores('-', "#__storepapers_autorpubli.prioridad=",	$prioridad);
		
		//Recogiendo publicación de la BD
		$database->setQuery("SELECT DISTINCT #__storepapers_publicaciones.texto
							FROM #__storepapers_autores, #__storepapers_autorpubli, #__storepapers_publicaciones
							WHERE $condicionAutor1 
								AND $condicionCategoria 
								AND #__storepapers_publicaciones.id = #__storepapers_autorpubli.idp
								AND $condicionAutor2 
								AND $condicionPrioridad 
								AND #__storepapers_publicaciones.published = 1 
							ORDER BY $orderbydate");
		$rows = null;
		$rows = $database->loadObjectList();
		//Recorro la consulta obtenida escribiendo los resultados.
		foreach ( $rows as $row ) {		
			$output .= $this->outputPaso2Consulta($list, $row->texto);
		}		
		$output .= $this->outputPaso3Consulta($list);	
		return($output);
	}
	/*
		{storepaperscategory: ID CATEGORIA} 
		Esta función recibe un parametro, el identificador de categoria.
		Devuelve todas las publicaciones que coincide con esa categoria.
	*/
	private function getCategory($idCategoria, $list, $orderbydate){	

		$database =& JFactory::getDBO();
		
		$output				= $this->outputPaso1Consulta($list);
		$orderbydate		= $this->orderByDate($orderbydate);
		$orderbydate		= $this->searchPublicationOrder($orderbydate);
		
		$condicionCategoria	= $this->extraerIdentificadores('-', "#__storepapers_publicaciones.idc=", $idCategoria);
		
		//Recogiendo publicación de la BD		
		$database->setQuery("SELECT DISTINCT #__storepapers_publicaciones.texto
							FROM #__storepapers_publicaciones
							WHERE $condicionCategoria
								AND #__storepapers_publicaciones.published = 1						
							ORDER BY $orderbydate");
		$rows = null;
		$rows = $database->loadObjectList();
		//Recorro la consulta obtenida escribiendo los resultados.
		foreach ( $rows as $row ) {
			$output .= $this->outputPaso2Consulta($list, $row->texto);
		}	
		$output .= $this->outputPaso3Consulta($list);
		return($output);
	}
	/*
		{storepapersyearcategory: ID AÑO : ID CATEGORIA} 
		Esta función recibe dos parametros, el año de publicación y la categoria.
		Devuelve el texto de la publicación.
	*/
	private function getYearCategory($idAno, $idCategoria, $list, $orderbydate){

		$database =& JFactory::getDBO();

		$output 			= $this->outputPaso1Consulta($list);
		$orderbydate 		= $this->orderByDate($orderbydate);
		$orderbydate		= $this->searchPublicationOrder($orderbydate);
		
		$condicionAno 		= $this->extraerIdentificadores('-', "#__storepapers_publicaciones.year=",	$idAno);
		$condicionCategoria	= $this->extraerIdentificadores('-', "#__storepapers_publicaciones.idc=",	$idCategoria);
		
		//Recogiendo publicación de la BD		
		$database->setQuery("SELECT DISTINCT texto
							FROM #__storepapers_publicaciones
							WHERE $condicionAno
								AND $condicionCategoria
								AND #__storepapers_publicaciones.published = 1
							ORDER BY $orderbydate");
		$rows = null;
		$rows = $database->loadObjectList();
		//Recorro la consulta obtenida escribiendo los resultados.
		foreach ( $rows as $row ) {
			$output .= $this->outputPaso2Consulta($list, $row->texto);
		}	
		$output .= $this->outputPaso3Consulta($list);
		return($output);
	}
	/*
		{storepaperspublication: ID PUBLICACION}
		Esta función recibe por parámetro un identificador de una publicación para mostrar.
		Devuelve el texto de la publicación.
	*/
	private function getPublication($id, $list) {
		
		$database =& JFactory::getDBO();
			
		$output = $this->outputPaso1Consulta($list);
		$condicionID = $this->extraerIdentificadores('-', "#__storepapers_publicaciones.id=", $id);
		
		//Recogiendo publicación de la BD	
		$database->setQuery("SELECT DISTINCT texto FROM #__storepapers_publicaciones 
							WHERE $condicionID 
								AND #__storepapers_publicaciones.published = 1");
		$rows = null;
		$rows = $database->loadObjectList();
		//Recorro la consulta obtenida escribiendo los resultados.
		foreach ( $rows as $row ) {
			$output .= $this->outputPaso2Consulta($list, $row->texto);
		}	
		$output .= $this->outputPaso3Consulta($list);
		return($output);
	}
	// ------------
	// Versión 1.22
	// ------------
	/*
		{storepapersauthorpublications: ID AUTOR} 
		Esta función recibe un parametro, el identificador del autor.
		Devuelve el texto de la publicación. Todas las publicaciones relacionadas con este autor.
	*/
	private function getAuthorPublications($idAutor, $list, $orderbydate){
		
		$database =& JFactory::getDBO();
		
		$output 			= $this->outputPaso1Consulta($list);
		$orderbydate 		= $this->orderByDate($orderbydate);
		$orderbydate		= $this->searchPublicationOrder($orderbydate);
		
		$condicionAutor1 	= $this->extraerIdentificadores('-', "#__storepapers_autorpubli.ida=",	$idAutor);
		$condicionAutor2	= $this->extraerIdentificadores('-', "#__storepapers_autores.id=",		$idAutor);
		
		//Recogiendo publicación de la BD		
		$database->setQuery("SELECT DISTINCT #__storepapers_publicaciones.texto
							FROM #__storepapers_autores, #__storepapers_autorpubli, #__storepapers_publicaciones
							WHERE $condicionAutor1
								AND #__storepapers_publicaciones.id = #__storepapers_autorpubli.idp
								AND $condicionAutor2
								AND #__storepapers_publicaciones.published = 1
							ORDER BY $orderbydate");
		$rows = null;
		$rows = $database->loadObjectList();
		//Recorro la consulta obtenida escribiendo los resultados.
		foreach ( $rows as $row ) {		
			$output .= $this->outputPaso2Consulta($list, $row->texto);
		}	
		$output .= $this->outputPaso3Consulta($list);	
		return($output);
	}
	/*
		{storepapersauthorpublicationspriority: ID AUTOR : PRIORITY} 
		Esta función recibe dos parametros, el identificador del autor y la prioridad.
		Devuelve el texto de la publicación. Todas las publicaciones relacionadas con este autor y con la prioridad dada.
	*/
	private function getAuthorPublicationsPriority($idAutor, $prioridad, $list, $orderbydate){
		
		$database =& JFactory::getDBO();
		
		$output 			= $this->outputPaso1Consulta($list);
		$orderbydate 		= $this->orderByDate($orderbydate);
		$orderbydate		= $this->searchPublicationOrder($orderbydate);
		
		$condicionAutor1 	= $this->extraerIdentificadores('-', "#__storepapers_autorpubli.ida=", 			$idAutor);
		$condicionAutor2 	= $this->extraerIdentificadores('-', "#__storepapers_autores.id=", 				$idAutor);
		$condicionPrioridad	= $this->extraerIdentificadores('-', "#__storepapers_autorpubli.prioridad=",	$prioridad);
		
		//Recogiendo publicación de la BD		
		$database->setQuery("SELECT DISTINCT #__storepapers_publicaciones.texto
							FROM #__storepapers_autores, #__storepapers_autorpubli, #__storepapers_publicaciones
							WHERE $condicionAutor1 
								AND #__storepapers_publicaciones.id = #__storepapers_autorpubli.idp
								AND $condicionAutor2 
								AND $condicionPrioridad 
								AND #__storepapers_publicaciones.published = 1
							ORDER BY $orderbydate");
		$rows = null;
		$rows = $database->loadObjectList();
		//Recorro la consulta obtenida escribiendo los resultados.
		foreach ( $rows as $row ) {		
			$output .= $this->outputPaso2Consulta($list, $row->texto);
		}		
		$output .= $this->outputPaso3Consulta($list);	
		return($output);
	}
	/*
		{storepaperspublications}	
		Devuelve el texto de todas las publicaciones. Ordenadas por año y por nombre de la publicación.
	*/
	private function getPublications($list, $orderbydate) {	
		
		$database =& JFactory::getDBO();
			
		$output				= $this->outputPaso1Consulta($list);
		$orderbydate		= $this->orderByDate($orderbydate);
		$orderbydate		= $this->searchPublicationOrder($orderbydate);
		
		//Recogiendo publicación de la BD	
		$database->setQuery("SELECT texto FROM #__storepapers_publicaciones 
							WHERE #__storepapers_publicaciones.published = 1 
							ORDER BY $orderbydate");
		$rows = null;
		$rows = $database->loadObjectList();
		//Recorro la consulta obtenida escribiendo los resultados.
		foreach ( $rows as $row ) {		
			$output .= $this->outputPaso2Consulta($list, $row->texto);
		}	
		$output .= $this->outputPaso3Consulta($list);	
		
		return($output);	
	}
	// ------------
	// Versión 1.41
	// ------------
	/*
		{storepapersauthorcategoryyear: ID AUTOR : ID CATEGORIA : YEAR} 
		Esta función recibe tres parametros, el identificador del autor de la publicación, la categoria de la publicación 
		y el año de la publicación.
		Devuelve el texto de la publicación.
	*/
	private function getAuthorCategoryYear($idAutor, $idCategoria, $year, $list, $orderbydate){	

		$database =& JFactory::getDBO();
		
		$output 			= $this->outputPaso1Consulta($list);
		$orderbydate 		= $this->orderByDate($orderbydate);
		$orderbydate		= $this->searchPublicationOrder($orderbydate);
		
		$condicionAutor1 	= $this->extraerIdentificadores('-', "#__storepapers_autorpubli.ida=",		$idAutor);
		$condicionAutor2 	= $this->extraerIdentificadores('-', "#__storepapers_autores.id=",			$idAutor);
		$condicionCategoria	= $this->extraerIdentificadores('-', "#__storepapers_publicaciones.idc=",	$idCategoria);
		$condicionAno 		= $this->extraerIdentificadores('-', "#__storepapers_publicaciones.year=",	$year);
		
		//Recogiendo publicación de la BD
		$database->setQuery("SELECT DISTINCT #__storepapers_publicaciones.texto
							FROM #__storepapers_autores, #__storepapers_autorpubli, #__storepapers_publicaciones
							WHERE $condicionAutor1 
								AND $condicionCategoria 
								AND #__storepapers_publicaciones.id = #__storepapers_autorpubli.idp
								AND $condicionAutor2 
								AND $condicionAno 
								AND #__storepapers_publicaciones.published = 1 
							ORDER BY $orderbydate");
		$rows = null;
		$rows = $database->loadObjectList();
		//Recorro la consulta obtenida escribiendo los resultados.
		foreach ( $rows as $row ) {		
			$output .= $this->outputPaso2Consulta($list, $row->texto);
		}		
		$output .= $this->outputPaso3Consulta($list);	
		return($output);
	}
	// ----------------------------------------------
	// FUNCIONES DE APOYO
	// ----------------------------------------------

	/*
		Función que extrae los identificadores de una cadena tipo "num-num-num-..."
		Dada la variable "cadenaCondicion" se añade nuevas condiciones en la sentencia
		concatenadas con una orden "OR". El delimitador usado es el usado en la variable.
	*/
	private function extraerIdentificadores($delimitador, $cadenaCondicion, $identificadores){

		$arrayID = explode($delimitador, $identificadores);	
		$numArrayID = count($arrayID);
		
		$condicion = "($cadenaCondicion";
		
		for($i = 0; $i < $numArrayID; $i += 1){		
			$condicion .= $arrayID[$i]." ";
			if($i != ($numArrayID - 1))
				$condicion .= " OR ".$cadenaCondicion;
		}
		$condicion .= ")";
		return($condicion);
	}
	/*
		Función que se encarga de eliminar el código que realiza la consulta en la base de datos.
		Se le pasa por parámetro el vector de consulta asi como el contenido y la salida de la consulta.
		Devuelve el contenido.
	*/
	private function quitarConsultaDelArticulo($string, $arrayMatch, $numArrayMatch, $output, $content){

		//Estas lineas se encargan de eliminar el nombre de la consulta del texto a visualizar.		
		for($i = 0; $i < $numArrayMatch; $i += 1){					
			$string = $string.":".$arrayMatch[$i];
		}
		$string = $string."}/";				
		$content = preg_replace($string, $output, $content);
		
		return($content);
	}
	/*
		Función que se le paso el tipo de listado a mostrar en la consulta.
		Se encarga de crear la cabecera. Añade una clase en "ol" o "ul".
		Acepta tres tipos diferentes, {"DEFAULT","NONE","NUMBER"}
	*/	
	private function outputPaso1Consulta($list){
		
		$class_ul			= $this->params->def('class_ul',		"");
		$class_ol			= $this->params->def('class_ol',		"");
		
		if(!$class_ul == "")
			$class_ul = "class=\"$class_ul\"";
		
		if(!$class_ol == "")
			$class_ol = "class=\"$class_ol\"";
		
		//Evaluamos la condición LIST
		switch ($list) {		
			case "NONE"		: $output = "<div>";
							  break;			
			case "NUMBER"	: $output = "<div><ol $class_ol>";
							  break;			
			default 		: $output = "<div><ul $class_ul>";
							  break;
		}
		return($output);
	}
	/*
		Función que se le paso el tipo de listado a mostrar en la consulta, tambien se le pasa el texto a mostrar.
		Se encarga de crear el cuerpo. Además elimina la etiqueta <p> y </p> del principio y el final.
		Acepta tres tipos diferentes, {"DEFAULT","NONE","NUMBER"}
	*/	
	private function outputPaso2Consulta($list, $texto){
	
		if(stripos($texto, "<p>") !== false)
			$texto = substr($texto, stripos($texto,"<p>") + strlen("<p>"));
			
		if(strripos($texto, "</p>") !== false)
			$texto = substr_replace($texto, "", strripos($texto,"</p>"));
		
		//Evaluamos la condición LIST
		switch ($list) {			
			case "NONE"		: $output = "<p>$texto</p>";
							  break;			
			case "NUMBER"	: $output = "<li>$texto</li>";
							  break;			
			default 		: $output = "<li>$texto</li>";
							  break;
		}
		return($output);
	}
	/*
		Función que se le paso el tipo de listado a mostrar en la consulta.
		Se encarga de crear el final.
		Acepta tres tipos diferentes, {"DEFAULT","NONE","NUMBER"}
	*/	
	private function outputPaso3Consulta($list){
		//Evaluamos la condición LIST	
		switch ($list) {		
			case "NONE"		: $output = "</div>";
							  break;			
			case "NUMBER"	: $output = "</ol></div>";
							  break;			
			default 		: $output = "</ul></div>";
							  break;
		}	
		return($output);
	}
	/*
		Función que se comprueba y selecciona el orden de la consulta. Por Fecha.
		Acepta dos tipos diferentes, {"ASC","DESC"}
	*/	
	private function orderByDate($orderbydate){
		//Evaluamos la condición ORDERBYDATE	
		switch ($orderbydate) {		
			case "ASC"		: $orderby = "#__storepapers_publicaciones.year ASC, #__storepapers_publicaciones.month ASC";
							  break;			
			case "DESC"		: $orderby = "#__storepapers_publicaciones.year DESC, #__storepapers_publicaciones.month ASC";
							  break;			
			default 		: $orderby = "#__storepapers_publicaciones.year DESC, #__storepapers_publicaciones.month ASC";
							  break;
		}	
		return($orderby);
	}
	/*
		Función para saber el orden que se usa para mostrar las publicaciones.
		Valor 0		->	Por título
		Valor 1		->	Por autor
		
		Falta por terminar como realizar la consulta para ordenar las publicaciones según el autor.
		Por ahora todas las opciones hacen lo mismo.
	*/
	private function searchPublicationOrder($orderbydate){
		//Evaluamos la condición SEARCH PUBLICATION
		switch ($this->params->def('order_publication', '0')) {		
			case "0"		: $orderbydate .= ", #__storepapers_publicaciones.nombre ASC";
							  break;			
			case "1"		: $orderbydate .= ", #__storepapers_publicaciones.nombre ASC";
							  break;			
			default 		: $orderbydate .= ", #__storepapers_publicaciones.nombre ASC";
							  break;
		}
		return($orderbydate);
	}
}