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
dol_include_once("/territoire/class/laacctotown.class.php");
dol_include_once("/territoire/class/localauthority.class.php");
dol_include_once("/territoire/class/html.form.localauthority.class.php");
dol_include_once("/territoire/class/html.form.laacctotown.class.php");

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
$laAccToTown = new LAAccToTown($db);
$localAuthForm = new LocalAuthorityForm($db);
$laacctoTownForm = new LAAccToTownForm($db);
$search_localauthoritycode = GETPOST('search_localauthoritycode', 'alpha');
$search_geog_code = GETPOST('search_geog_code', 'alpha');


/*
 * Actions
 */
//To update a town according to local authority ("collectivite"):
if($action == 'update')
{
    $laAccToTown->fk_localauthoritycode = GETPOST('new_localauthority', 'alpha');
    $laAccToTown->fk_code = GETPOST('code', 'alpha');
    $laAccToTown->updateFromCode($laAccToTown->fk_code, $user, 'update');
    setEventMessages($langs->trans("cityUpdated"), null, 'mesgs');
}


//To delete town linked to a local authority:
if ($action == 'confirm_delete' && $confirm == 'yes')
{
    $laAccToTown->fk_code = GETPOST('code','alpha');
    $laAccToTown->fetchFromCode($laAccToTown->fk_code);
    $laAccToTown->delete($user);
    setEventMessages($langs->trans("cityDeleted"), null, 'mesgs');
}

//To link a town to a local authority:
if($action == 'create_new_LAAccToTown')
{
    $code = GETPOST('townzip_id', 'alpha');
    $locAuthId = GETPOST('locauth_id', 'alpha');

    if($code == 0)
    {
        setEventMessages($langs->trans("ErrorNoCityEntered"), null, 'errors');
    }
    else
    {
        //Check if the fk_code (geographical code) is already entered in the database, in the llx_c_territoire_collecparville table:
            $sql = 'SELECT c.rowid, fk_code, localauthoritycode, '; 
            $sql .= 'localauthorityname, fk_localauthoritycode '; 
            $sql .= 'FROM '. MAIN_DB_PREFIX .'c_territoire_collecparville as c '; 
            $sql .= 'LEFT JOIN '. MAIN_DB_PREFIX .'c_territoire_collectivites '; 
            $sql .= 'ON localauthoritycode = fk_localauthoritycode';
               

            $resql = $db->query($sql);

            $fk_codeAlreadyEntered = false;

            if ($resql)
            {
                $num = $db->num_rows($resql);

                 if ($num)
                 {
                      $i = 0;
                       // Lines with values
                      while ($i < $num)
                      {
                          $obj = $db->fetch_object($resql);  

                          if($obj->fk_code == $code)
                          {
                            $fk_codeAlreadyEntered = true;
                            $locAuthAlreadyEntered = $obj->localauthorityname;
                          } 
                          ++$i;
                      }
                 }
            }

            //if the geographical code isn't already entered 
            //in the DB: create the new laAccToTown
            $laAccToTown->fk_localauthoritycode = $locAuthId;
            $laAccToTown->fk_code = $code;
            
            if($fk_codeAlreadyEntered == true)
            {
               setEventMessages($langs->trans("ErrorCityAlreadyEntered") . $locAuthAlreadyEntered, null, 'errors');
            }
            else
            {
                $laAccToTown->fk_code = $code;

                $laAccToTown->create($user);
                setEventMessages($langs->trans("cityCreated"), null, 'mesgs');

            }
        }
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
    "territoire@territoire"
);

