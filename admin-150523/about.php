<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2017 Mikael Carlavan <contact@mika-carl.fr>
 * Copyright (C) 2022 Julien Marchand <julien.marchand@iouston.com>
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
 *  \file       htdocs/territoire/index.php
 *  \ingroup    territoire
 *  \brief      Page to show product set
 */


$res=@include("../../main.inc.php");                   // For root directory
if (! $res) $res=@include("../../../main.inc.php");    // For "custom" directory


// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

dol_include_once("/territoire/lib/territoire.lib.php");

// Translations
$langs->load("territoire@territoire");

// Translations
$langs->load("errors");
$langs->load("admin");
$langs->load("other");

// Access control
if (! $user->admin) {
	accessforbidden();
}

$versions = array(
	array('version' => '1.0.0', 'date' => '12/04/2018', 'updates' => $langs->trans('FirstVersion')),
	array('version' => '1.0.1', 'date' => '17/02/2019', 'updates' => $langs->trans('territoireVersion17022019')),
	array('version' => '1.0.2', 'date' => '26/06/2019', 'updates' => $langs->trans('territoireVersion26062019')),
	array('version' => '1.0.3', 'date' => '18/09/2019', 'updates' => $langs->trans('territoireVersion18092019')),
	array('version' => '1.0.4', 'date' => '03/10/2019', 'updates' => $langs->trans('territoireVersion03102019')),
	array('version' => '1.0.5', 'date' => '05/12/2019', 'updates' => $langs->trans('territoireVersion05122019')),
	array('version' => '1.0.6', 'date' => '18/03/2020', 'updates' => $langs->trans('territoireVersion18032020')),
	array('version' => '1.0.7', 'date' => '21/03/2020', 'updates' => $langs->trans('territoireVersion21032020')),
	array('version' => '1.0.8', 'date' => '08/04/2020', 'updates' => $langs->trans('territoireVersion08042020')),
	array('version' => '1.0.9', 'date' => '18/05/2020', 'updates' => $langs->trans('territoireVersion18052020')),
    array('version' => '1.0.10', 'date' => '13/01/2021', 'updates' => $langs->trans('territoireVersion13012021')),
    array('version' => '1.0.11', 'date' => '29/04/2021', 'updates' => $langs->trans('territoireVersion29042021')),
    array('version' => '1.0.12', 'date' => '25/01/2022', 'updates' => $langs->trans('territoireVersion25012022')),
    array('version' => '1.0.13', 'date' => '30/06/2022', 'updates' => $langs->trans('territoireVersion30062022')),
    array('version' => '1.0.14', 'date' => '01/12/2022', 'updates' => $langs->trans('territoireVersion1014')),
    array('version' => '1.0.15', 'date' => '16/01/2023', 'updates' => $langs->trans('territoireVersion1015')),
    array('version' => '1.0.16', 'date' => '14/02/2023', 'updates' => $langs->trans('territoireVersion1016')),

);

/*
 * View
 */

$form = new Form($db);

llxHeader('', $langs->trans('territoireAbout'));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'. $langs->trans("BackToModuleList") . '</a>';
print load_fiche_titre($langs->trans('territoireAbout'), $linkback);

// Configuration header
$head = territoire_prepare_admin_head();
dol_fiche_head(
	$head,
	'about',
	$langs->trans("ModuleterritoireName"),
	0,
	'territoire@territoire'
);

// About page goes here
echo $langs->trans("territoireAboutPage");

echo '<br />';

$url = 'http://www.iouston.com/systeme-gestion-entreprise-dolibarr/modules-dolibarr/module-dolibarr-frais-transport';

print '<h2>'.$langs->trans("About").'</h2>';
print $langs->trans("territoireAboutDescLong", $url, $url);

print '<h2>'.$langs->trans("MaintenanceAndSupportTitle").'</h2>';
print $langs->trans("MaintenanceAndSupportDescLong");

print '<h2>'.$langs->trans("UpdateTitle").'</h2>';
print $langs->trans("UpdateDescLong");

print '<h2>'.$langs->trans("ModulesTitle").'</h2>';
print $langs->trans("ModulesDescLong");

echo '<br />';

print '<a href="http://www.dolistore.com">'.img_picto('dolistore', dol_buildpath('/territoire/img/dolistore.png', 1), '', 1).'</a>';

print '<hr />';

print '<a href="http://www.iouston.com">'.img_picto('iouston', dol_buildpath('/territoire/img/iouston.png', 1), '', 1).'</a>';

echo '<br />';

print $langs->trans("IoustonDesc");

print '<hr />';
print '<h2>'.$langs->trans("ChangeLog").'</h2>';


print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("ChnageLogVersion").'</td>';
print '<td>'.$langs->trans("ChnageLogDate").'</td>';
print '<td>'.$langs->trans("ChnageLogUpdates").'</td>';
print "</tr>\n";

foreach ($versions as $version)
{
	print '<tr class="oddeven">';
	print '<td>';
	print $version['version'];
	print '</td>';
	print '<td>';
	print $version['date'];
	print '</td>';
	print '<td>';
	print $version['updates'];
	print '</td>';
	print '</tr>';
}


print '</table>';

// Page end
dol_fiche_end();
llxFooter();
$db->close();
