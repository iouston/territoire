<?php
/* Copyright (C) 2023 Julien Marchand <julien.marchand@iouston.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/attestationtva/class/actions_attestationtva.class.php
 *  \ingroup    attestationtva
 *  \brief      File of class to manage actions on propal
 */
// require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
// require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
// dol_include_once('/attestationtva/class/attestationtva.class.php');

class ActionsTerritoire
{ 

	function printAddress($parameters, &$object, &$action, $hookmanager)		
	{
		global $db;

		$soc = new Societe($db);
		$soc->fetch(GETPOST('socid','int'));

		// echo '<pre>';
		// print_r($soc->town);
		// echo '</pre>';
		
		$sql = "SELECT z.code, z.zip, z.town, cpv.fk_code, cpv.fk_localauthoritycode, c.localauthoritycode, c.localauthorityname ";
		$sql .= "FROM llx_c_ziptown as z ";
		$sql .= "INNER JOIN llx_c_territoire_collecparville as cpv ";
		$sql .= "ON cpv.fk_code = z.code ";
	 	$sql .= "INNER JOIN llx_c_territoire_collectivites as c ";
	 	$sql .= "ON c.localauthoritycode = cpv.fk_localauthoritycode ";
	 	$sql .= "WHERE z.town = '". $soc->town . "'"; 

	 	$resql = $db->query($sql);

	 	$obj = $db->fetch_object($resql); 

		$this->resprints = '';
		$object=$object. '</br>' . $obj->localauthorityname;



		return 0;
	}



}


