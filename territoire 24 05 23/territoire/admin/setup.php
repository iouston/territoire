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
 *  \file       htdocs/territoire/admin/setup.php
 *  \ingroup    territoire
 *  \brief      Admin page
 */


$res=@include("../../main.inc.php");                   // For root directory
if (! $res) $res=@include("../../../main.inc.php");    // For "custom" directory

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';
dol_include_once("/territoire/lib/territoire.lib.php");
dol_include_once("/territoire/class/territoire.class.php");
dol_include_once("/territoire/class/localauthority.class.php");
dol_include_once("/territoire/class/html.form.localauthority.class.php");

// Translations
$langs->load("territoire@territoire");

// Access control
if (! $user->admin) accessforbidden();

//sort_order and soertfield
$sortfield = GETPOST("sortfield",'alpha') ? GETPOST("sortfield",'alpha') : 'rowid';
$sortorder = GETPOST("sortorder",'alpha') ? GETPOST("sortorder",'alpha') : 'ASC';

// Parameters
$action = GETPOST('action', 'alpha');
$confirm = GETPOST('confirm', 'alpha');
$rowid = GETPOST('rowid', 'int');
$form = new Form($db);
$formconfirm = '';
$localAuthForm = new LocalAuthorityForm($db);
$localAuthority = new LocalAuthority($db);
$search_localauthoritycode = GETPOST('search_localauthoritycode', 'alpha');
$search_localauthorityname = GETPOST('search_localauthorityname', 'alpha');




/*
 * Actions
 */
//To display the button "activate", "disable" for the 
//local authorities ("collectivités"): 
$acts[0] = "activate";
$acts[1] = "disable";
$actl[0] = img_picto($langs->trans("Disabled"),'switch_off');
$actl[1] = img_picto($langs->trans("Activated"),'switch_on');

// activate
if ($action == $acts[0])
{
    $rowid = GETPOST('rowid', 'int');
    $localAuthority->setActive($rowid);
}

// disable
if ($action == $acts[1])
{
    $rowid = GETPOST('rowid', 'int');
    $localAuthority->fetch($rowid);
    $localAuthority->setInactive($rowid);
}

//To update a local authority:
if($action == 'update_localauthority')
{
    $localAuthority->rowid = GETPOST('rowid_to_keep', 'int');
    $localAuthority->localAuthorityCode = GETPOST('localAuthorityCode', 'alpha');
    $localAuthority->localAuthorityName = GETPOST('localAuthorityName', 'alpha');
    $localAuthority->active = GETPOST('active', 'int');
    $localAuthority->update($localAuthority->rowid, $user, 'update');
}

//To delete a local authority:
if ($action == 'confirm_delete' && $confirm == 'yes')
{
    $localAuthority->rowid = GETPOST('rowid', 'int');
    $localAuthority->fetch($localAuthority->rowid);
    $localAuthority->delete($user);
}

//To create a local authority:
if($action == 'create_new_local_authority')
{
    $localAuthority->localAuthorityCode = GETPOST('localAuthorityCode', 'alpha');
    $localAuthority->localAuthorityName = GETPOST('localAuthorityName', 'alpha');
    $localAuthority->active = GETPOST('active', 'int');
    $localAuthority->create($user);
}


/*
 * View
 */

llxHeader('', $langs->trans('territoireSetup'));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';

print load_fiche_titre($langs->trans('territoireSetup'), $linkback);

// Configuration header
$head = territoire_prepare_admin_head();
dol_fiche_head(
	$head,
	'settings',
	$langs->trans("ModuleterritoireName"),
	0,
	"territoire1@territoire"
);

