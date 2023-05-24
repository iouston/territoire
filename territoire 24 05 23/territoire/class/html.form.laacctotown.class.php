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
 *  \file       htdocs/territoire/class/html.form.laactotown.class.php
 *  \ingroup    territoire
 *  \brief      File of class to manage form for towns according to local authorities
 */
dol_include_once("/vacscol/class/laacctotown.class.php");
dol_include_once("/categories/class/categorie.class.php");


class LAAccToTownForm
{
    var $db;
    var $error;

    /**
     * Constructor
     * @param      $db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
    }

        
/**
     *    Return combo list of products sets
     *    @param     selected               Id preselected products set
     *    @param     htmlname               Name of html select object
     *    @param     htmloption             Options html on select object
     *    @param     use_empty              1 to display empty, 0 not to
     *    @return    string                 HTML string with select
     */
    function select_geog_code($selected='',$htmlname='search_geog_code', $htmloption='',$use_empty=1)
    {
        global $db, $conf, $langs;

        $lacToTown = new LAAccToTown($db);

        $lacToTownListe = $lacToTown->fetch();
       
        //Build select
        $select = '<select class="flat" id = "'.$htmlname.'" name = "'.$htmlname.'" '.$htmloption.'>';
        if (is_array($lacToTownListe) && sizeof($lacToTownListe))
        {
            
            if($use_empty==1)
            {
                $select .= '<option value="0">&nbsp;</option>';
            }
           
            foreach ($lacToTownListe as $lacToTown)
            {                    
                    $select .= '<option value="'.$lacToTown->fk_code.'" '.($lacToTown->fk_code == $selected ? 'selected="selected"' : '').'>'.$lacToTown->fk_code.'</option>';
              
            }            
        } 
        
        $select .= '</select>';

        return $select;
    }

 
//Function to search for the town followed by the zip code with predictive text
//The user has to enter at least 3 letters to activate the research      
public function selectTownZip($selected = '', $htmlname = 'townzip_id', $filtre = '', $useempty = 1, $moreattrib = '', $noinfoadmin = 0, $morecss = '',$minautocomplete=3)
   {
     global $langs, $conf, $user;
  
     $langs->load("admin");

     $sql = "SELECT code, zip, town";
     $sql .= " FROM ".$this->db->prefix()."c_ziptown";
     $sql .= " WHERE active > 0";
     $sql .= " AND code is not null"; 
     if ($filtre) {
       $sql .= " AND ".$filtre;
     }
     $sql .= " ORDER BY town ASC, zip ASC";
  
     dol_syslog(get_class($this)."::selectTownZip", LOG_DEBUG);
     $result = $this->db->query($sql);
     if ($result) {
       $num = $this->db->num_rows($result);
       $i = 0;
       if ($num) 
       {       
            print '<select id="select'.$htmlname.'" class="flat selectshippingmethod'.($morecss ? ' '.$morecss : '').'" name="'.$htmlname.'"'.($moreattrib ? ' '.$moreattrib : '').'>';
           if ($useempty == 1 )
           {
              print '<option value="0">&nbsp;</option>';
           }
           while ($i < $num) 
           {
               $obj = $this->db->fetch_object($result);
               if ($selected == $obj->code) {
                    print '<option value="'.$obj->code.'" selected>';
               } 
               else 
               {
                    print '<option value="'.$obj->code.'">';
               }
               print $obj->town . ' - ' . $obj->zip;
               print '</option>';
               $i++;
         }
         print "</select>";
         if ($user->admin  && empty($noinfoadmin)) {
           print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"), 1);
         }
  
         print ajax_combobox('select'.$htmlname,'',$minautocomplete);
       } else {
         print $langs->trans("NoSelectTownDefined");
       }
     } else {
       dol_print_error($this->db);
     }
   }


//Function to search for the local authority ("intercommunalité") with predictive text
//The user has to enter at least 3 letters to activate the research      
public function selectLocauth($selsociected = '', $htmlname = 'search_localauthoritycode', $filtre = '', $useempty = 1, $moreattrib = '', $noinfoadmin = 0, $morecss = '',$minautocomplete=3)
   {
     global $langs, $conf, $user;
  
     $langs->load("admin");

     $sql = "SELECT localauthoritycode, localauthorityname, entity";
     $sql .= " FROM ".$this->db->prefix()."c_territoire_collectivites";
     $sql .= " WHERE active > 0";
     $sql .= " AND entity = ". $conf->entity;
     if ($filtre) 
     {
        $sql .= " AND ".$filtre;
     }
     $sql .= " ORDER BY localauthorityname ASC";

     dol_syslog(get_class($this)."::selectTownZip", LOG_DEBUG);
     $result = $this->db->query($sql);
     if ($result) 
     {
       $num = $this->db->num_rows($result);
       $i = 0;
       if ($num) 
       {    
          print '<select id="select'.$htmlname.'" class="flat selectshippingmethod'.($morecss ? ' '.$morecss : '').'" name="'.$htmlname.'"'.($moreattrib ? ' '.$moreattrib : '').'>';
          if ($useempty == 1 )
          {
             print '<option value="0">&nbsp;</option>';
          }
          while ($i < $num) {
              $obj = $this->db->fetch_object($result);
              if ($selected == $obj->localauthoritycode) {
                 print '<option value="'.$obj->localauthoritycode.'" selected>';
              } 
              else 
              {
                  print '<option value="'.$obj->localauthoritycode.'">';
              }
              print $obj->localauthorityname;
              print '</option>';
              $i++;
         }
         print "</select>";
         if ($user->admin  && empty($noinfoadmin)) {
           print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"), 1);
         }
  
         print ajax_combobox('select'.$htmlname,'',$minautocomplete);
       } else {
         print $langs->trans("NoSelectTownDefined");
       }
     } else {
       dol_print_error($this->db);
     }
   }


}

