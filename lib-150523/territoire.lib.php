<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2017 Mikael Carlavan <contact@mika-carl.fr>
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
 *	\file       htdocs/territoire/lib/territoire.lib.php
 *	\brief      Ensemble de fonctions de base pour le module territoire
 * 	\ingroup	territoire
 */


/**
 * Prepare array with list of tabs
 *
 * @param   Product	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function territoire_prepare_admin_head()
{
	global $db, $langs, $conf, $user;
	$langs->load("territoire@territoire");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/territoire/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("localAuthorities");
	$head[$h][2] = 'localAuthorities';
	$h++;

	$head[$h][0] = dol_buildpath("/territoire/admin/laacctotown.php", 1);
	$head[$h][1] = $langs->trans("localAuthorityAccordingToTown");
	$head[$h][2] = 'localAuthorityAccordingToTown';
	$h++;

	// if($conf->global->USE_MONTHLY_GAS_TAX){
	// $head[$h][0] = dol_buildpath("/territoire/admin/gastax.php", 1);
	// $head[$h][1] = $langs->trans("GasTax");
	// $head[$h][2] = 'gastax';
	// $h++;
	// }

	// $head[$h][0] = dol_buildpath("/territoire/admin/zones.php", 1);
	// $head[$h][1] = $langs->trans("Zones");
	// $head[$h][2] = 'zones';
	// $h++;

	// if($conf->global->USE_WEIGHT){
	// $head[$h][0] = dol_buildpath("/territoire/admin/weightranges.php", 1);
	// $head[$h][1] = $langs->trans("WeightRanges");
	// $head[$h][2] = 'weightranges';
	// $h++;
	// }

	// if($conf->global->USE_LINEAR_METER){
	// $head[$h][0] = dol_buildpath("/territoire/admin/linearmeterranges.php", 1);
	// $head[$h][1] = $langs->trans("LinearMeterRanges");
	// $head[$h][2] = 'linearmeterranges';
	// $h++;
	// }

	// $head[$h][0] = dol_buildpath("/territoire/admin/extracosts.php", 1);
	// $head[$h][1] = $langs->trans("ExtraCosts");
	// $head[$h][2] = 'extracosts';
	// $h++;


	$head[$h][0] = dol_buildpath("/territoire/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	return $head;
}