//Asking a confirmation before deleting a local authority:
if ($action == 'delete')
{
   print $form->formconfirm($_SERVER["PHP_SELF"].'?rowid='.$rowid.$paramwithsearch, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_delete','',0,1);
}

// Setup page goes here
echo $langs->trans("territoireSetupPage");


print load_fiche_titre($langs->trans("toAddALocalAuthority"),'','');

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="create_new_local_authority">';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td><label for="localAuthorityCode">'.  $langs->trans("localAuthorityCode") . '</label></td>';
print '<td><label for="localAuthorityName">'.  $langs->trans("localAuthorityName") . '</label></td>';
print '<td><label for="active">'.  $langs->trans("active") . '</label></td>';
print '</tr>';
print '<tr>';
print '<td><input type="text" name="localAuthorityCode" value="" placeholder="'.$langs->trans('LACodePlaceholderLabel').'"></td>';
print '<td><input type="text" name="localAuthorityName" value="" placeholder="'.$langs->trans('LANamePlaceholderLabel').'"></td>';
print '<td>';
print $form-> selectyesno('active', '1', 1, false);
print '</td>';
print '<td><input type="submit" class="button" name="submitButton" value="'.$langs->trans("Add").'"></td>';

print '</tr>';
print '</table>';
print '</form>';


/**
* Display of the "collectivite" already entered in the database:
*/
print load_fiche_titre($langs->trans("collectiviteList"),'','');


//Form filter
print '<form action="'.$_SERVER['PHP_SELF'].'?rowid='.$rowid.'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<div class="div-table-responsive">';
print '<table class="noborder" width="100%">';

// Title of lines
print '<tr class="liste_titre">';
print getTitleFieldOfList($langs->trans("localAuthorityCode"), 0, $_SERVER["PHP_SELF"], 'localauthoritycode', '', $param, "align='left'", $sortfield, $sortorder);
print getTitleFieldOfList($langs->trans("localAuthorityName"), 0, $_SERVER["PHP_SELF"], 'localauthorityname', '', $param, "align='left'", $sortfield, $sortorder);
print getTitleFieldOfList($langs->trans("active"), 0, $_SERVER["PHP_SELF"], 'active', '', $param, "align='left'", $sortfield, $sortorder);
print '</tr>';

// Title line with search boxes
print '<tr class="liste_titre_filter">';
//Local authority code:
print '<td class="liste_titre">';
print $localAuthForm->select_localauthoritycode($search_localauthoritycode, 'search_localauthoritycode','', 1);
print '</td>';

//Local authority name:
print '<td class="liste_titre">';
print $localAuthForm->select_localauthorityname($search_localauthorityname, 'search_localauthorityname','', 1);
print '</td>';

//Active:
print '<td class="liste_titre">';
print '&nbsp;';
print '</td>';

print '<td class="liste_titre" colspan="3" align="right">';
print $form->showFilterAndCheckAddButtons(0);
print '</td>';
print '</tr>';

$sql = "SELECT rowid, localauthoritycode, localauthorityname, active";
$sql.= " FROM ".MAIN_DB_PREFIX."c_territoire_collectivites";
$sql.= " WHERE entity = ".$conf->entity;

if(!empty($search_localauthoritycode))
{
    $sql.=" AND localauthoritycode='" .$search_localauthoritycode ."'";
}

if(!empty($search_localauthorityname))
{
    $sql.=" AND localauthorityname='" .$search_localauthorityname . "'";
}

$sql.= " ORDER BY ".$sortfield." ".$sortorder;

$resql = $db->query($sql);

if ($resql)
{
    $num = $db->num_rows($resql);

    if ($num)
    {
        $i = 0;
        // Lines with values
        while ($i < $num)
        {
            $rowid = GETPOST('rowid','alpha');
            --$rowid;

            if($action == 'update' && $rowid == $i)
            {
                $rowid = GETPOST('rowid','alpha');

                $sql2 = "SELECT rowid, localauthoritycode, localauthorityname, active";
                $sql2 .= " FROM ".MAIN_DB_PREFIX."c_territoire_collectivites";
                $sql2 .= " WHERE entity = ".$conf->entity;    
                $sql2 .=" AND rowid =" . $rowid;


                $resql2 = $db->query($sql2);
                $obj2 = $db->fetch_object($resql2);  

                print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
                print '<tr class="oddeven" id="rowid-'.$obj2->rowid.'">';
                print '<td><input type="text" name="localAuthorityCode" value="'.$obj2->localauthoritycode.'">' . '</td>';
                print '<td><input type="text" name="localAuthorityName" value="'.$obj2->localauthorityname.'">' . '</td>';
                print '<td><input type="text" name="active" value="'.$obj2->active.'">' . '</td>';
                print '<input type="hidden" name="rowid_to_keep" value="'.$obj2->rowid.'">';
                print '<td><input class="button" type="submit" name="submitButton" value="'.$langs->trans("Modify"). '">'. '</td>';
                print '<input type="hidden" name="action" value="update_localauthority">';
                print '<input type="hidden" name="rowid_to_keep" value="'.$obj2->rowid.'">';
                print '</tr>';
                print '</form>';
            }

            $obj = $db->fetch_object($resql);   
            print '<tr class="oddeven" id="rowid-'.$obj->rowid.'">';

            print '<td>'.$obj->localauthoritycode.'</td>';

            print '<td>'.$obj->localauthorityname.'</td>';
           
            print '<td align="left"><a href="'.$_SERVER['PHP_SELF'].'?action='.$acts[$obj->active] . '&rowid='.$obj->rowid.'&token='.newtoken().'">'.$actl[$obj->active].'</a></td>';

            print '<td>';
            print '<a href="'.$_SERVER['PHP_SELF'].'?action=delete&rowid='.$obj->rowid.'&token='.newtoken().'">'.img_delete().'</a>';
            print '</td>';

            print '</tr>';

            $i++;
        }

    }

}
else
{
    dol_print_error($db);
}

print '</form>';
print '</table>';

// Page end
dol_fiche_end();
llxFooter();
