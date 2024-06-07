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
	$head[$h][1] = $langs->trans("townAccordingToLocAuth");
	$head[$h][2] = 'townAccordingToLocAuth';
	$h++;
	
	$head[$h][0] = dol_buildpath("/territoire/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	return $head;
}
