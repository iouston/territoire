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
$laAccToTown2 = new LAAccToTown($db); // VERIFIER SI NECESSAIRE //////////////////////////////////////////


/*
 * Actions
 */
if($action == 'update')
{
    $laAccToTown->fk_localauthoritycode = GETPOST('new_localauthority', 'int')
    $laAccToTown->fk_code = GETPOST('code', 'int');
    $laAccToTown->update($laAccToTown->rowid, $user, 'update');
}


if ($action == 'confirm_delete' && $confirm == 'yes')
{
    $laAccToTown->rowid = GETPOST('rowid', 'int');
    $laAccToTown->fetch($laAccToTown->rowid);
    $laAccToTown->delete($user);
}


if($action == 'create_new_LAAccToTown')
{
    $postcode = GETPOST('postcode', 'alpha');
    $cityName = GETPOST('cityName', 'alpha');
    $cityNameUpper = strtoupper($cityName);
    //$laAccToTown->fk_localauthoritycode = GETPOST('fk_localauthoritycode', 'alpha');
    $locauthName = GETPOST('locauthName', 'alpha');

    $pcAndCity = $postcode . ' ' . $cityNameUpper;
    $fk_code = 0;
    echo 'postcode =' . $postcode . ' - ';
    echo 'cityName =' . $cityName . ' - ';
    echo 'pcAndCity =' . $pcAndCity . ' - ';


    //Get back the fk_code (geographical code) from the city name and zip code
    $sql4 = 'SELECT rowid, code, zip, town FROM '. MAIN_DB_PREFIX .'c_ziptown';

    echo $sql4;

    $resql4 = $db->query($sql4);

    if ($resql4)
    {
        $num4 = $db->num_rows($resql4);

         if ($num4)
         {
              $i = 0;
               // Lines with values
              while ($i < $num4)
              {
                  $obj4 = $db->fetch_object($resql4);  

                  $pcAndCityDB = $obj4->zip .' ' . $obj4->town;
                  echo 'pcAndCityDB='. $pcAndCityDB . '</br>';


                  if($pcAndCity == $pcAndCityDB)
                  {

                    $fk_code = $obj4->code;
                  } 
                  ++$i;
              }
         }
    }
    echo 'fk_code after sql ='. $fk_code . ' - ';


    //Check if the fk_code (geographical code) is already entered in the database, in the llx_c_territoire_collecparville table:
    $sql2 = 'SELECT rowid, fk_code FROM '. MAIN_DB_PREFIX .'c_territoire_collecparville';

    $resql2 = $db->query($sql2);

    $fk_codeAlreadyEntered = false;

    if ($resql)
    {
        $num2 = $db->num_rows($resql2);

         if ($num2)
         {
              $i = 0;
               // Lines with values
              while ($i < $num2)
              {
                  $obj2 = $db->fetch_object($resql2);  

                  if($obj2->fk_code == $fk_code)
                  {
                    $fk_codeAlreadyEntered = true;
                  } 
                  ++$i;
              }
         }
    }

    echo 'fk_codeAlreadyEntered =' . $fk_codeAlreadyEntered;


    //Check if the localauthority is already entered in the database in the llx_c_territoire_collectivites table:
    $sql3 = 'SELECT rowid, localauthoritycode, localauthorityname FROM '. MAIN_DB_PREFIX .'c_territoire_collectivites';

    $resql3 = $db->query($sql3);

    if ($resql3)
    {
        $num3 = $db->num_rows($resql3);

         if ($num3)
         {
              $i = 0;
               // Lines with values
              while ($i < $num3)
              {
                  $obj3 = $db->fetch_object($resql3);  

                  if($obj3->localauthorityname == $locauthName)
                  {
                    $laAccToTown2->fk_localauthoritycode = $obj3->localauthoritycode;
                  } 
                  ++$i;
              }
         }
    }


    //If yes: error message, if not: create the new laAccToTown
    echo '</br>';
    
    echo '$laAccToTown2->fk_localauthoritycode =' . $laAccToTown2->fk_localauthoritycode;
    echo '</br>';
    
    if($fk_codeAlreadyEntered == true)
    {
        echo 'Erreur : la ville possède déjà une collectivité';
    }
    else
    {
        echo 'ELSE ';
        $laAccToTown2->fk_code = $fk_code;

        $laAccToTown2->create($user);

    }

    echo '$laAccToTown2->fk_code=' . $laAccToTown2->fk_code;
    echo '</br>';



    //
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

if ($action == 'delete')
{
    print $form->formconfirm($_SERVER["PHP_SELF"].'?rowid='.$rowid.$paramwithsearch, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_delete','',0,1);
}

// Local Authority According to Town page goes here
echo $langs->trans("territoireSetupPage");

print load_fiche_titre($langs->trans("toAddALAAccToTown"),'','');

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="create_new_LAAccToTown">';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
//print '<td><label for="fk_code">'.  $langs->trans("fk_code") . '</label></td>';
//print '<td><label for="postcode">'.  $langs->trans("postcode") . '</label></td>';
print '<td><label for="cityzip">'.  $langs->trans("cityzip") . '</label></td>';
//print '<td><label for="cityName">'.  $langs->trans("cityName") . '</label></td>';
// print '<td><label for="fk_localauthoritycode">'.  $langs->trans("fk_localauthoritycode") . '</label></td>';
print '<td><label for="locauthName">'.  $langs->trans("locauthName") . '</label></td>';
print '<td>Action';
print '</td>';
print '</tr>';
print '<tr>';
//print '<td><input type="text" name="postcode" value="" placeholder="'.$langs->trans('postcodePlaceholder').'"></td>';
print '<td>';
$laacctoTownForm->selectTownZip('', '', '', 1) ;
print '</td>';
//print '<td><input type="text" name="cityName" value="" placeholder="'.$langs->trans('cityNamePlaceholder').'"></td>';
// print '<td><input type="text" name="fk_localauthoritycode" value="" placeholder="'.$langs->trans('fk_LAcodePlaceholderLabel').'"></td>';
print '<td>' ;
 $laacctoTownForm->selectLocauth('', 'locauth_id', '', 0, '', 0, '', 3);
print '</td>';
//print '<td><input type="text" name="locauthName" value="" placeholder="'.$langs->trans('locauthNamePlaceholder').'"></td>';
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
print getTitleFieldOfList($langs->trans("geographicalCode"), 0, $_SERVER["PHP_SELF"], 'fk_code', '', $param, "align='left'", $sortfield, $sortorder);
print getTitleFieldOfList($langs->trans("localAuthorityCode"), 0, $_SERVER["PHP_SELF"], 'fk_localauthoritycode', '', $param, "align='left'", $sortfield, $sortorder);
print '</tr>';

// Title line with search boxes
print '<tr class="liste_titre_filter">';
//Geographical code :
print '<td class="liste_titre">';
$laacctoTownForm->selectLocauth('', 'search_localauthoritycode', '', 0, '', 0, '', 3);
// print $laacctoTownForm->select_geog_code($search_geog_code, 'search_geog_code','', 1);
print '</td>';
// //Local authority code:
// print '<td class="liste_titterritre">';
// print $localAuthForm->select_localauthoritycode($search_localauthoritycode, 'search_localauthoritycode','', 1);
// print '&nbsp;';
// print '</td>';

print '<td class="liste_titre" colspan="3" align="right">';
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
            while ($i < $num)
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

if($action == 'editfield')
{
                print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                print '<input type="hidden" name="action" value="update">';
                print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
                print '<input type="hidden" name="code" value="'.GETPOST('code', 'int').'">';
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
