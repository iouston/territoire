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
 *  \file       htdocs/territoire/class/html.form.localauthority.class.php
 *  \ingroup    territoire
 *  \brief      File of class to manage form for local authorities
 */
dol_include_once("/territoire/class/localauthority.class.php");
dol_include_once("/categories/class/categorie.class.php");


class LocalAuthorityForm
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
     *    Return combo list of local authority codes
     *    @param     selected               Id preselected products set
     *    @param     htmlname               Name of html select object
     *    @param     htmloption             Options html on select object
     *    @param     use_empty              1 to display empty field, 0 not to
     *    @return    string                 HTML string with select
     */
    function select_localauthoritycode($selected='',$htmlname='search_localauthoritycode', $htmloption='',$use_empty=1)
    {
        global $db, $conf, $langs;

        $localAuth = new LocalAuthority($db);

        $localAuthListe = $localAuth->fetch();
       
        //Build select
        $select = '<select class="flat" id = "'.$htmlname.'" name = "'.$htmlname.'" '.$htmloption.'>';
        if (is_array($localAuthListe) && sizeof($localAuthListe))
        {
            
            if($use_empty==1)
            {
                $select .= '<option value="0">&nbsp</option>';
            }
           
            foreach ($localAuthListe as $localAuth)
            {                    
                    $select .= '<option value="'.$localAuth->localauthoritycode.'" '.($localAuth->localauthoritycode == $selected ? 'selected="selected"' : '').'>'.$localAuth->localauthoritycode.'</option>';
              
            }            
        } 
        
        $select .= '</select>';

        return $select;
    }

        
/**
     *    Return combo list of local authority names
     *    @param     selected               Id preselected products set
     *    @param     htmlname               Name of html select object
     *    @param     htmloption             Options html on select object
     *    @param     use_empty              1 to display empty field, 0 not to
     *    @return    string                 HTML string with select
     */
    function select_localauthorityname($selected='',$htmlname='search_localauthorityname', $htmloption='',$use_empty=1)
    {
        global $db, $conf, $langs;

        $localAuth = new LocalAuthority($db);

        $localAuthListe = $localAuth->fetch();
       
        //Build select
        $select = '<select class="flat" id = "'.$htmlname.'" name = "'.$htmlname.'" '.$htmloption.'>';
        if (is_array($localAuthListe) && sizeof($localAuthListe))
        {
            
            if($use_empty==1)
            {
                $select .= '<option value="0">&nbsp</option>';
            }
           
            foreach ($localAuthListe as $localAuth)
            {                    
                    $select .= '<option value="'.$localAuth->localauthorityname.'" '.($localAuth->localauthorityname == $selected ? 'selected="selected"' : '').'>'.$localAuth->localauthorityname.'</option>';
              
            }            
        } 
        
        $select .= '</select>';

        return $select;
    }

        
}