//Asking a confirmation before deleting the town linked to the local authority:
if ($action == 'delete')
{
    $code = GETPOST('code', 'alpha');
    print $form->formconfirm($_SERVER["PHP_SELF"].'?code='.$code.$paramwithsearch, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_delete','',0,1);
}

// Local Authority According to Town page goes here
echo $langs->trans("territoireSetupPage");

print load_fiche_titre($langs->trans("toAddTownToLocAuth"),'','');

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="create_new_LAAccToTown">';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td><label for="cityzip">'.  $langs->trans("cityzip") . '</label></td>';
print '<td><label for="locauthName">'.  $langs->trans("locauthName") . '</label></td>';
print '<td>Action';
print '</td>';
print '</tr>';
print '<tr>';
print '<td>';
$laacctoTownForm->selectTownZip('', 'townzip_id', '', 1) ;
print '</td>';
print '<td>' ;
 $laacctoTownForm->selectLocauth('', 'locauth_id', '', 1, '', 0, '', 3);
print '</td>';
print '<td><input type="submit" class="button" name="submitButton" value="'.$langs->trans("Add").'"></td>';
print '</tr>';
print '</table>';
print '</form>';



/**
* Display of the towns linked to the chosen local authority:
*/
print load_fiche_titre($langs->trans("cityList"),'','');


//Form filter
print '<form action="'.$_SERVER['PHP_SELF'].'?rowid='.$rowid.'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<div class="div-table-responsive">';
print '<table class="noborder" width="100%">';

// Title of lines
print '<tr class="liste_titre">';
print getTitleFieldOfList($langs->trans("locauthName"), 0, $_SERVER["PHP_SELF"], 'fk_code', '', $param, "align='left'", $sortfield, $sortorder);
print '</tr>';

// Title line with search boxes
print '<tr class="liste_titre_filter">';
//Geographical code :
print '<td class="liste_titre">';
$laacctoTownForm->selectLocauth('', 'search_localauthoritycode', '', 1, '', 0, '', 3);
print '</td>';
print '<td class="liste_titre" colspan="3" align="left">';
print $form->showFilterAndCheckAddButtons(0);
print '</td>';
print '</tr>';

if(!empty($search_localauthoritycode))
{ 
    $sql = "SELECT z.town, z.code, cv.fk_code, cv.fk_localauthoritycode, c.localauthorityname";
    $sql.= " FROM llx_c_ziptown as z";
    $sql.= " LEFT JOIN llx_c_territoire_collecparville as cv";
    $sql.= " ON cv.fk_code = z.code";
    $sql.= " LEFT JOIN llx_c_territoire_collectivites as c";
    $sql.= " ON cv.fk_localauthoritycode = c.localauthoritycode";
    $sql.= " WHERE c.entity = 1";
    $sql.= " AND cv.fk_localauthoritycode = " . $search_localauthoritycode;
    $sql.= " ORDER BY z.town ASC";


    $resql = $db->query($sql);

    if ($resql)
    {
        $num = $db->num_rows($resql);

        if ($num)
        {
            $i = 1;
            // Lines with values
            while ($i <= $num)
            {
                $obj = $db->fetch_object($resql);   
                print '<tr class="oddeven" id="code-'.$obj->rowid.'">';

                print '<td>'.$obj->town.'</td>';

                print '<td>';
                print '<a href="'.$_SERVER['PHP_SELF'].'?action=editfield&code='.$obj->code.'&town='. urlencode($obj->town).'&token='.newtoken().'">'.img_edit().'</a>';

                print '</td>';

                print '<td>';
                print '<a href="'.$_SERVER['PHP_SELF'].'?action=delete&code='.$obj->code.'&token='.newtoken().'">'.img_delete().'</a>';
                print '</td>';

                print '</tr>';

                $i++;
            }

        }

    }
}
else
{
    dol_print_error($db);
}

//To display the inputs to update a town linked to the chosen local authority:
if($action == 'editfield')
{
                print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                print '<input type="hidden" name="action" value="update">';
                print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
                print '<input type="hidden" name="code" value="'.GETPOST('code', 'alpha').'">';
                print '<tr class="oddeven">';
                print '<td> '. GETPOST('town', 'alpha') . '</td>';
                print '<td>';
                $laacctoTownForm->selectLocauth('', 'new_localauthority', '', 0, '', 0, '', 3);
                print '</td>';
                
               print '<td><input class="button" type="submit" name="submitButton" value="'.$langs->trans("Modify"). '">'. '</td>';

                print '</tr>';
                print '</form>';
}

print '</form>';
print '</table>';

// Page end
dol_fiche_end();
llxFooter();
