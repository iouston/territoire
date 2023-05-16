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
 * 	\defgroup   territoire     Module territoire
 *  \brief      territoire module descriptor.
 *
 *  \file       htdocs/territoire/core/modules/modterritoire.class.php
 *  \ingroup    territoire
 *  \brief      Description and activation file for module territoire
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';

dol_include_once("/territoire/class/interco.class.php");

// The class name should start with a lower case mod for Dolibarr to pick it up
// so we ignore the Squiz.Classes.ValidClassName.NotCamelCaps rule.
// @codingStandardsIgnoreStart
/**
 *  Description and activation class for module territoire
 */
class modTerritoire extends DolibarrModules
{
	// @codingStandardsIgnoreEnd
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
        global $langs,$conf;

        $this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 446256;		// TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve id number for your module
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'territoire';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','interface','other'
		// It is used to group modules by family in module setup page
		$this->family = "iouston";
		// Module position in the family
		$this->module_position = 500;
		// Gives the possibility to the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '001', 'label' => $langs->trans("MyOwnFamily")));

		// Module label (no space allowed), used if translation string 'ModuleterritoireName' not found (MyModue is name of module).
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleterritoireDesc' not found (MyModue is name of module).
		$this->description = 'territoire';
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "Module permettant d'associer une ville avec ses intercommunalités";

		$this->editor_name = 'iouston informatique';
		$this->editor_url = 'http://www.iouston.com';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0.0';
		$this->url_last_version = 'https://www.iouston.com/dolibarr_modules_version/frais-de-transport.txt';
		// Key used in llx_const table to save module status enabled/disabled (where SITFAC is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='territoire16@territoire';

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /territoire/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /territoire/core/modules/barcode)
		// for specific css file (eg: /territoire/css/territoire.css.php)
		$this->module_parts = array(
		                        	'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
									'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
									'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
									'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
									'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
		                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
									'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
									'models' => 0,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
									'css' => array(),	// Set this to relative path of css file if module has its own css file
	 								'js' => array(),          // Set this to relative path of js file if module must load a js on all pages
									'hooks' => array('thirdpartycard') 	// Set here all hooks context managed by module. You can also set hook context 'all'
		                        );

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/territoire/temp","/territoire/subdir");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into territoire/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@territoire");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of module class names as string that must be enabled if this module is enabled
		$this->requiredby = array();	// List of module ids to disable if this one is disabled
		$this->conflictwith = array();	// List of module class names as string this module is in conflict with
		$this->phpmin = array(5,3);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(4,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("territoire@territoire");
		$this->warnings_activation = array();                     // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
		$this->warnings_activation_ext = array();                 // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','ES'='textes'...)

		$this->const = array();

        $this->tabs = array();  	// To add a new tab identified by code tabname1

		if (! isset($conf->territoire) || ! isset($conf->territoire->enabled))
        {
        	$conf->territoire=new stdClass();
        	$conf->territoire->enabled=0;
        }

        // Dictionaries
		$this->dictionaries=array(
			'langs' => 'territoire',
            'tabname' => array(
				"c_territoire_collectivites",
				"c_territoire_collecparville"
			),
            'tablib' => array(
				"CollectivitesTable",
				"CollectivitesParVilleTable"
			),		// Label of tables
            'tabsql' => array(
				'SELECT f.rowid as rowid, f.localauthoritycode, f.localauthorityname, f.active, f.entity, f.tms FROM '.MAIN_DB_PREFIX.'c_territoire_collectivites as f',
				'SELECT f.rowid as rowid, f.fk_code, f.fk_localauthoritycode, f.entity, f.tms FROM '.MAIN_DB_PREFIX.'c_territoire_collecparville as f'
 			),	// Request to select fields
            'tabsqlsort' => array(
				"rowid ASC",
				"rowid ASC"
			),
				//Sort order
            'tabfield' => array(
				"localauthoritycode, localauthorityname",
				"fk_code, fk_localauthoritycode"
			),														
			   // List of fields (result of select to show dictionary)
            'tabfieldvalue' => array(
				"localauthoritycode, localauthorityname",
				"fk_code, fk_localauthoritycode"
			),														
				// List of fields (list of fields to edit a record)
            'tabfieldinsert' => array(
				"localauthoritycode, localauthorityname",
				"fk_code, fk_localauthoritycode"
			),														
				// List of fields (list of fields for insert)
            'tabrowid' => array(
				"rowid",
				"rowid"
			),																									// Name of columns with primary key (try to always name it 'rowid')
            'tabcond' => array(
				$conf->territoire->enabled,
				$conf->territoire->enabled,
			)				
		);


        // Boxes/Widgets
		// Add here list of php file(s) stored in territoire/core/boxes that contains class to show a widget.
        $this->boxes = array();


		// Cronjobs (List of cron jobs entries to add when module is enabled)
		$this->cronjobs = array();


		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r = 0;
		$this->rights[$r][0] = 446256;
		// $this->rights[$r][1] = 'Mettre à jour taxe gasoil mensuelle';
		// $this->rights[$r][2] = 'r';
		// $this->rights[$r][3] = 0;
		// $this->rights[$r][4] = 'updategastax';
        // $this->rights[$r][5] = '';
				
		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r = 0;
        $this->menu[$r]=array(
            'fk_menu'=>'fk_mainmenu=billing',			// Put 0 if this is a top menu
        	'type'=> 'left',			// This is a Top menu entry
        	'titre'=> $langs->trans('TransportCosts'),
        	'mainmenu'=> 'billing',
        	'leftmenu'=> 'transportCosts',		// Use 1 if you also want to add left menu entries using this descriptor. Use 0 if left menu entries are defined in a file pre.inc.php (old school).
			'url'=> '/territoire/transport_cout_control.php',
			'langs'=> 'territoire@territoire',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=> 101,
			'enabled'=> '1',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=> '',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=> '',
			'user'=> 2
		);
		$r++;

        $this->menu[$r]=array(
            'fk_menu'=>'fk_mainmenu=billing,fk_leftmenu=transportCosts',			// Put 0 if this is a top menu
        	'type'=> 'left',			// This is a Top menu entry
        	'titre'=> $langs->trans('TransportControlCosts'),
        	'mainmenu'=> 'billing',
        	'leftmenu'=> 'transportCosts',		// Use 1 if you also want to add left menu entries using this descriptor. Use 0 if left menu entries are defined in a file pre.inc.php (old school).
			'url'=> '/territoire/transport_cout_control.php',
			'langs'=> 'territoire@territoire',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=> 102,
			'enabled'=> '1',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=> '',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=> '',
			'user'=> 2
		);
		$r++;

		$this->menu[$r]=array(
            'fk_menu'=>'fk_mainmenu=billing,fk_leftmenu=transportCosts',			// Put 0 if this is a top menu
        	'type'=> 'left',			// This is a Top menu entry
        	'titre'=> $langs->trans('TransportUpdateGasTax'),
        	'mainmenu'=> 'billing',
        	'leftmenu'=> 'transportCosts',		// Use 1 if you also want to add left menu entries using this descriptor. Use 0 if left menu entries are defined in a file pre.inc.php (old school).
			'url'=> '/territoire/admin/gastax.php',
			'langs'=> 'territoire@territoire',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=> 103,
			'enabled'=> '1',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
			'perms'=> '$user->rights->territoire->updategastax',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
			'target'=> '',
			'user'=> 2
		);

		// Exports
		//--------
		// $r=0;

		// // Export list of transporteur
		// $r++;
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='ExportterritoireTransporteur';
		// $this->export_icon[$r]=$this->picto;
		// $this->export_permission[$r]=array(array("societe","export"));
		// $this->export_fields_array[$r]=array(
		// 	't.rowid'=>"Id",
		// 	't.label'=>"Name",
		// 	't.ref'=>"Ref",
		// 	's.rowid'=>"Societe_ID",
		// 	's.nom'=>"Societe_Name",
		// 	't.datec'=>"CreationDate",
		// 	't.fk_product'=>"AssociatedProduct",
		// 	't.out_of_range'=>"OutOfRange",
		// 	't.subject_to_fuel_tax'=>"SubjectToFuelTax",
		// 	't.tms'=>"Tms",
		// 	't.entity'=>"Entity",
		// 	't.active'=>"Active",
		// 	't.fk_shipping_method'=>"ShippingType",
		// 	'sh.libelle'=>"ShippingTypeName",
		// 	't.usesaison'=>"UseSaison",
		// );
		// $this->export_TypeFields_array[$r]=array(
		// 	't.rowid'=>"Numeric",
		// 	 't.label'=>"Text",
		// 	 't.ref'=>"Text",
		// 	 's.rowid'=>"Numeric",
		// 	 's.nom'=>"Text",
		// 	 't.datec'=>"Date",
		// 	 't.fk_product'=>"Numeric",
		// 	 't.out_of_range'=>"Numeric",
		// 	 't.subject_to_fuel_tax'=>"Numeric",
		// 	 't.tms'=>"Date",'t.entity'=>"Numeric",
		// 	 't.active'=>"Numeric",
		// 	 't.fk_shipping_method'=>"Numeric",
		// 	 'sh.libelle'=>"Text",
		// 	 't.usesaison'=>"Numeric",
		// 	);
		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'c_transporteurs as t';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON s.rowid = t.fk_soc';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_shipment_mode as sh ON sh.rowid = t.fk_shipping_method';
		// $this->export_sql_end[$r] .=' ORDER BY t.rowid';

		// // Export departments 
		// $r++;
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='ExportterritoireZoneDepartement';
		// $this->export_icon[$r]=$this->picto;
		// $this->export_permission[$r]=array(array("societe","export"));
		// $this->export_fields_array[$r]=array(
		// 	'z.rowid'=>"ZoneId",
		// 	'co.nom'=>"ZoneName", 
		// 	'co.code_departement'=>"DepartmentCode", 
		// 	'co.rowid' => "DepartmentId",
		// 	'z.entity'=>"Entity", 
		// 	'z.ismountain'=>"ismountain", 
		// 	'z.ishighmountain'=>"ishighmountain", 
		// 	'z.isonisland'=>"isonisland", 
		// 	'z.seasondaynumberstart'=>"seasondaynumberstart", 
		// 	'z.seasonmonthnumberstart'=>"seasonmonthnumberstart",
		// 	'z.seasondaynumberend'=>"seasondaynumberend", 
		// 	'z.seasonmonthnumberend'=>"seasonmonthnumberend",
		// 	'z.seasonrate'=>"seasonrate" 
		// );
		
		// $this->export_TypeFields_array[$r]=array(
		// 	'z.rowid'=>"Numeric", 
		// 	'co.nom'=>"Text",
		// 	'co.code_departement'=>"Text", 
		// 	'co.rowid'=>"Numeric",
		// 	'z.entity'=>"Numeric",
		// 	'z.ismountain'=>"Numeric", 
		// 	'z.ishighmountain'=>"Numeric", 
		// 	'z.isonisland'=>"Numeric", 
		// 	'z.seasondaynumberstart'=>"Numeric", 
		// 	'z.seasonmonthnumberstart'=>"Numeric",
		// 	'z.seasondaynumberend'=>"Numeric", 
		// 	'z.seasonmonthnumberend'=>"Numeric",
		// 	'z.seasonrate'=>"Numeric"
		// );

		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'c_zones_departements as z';
        // $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_departements as co ON co.rowid = z.fk_object';
		// $this->export_sql_end[$r] .=' ORDER BY co.nom';

		// // Export regions
		// $r++;
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='ExportterritoireZoneRegion';
		// $this->export_icon[$r]=$this->picto;
		// $this->export_permission[$r]=array(array("societe","export"));
		// $this->export_fields_array[$r]=array(
		// 	'z.rowid'=>"ZoneId",
		// 	'co.nom'=>"ZoneName", 
		// 	'co.code_region'=>"RegionCode", 
		// 	'co.rowid' => "RegionId",
		// 	'z.entity'=>"Entity",
		// 	'z.ismountain'=>"Ismountain", 
		// 	'z.ishighmountain'=>"Ishighmountain", 
		// 	'z.isonisland'=>"Isonisland", 
		// 	'z.seasondaynumberstart'=>"Seasondaynumberstart", 
		// 	'z.seasonmonthnumberstart'=>"Seasonmonthnumberstart",
		// 	'z.seasondaynumberend'=>"Seasondaynumberend", 
		// 	'z.seasonmonthnumberend'=>"Seasonmonthnumberend",
		// 	'z.seasonrate'=>"Seasonrate" 
		// );
		
		// $this->export_TypeFields_array[$r]=array(
		// 	'z.rowid'=>"Numeric", 
		// 	'co.nom'=>"Text",
		// 	'co.code_region'=>"Text", 
		// 	'co.rowid'=>"Numeric",
		// 	'z.entity'=>"Numeric",
		// 	'z.ismountain'=>"Numeric", 
		// 	'z.ishighmountain'=>"Numeric", 
		// 	'z.isonisland'=>"Numeric", 
		// 	'z.seasondaynumberstart'=>"Numeric", 
		// 	'z.seasonmonthnumberstart'=>"Numeric",
		// 	'z.seasondaynumberend'=>"Numeric", 
		// 	'z.seasonmonthnumberend'=>"Numeric",
		// 	'z.seasonrate'=>"Numeric" 
		// );

		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'c_zones_regions as z';
        // $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_regions as co ON co.rowid = z.fk_object';
		// $this->export_sql_end[$r] .=' ORDER BY co.nom';

		// // Export countries
		// $r++;
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='ExportterritoireZonePays';
		// $this->export_icon[$r]=$this->picto;
		// $this->export_permission[$r]=array(array("societe","export"));
		// $this->export_fields_array[$r]=array(
		// 	'z.rowid'=>"ZoneId",
		// 	'co.label'=>"ZoneName", 
		// 	'co.code'=>"CountryCode", 
		// 	'co.rowid' => "CountryId",
		// 	'z.entity'=>"Entity",
		// 	'z.ismountain'=>"Ismountain", 
		// 	'z.ishighmountain'=>"Ishighmountain", 
		// 	'z.isonisland'=>"Isonisland", 
		// 	'z.seasondaynumberstart'=>"Seasondaynumberstart", 
		// 	'z.seasonmonthnumberstart'=>"Seasonmonthnumberstart",
		// 	'z.seasondaynumberend'=>"Seasondaynumberend", 
		// 	'z.seasonmonthnumberend'=>"Seasonmonthnumberend",
		// 	'z.seasonrate'=>"Seasonrate"

		// );
		
		// $this->export_TypeFields_array[$r]=array(
		// 	'z.rowid'=>"Numeric", 
		// 	'co.label'=>"Text",
		// 	'co.code'=>"Text", 
		// 	'co.rowid'=>"Numeric",
		// 	'z.entity'=>"Numeric",
		// 	'z.ismountain'=>"Numeric", 
		// 	'z.ishighmountain'=>"Numeric", 
		// 	'z.isonisland'=>"Numeric", 
		// 	'z.seasondaynumberstart'=>"Numeric", 
		// 	'z.seasonmonthnumberstart'=>"Numeric",
		// 	'z.seasondaynumberend'=>"Numeric", 
		// 	'z.seasonmonthnumberend'=>"Numeric",
		// 	'z.seasonrate'=>"Numeric"
		// );

		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'c_zones_pays as z';
        // $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_country as co ON co.rowid = z.fk_object';
		// $this->export_sql_end[$r] .=' ORDER BY co.label';


		// // Export list of slices weight and amount
		// $r++;
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='ExportterritoireZoneDepartementPoidsTarif';
		// $this->export_icon[$r]=$this->picto;
		// $this->export_permission[$r]=array(array("societe","export"));
		// $this->export_fields_array[$r]=array(
		// 	't.rowid'=>"Id",
		// 	't.fk_transporteur'=>"TransporterId",
		// 	't.fk_zone'=>"ZoneId",
		// 	'co.nom'=>"ZoneName",
		// 	't.poids_min'=>"MinWeight",
		// 	't.poids_max'=>"MaxWeight",
		// 	't.fk_unit_min'=>"MinUnit",
		// 	't.fk_unit_max'=>"MaxUnit",
		// 	't.montant'=>"Amount",
		// 	't.datec'=>"CreationDate",
		// 	't.tms'=>"Tms",
		// 	't.poids_reel'=>"Poidsreel",
		// 	't.roundsup10'=>"AroundSup10",
		// 	't.div100'=>"DivBy100",
		// 	't.active'=>"Active",
		// 	't.entity'=>"Entity");
		
		// $this->export_TypeFields_array[$r]=array(
		// 	't.rowid'=>"Numeric",
		// 	't.fk_transporteur'=>"Numeric",
		// 	't.fk_zone'=>"Numeric",
		// 	'co.nom'=>"Text",
		// 	't.poids_min'=>"Numeric",
		// 	't.poids_max'=>"Numeric",
		// 	't.fk_unit_min'=>"Numeric",
		// 	't.fk_unit_max'=>"Numeric",
		// 	't.montant'=>"Numeric",
		// 	't.datec'=>"Date",
		// 	't.tms'=>"Date",
		// 	't.active'=>"Numeric",
		// 	't.poids_reel'=>"Numeric",
		// 	't.roundsup10'=>"Numeric",
		// 	't.div100'=>"Numeric",
		// 	't.entity'=>"Numeric"
		// );

		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'c_tranches_poids_departements as t';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_zones_departements as z ON t.fk_zone = z.rowid';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_departements as co ON co.rowid = z.fk_object';

		// $r++;
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='ExportterritoireZoneRegionPoidsTarif';
		// $this->export_icon[$r]=$this->picto;
		// $this->export_permission[$r]=array(array("societe","export"));
		// $this->export_fields_array[$r]=array(
		// 	't.rowid'=>"Id",
		// 	't.fk_transporteur'=>"TransporterId",
		// 	't.fk_zone'=>"ZoneId",
		// 	'co.nom'=>"ZoneName",
		// 	't.poids_min'=>"MinWeight",
		// 	't.poids_max'=>"MaxWeight",
		// 	't.fk_unit_min'=>"MinUnit",
		// 	't.fk_unit_max'=>"MaxUnit",
		// 	't.montant'=>"Amount",
		// 	't.datec'=>"CreationDate",
		// 	't.tms'=>"Tms",
		// 	't.poids_reel'=>"Poidsreel",
		// 	't.roundsup10'=>"AroundSup10",
		// 	't.div100'=>"DivBy100",
		// 	't.active'=>"Active",
		// 	't.entity'=>"Entity"
		// );
		// $this->export_TypeFields_array[$r]=array(
		// 	't.rowid'=>"Numeric",
		// 	't.fk_transporteur'=>"Numeric",
		// 	't.fk_zone'=>"Numeric",
		// 	'co.nom'=>"Text",
		// 	't.poids_min'=>"Numeric",
		// 	't.poids_max'=>"Numeric",
		// 	't.fk_unit_min'=>"Numeric",
		// 	't.fk_unit_max'=>"Numeric",
		// 	't.montant'=>"Numeric",
		// 	't.datec'=>"Date",
		// 	't.tms'=>"Date",
		// 	't.roundsup10'=>"Numeric",
		// 	't.div100'=>"Numeric",
		// 	't.active'=>"Numeric",
		// 	't.entity'=>"Numeric"
		// );
		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'c_tranches_poids_regions as t';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_zones_regions as z ON t.fk_zone = z.rowid';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_regions as co ON co.rowid = z.fk_object';

		// $r++;
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='ExportterritoireZonePaysPoidsTarif';
		// $this->export_icon[$r]=$this->picto;
		// $this->export_permission[$r]=array(array("societe","export"));
		// $this->export_fields_array[$r]=array(
		// 	't.rowid'=>"Id",
		// 	't.fk_transporteur'=>"TransporterId",
		// 	't.fk_zone'=>"ZoneId",
		// 	'co.label'=>"ZoneName",
		// 	't.poids_min'=>"MinWeight",
		// 	't.poids_max'=>"MaxWeight",
		// 	't.fk_unit_min'=>"MinUnit",
		// 	't.fk_unit_max'=>"MaxUnit",
		// 	't.montant'=>"Amount",
		// 	't.datec'=>"CreationDate",
		// 	't.tms'=>"Tms",
		// 	't.poids_reel'=>"Poidsreel",
		// 	't.roundsup10'=>"AroundSup10",
		// 	't.div100'=>"DivBy100",
		// 	't.active'=>"Active",
		// 	't.entity'=>"Entity"
		// );
		
		// $this->export_TypeFields_array[$r]=array(
		// 	't.rowid'=>"Numeric",
		// 	't.fk_transporteur'=>"Numeric",
		// 	't.fk_zone'=>"Numeric",
		// 	'co.label'=>"Text",
		// 	't.poids_min'=>"Numeric",
		// 	't.poids_max'=>"Numeric",
		// 	't.fk_unit_min'=>"Numeric",
		// 	't.fk_unit_max'=>"Numeric",
		// 	't.montant'=>"Numeric",
		// 	't.datec'=>"Date",
		// 	't.tms'=>"Date",
		// 	't.roundsup10'=>"Numeric",
		// 	't.div100'=>"Numeric",
		// 	't.active'=>"Numeric",
		// 	't.entity'=>"Numeric"
		// );
		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'c_tranches_poids_pays as t';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_zones_pays as z ON t.fk_zone = z.rowid';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_country as co ON co.rowid = z.fk_object';

		// // Export list of slices linear and amount
		// $r++;
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='ExportterritoireZoneDepartementMetreTarif';
		// $this->export_icon[$r]=$this->picto;
		// $this->export_permission[$r]=array(array("societe","export"));
		// $this->export_fields_array[$r]=array(
		// 	't.rowid'=>"Id",
		// 	't.fk_transporteur'=>"TransporterId",
		// 	't.fk_zone'=>"ZoneId",
		// 	'co.nom'=>"ZoneName",
		// 	't.linear_min'=>"MinLinear",
		// 	't.linear_max'=>"MaxLinear",
		// 	't.fk_unit_min'=>"MinUnitLinear",
		// 	't.fk_unit_max'=>"MaxUnitLinear",
		// 	't.montant'=>"Amount",
		// 	't.datec'=>"CreationDate",
		// 	't.tms'=>"Tms",
		// 	't.linear_reel'=>"Metrereel",
		// 	't.active'=>"Active",
		// 	't.entity'=>"Entity"
		// );
		// $this->export_TypeFields_array[$r]=array(
		// 	't.rowid'=>"Numeric",
		// 	't.fk_transporteur'=>"Numeric",
		// 	't.fk_zone'=>"Numeric",
		// 	'co.nom'=>"Text",
		// 	't.linear_min'=>"Numeric",
		// 	't.linear_max'=>"Numeric",
		// 	't.fk_unit_min'=>"Numeric",
		// 	't.fk_unit_max'=>"Numeric",
		// 	't.montant'=>"Numeric",
		// 	't.datec'=>"Date",
		// 	't.tms'=>"Date",
		// 	't.active'=>"Numeric",
		// 	't.entity'=>"Numeric"
		// );
		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'c_tranches_metre_departements as t';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_zones_departements as z ON t.fk_zone = z.rowid';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_departements as co ON co.rowid = z.fk_object';

		// $r++;
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='ExportterritoireZoneRegionMetreTarif';
		// $this->export_icon[$r]=$this->picto;
		// $this->export_permission[$r]=array(array("societe","export"));
		// $this->export_fields_array[$r]=array(
		// 	't.rowid'=>"Id",
		// 	't.fk_transporteur'=>"TransporterId",
		// 	't.fk_zone'=>"ZoneId",
		// 	'co.nom'=>"ZoneName",
		// 	't.linear_min'=>"MinLinear",
		// 	't.linear_max'=>"MaxLinear",
		// 	't.fk_unit_min'=>"MinUnitLinear",
		// 	't.fk_unit_max'=>"MaxUnitLinear",
		// 	't.montant'=>"Amount",
		// 	't.datec'=>"CreationDate",
		// 	't.tms'=>"Tms",
		// 	't.linear_reel'=>"Metrereel",
		// 	't.active'=>"Active",
		// 	't.entity'=>"Entity"
		// );
		// $this->export_TypeFields_array[$r]=array(
		// 	't.rowid'=>"Numeric",
		// 	't.fk_transporteur'=>"Numeric",
		// 	't.fk_zone'=>"Numeric",
		// 	'co.nom'=>"Text",
		// 	't.linear_min'=>"Numeric",
		// 	't.linear_max'=>"Numeric",
		// 	't.fk_unit_min'=>"Numeric",
		// 	't.fk_unit_max'=>"Numeric",
		// 	't.montant'=>"Numeric",
		// 	't.datec'=>"Date",
		// 	't.tms'=>"Date",
		// 	't.active'=>"Numeric",
		// 	't.entity'=>"Numeric"
		// );
		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'c_tranches_metre_regions as t';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_zones_regions as z ON t.fk_zone = z.rowid';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_regions as co ON co.rowid = z.fk_object';

		// $r++;
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='ExportterritoireZonePaysMetreTarif';
		// $this->export_icon[$r]=$this->picto;
		// $this->export_permission[$r]=array(array("societe","export"));
		
		// $this->export_fields_array[$r]=array(
		// 	't.rowid'=>"Id",
		// 	't.fk_transporteur'=>"TransporterId",
		// 	't.fk_zone'=>"ZoneId",
		// 	'co.label'=>"ZoneName",
		// 	't.linear_min'=>"MinLinear",
		// 	't.linear_max'=>"MaxLinear",
		// 	't.fk_unit_min'=>"MinUnitLinear",
		// 	't.fk_unit_max'=>"MaxUnitLinear",
		// 	't.montant'=>"Amount",
		// 	't.datec'=>"CreationDate",
		// 	't.tms'=>"Tms",'t.linear_reel'=>"Metresreel",
		// 	't.active'=>"Active",'t.entity'=>"Entity");

		// $this->export_TypeFields_array[$r]=array(
		// 	't.rowid'=>"Numeric",
		// 	't.fk_transporteur'=>"Numeric",
		// 	't.fk_zone'=>"Numeric",
		// 	'co.label'=>"Text",
		// 	't.linear_min'=>"Numeric",
		// 	't.linear_max'=>"Numeric",
		// 	't.fk_unit_min'=>"Numeric",
		// 	't.fk_unit_max'=>"Numeric",
		// 	't.montant'=>"Numeric",
		// 	't.datec'=>"Date",
		// 	't.tms'=>"Date",
		// 	't.active'=>"Numeric",
		// 	't.entity'=>"Numeric"
		// );
		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'c_tranches_metre_pays as t';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_zones_pays as z ON t.fk_zone = z.rowid';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'c_country as co ON co.rowid = z.fk_object';


		// // Imports
		// //--------
		// $r=0;

		// $r++;
		// $this->import_code[$r]=$this->rights_class.'_'.$r;
		// $this->import_label[$r]="ImportterritoireTranchesPoidsDepartement";	// Translation key
		// $this->import_icon[$r]=$this->picto;
		// $this->import_entities_array[$r]=array();		// We define here only fields that use another icon that the one defined into import_icon
		// $this->import_tables_array[$r]=array('ft'=>MAIN_DB_PREFIX.'c_tranches_poids_departements');
		
		// $this->import_fields_array[$r]=array(
		// 	'ft.rowid'=>"Id",
		// 	'ft.fk_transporteur'=>"TransporterId",
		// 	'ft.fk_zone'=>"ZoneId",
		// 	'ft.poids_min'=>"MinWeight",
		// 	'ft.poids_max'=>"MaxWeight",
		// 	'ft.fk_unit_min'=>"MinUnit",
		// 	'ft.fk_unit_max'=>"MaxUnit",
		// 	'ft.montant'=>"Amount",
		// 	'ft.datec'=>"CreationDate",
		// 	'ft.tms'=>"Tms",
		// 	'ft.poids_reel'=>"Poidsreel",
		// 	'ft.roundsup10'=>"AroundSup10",
		// 	'ft.div100'=>"DivBy100",
		// 	'ft.active'=>"Active"
		// );
		// $this->import_examplevalues_array[$r]=array(
		// 	'ft.rowid'=>"",
		// 	'ft.fk_transporteur'=>"",
		// 	'ft.fk_zone'=>"",
		// 	'ft.poids_min'=>"min. weight with point decimal separator",
		// 	'ft.poids_max'=>"max. weight with point decimal separator",
		// 	'ft.fk_unit_min'=>"'-6=mg, -3=g, 0=kg, 3=t, 98=onces, 99=livres",
		// 	'ft.fk_unit_max'=>"'-6=mg, -3=g, 0=kg, 3=t, 98=onces, 99=livres",
		// 	'ft.montant'=>"amount with point decimal separator",
		// 	'ft.datec'=>"yyyy-mm-dd",
		// 	'ft.tms'=>"yyyy-mm-dd",
		// 	'ft.poids_reel'=>"1=price_per_kg / 0=price_per_slice",
		// 	'ft.roundsup10'=>"1=active / 0=inactive",
		// 	'ft.div100'=>"1=active / 0=inactive",
		// 	'ft.active'=>"1=active / 0=inactive");
        // $this->import_updatekeys_array[$r]=array('ft.rowid'=>"Id");

		// $r++;
		// $this->import_code[$r]=$this->rights_class.'_'.$r;
		// $this->import_label[$r]="ImportterritoireTranchesPoidsRegion";	// Translation key
		// $this->import_icon[$r]=$this->picto;
		// $this->import_entities_array[$r]=array();		// We define here only fields that use another icon that the one defined into import_icon
		// $this->import_tables_array[$r]=array('ft'=>MAIN_DB_PREFIX.'c_tranches_poids_regions');
		
		// $this->import_fields_array[$r]=array(
		// 	'ft.rowid'=>"Id",
		// 	'ft.fk_transporteur'=>"TransporterId",
		// 	'ft.fk_zone'=>"ZoneId",
		// 	'ft.poids_min'=>"MinWeight",
		// 	'ft.poids_max'=>"MaxWeight",
		// 	'ft.fk_unit_min'=>"MinUnit",
		// 	'ft.fk_unit_max'=>"MaxUnit",
		// 	'ft.montant'=>"Amount",
		// 	'ft.datec'=>"CreationDate",
		// 	'ft.tms'=>"Tms",
		// 	'ft.poids_reel'=>"Poidsreel",
		// 	'ft.roundsup10'=>"AroundSup10",
		// 	'ft.div100'=>"DivBy100",
		// 	'ft.active'=>"Active"
		// );
		// $this->import_examplevalues_array[$r]=array(
		// 	'ft.rowid'=>"",
		// 	'ft.fk_transporteur'=>"",
		// 	'ft.fk_zone'=>"",
		// 	'ft.poids_min'=>"min. weight with point decimal separator",
		// 	'ft.poids_max'=>"max. weight with point decimal separator",
		// 	'ft.fk_unit_min'=>"'-6=mg, -3=g, 0=kg, 3=t, 98=onces, 99=livres",
		// 	'ft.fk_unit_max'=>"'-6=mg, -3=g, 0=kg, 3=t, 98=onces, 99=livres",
		// 	'ft.montant'=>"amount with point decimal separator",
		// 	'ft.datec'=>"yyyy-mm-dd",
		// 	'ft.tms'=>"yyyy-mm-dd",
		// 	'ft.poids_reel'=>"1=price_per_kg / 0=price_per_slice",
		// 	'ft.roundsup10'=>"1=active / 0=inactive",
		// 	'ft.div100'=>"1=active / 0=inactive",
		// 	'ft.active'=>"1=active / 0=inactive"
		// );
        // $this->import_updatekeys_array[$r]=array('ft.rowid'=>"Id");

		// $r++;
		// $this->import_code[$r]=$this->rights_class.'_'.$r;
		// $this->import_label[$r]="ImportterritoireTranchesPoidsPays";	// Translation key
		// $this->import_icon[$r]=$this->picto;
		// $this->import_entities_array[$r]=array();		// We define here only fields that use another icon that the one defined into import_icon
		// $this->import_tables_array[$r]=array('ft'=>MAIN_DB_PREFIX.'c_tranches_poids_pays');
		
		// $this->import_fields_array[$r]=array(
		// 	'ft.rowid'=>"Id",
		// 	'ft.fk_transporteur'=>"TransporterId",
		// 	'ft.fk_zone'=>"ZoneId",
		// 	'ft.poids_min'=>"MinWeight",
		// 	'ft.poids_max'=>"MaxWeight",
		// 	'ft.fk_unit_min'=>"MinUnit",
		// 	'ft.fk_unit_max'=>"MaxUnit",
		// 	'ft.montant'=>"Amount",
		// 	'ft.datec'=>"CreationDate",
		// 	'ft.tms'=>"Tms",
		// 	'ft.poids_reel'=>"Poidsreel",
		// 	'ft.roundsup10'=>"AroundSup10",
		// 	'ft.div100'=>"DivBy100",
		// 	'ft.active'=>"Active"
		// );

		// $this->import_examplevalues_array[$r]=array(
		// 	'ft.rowid'=>"",
		// 	'ft.fk_transporteur'=>"",
		// 	'ft.fk_zone'=>"",
		// 	'ft.poids_min'=>"min. weight with point decimal separator",
		// 	'ft.poids_max'=>"max. weight with point decimal separator",
		// 	'ft.fk_unit_min'=>"'-6=mg, -3=g, 0=kg, 3=t, 98=onces, 99=livres",
		// 	'ft.fk_unit_max'=>"'-6=mg, -3=g, 0=kg, 3=t, 98=onces, 99=livres",
		// 	'ft.montant'=>"amount with point decimal separator",
		// 	'ft.datec'=>"yyyy-mm-dd",
		// 	'ft.tms'=>"yyyy-mm-dd",
		// 	'ft.poids_reel'=>"1=price_per_kg / 0=price_per_slice",
		// 	'ft.roundsup10'=>"1=active / 0=inactive",
		// 	'ft.div100'=>"1=active / 0=inactive",
		// 	'ft.active'=>"1=active / 0=inactive"
		// );
        // $this->import_updatekeys_array[$r]=array('ft.rowid'=>"Id");

        // $r++;
		// $this->import_code[$r]=$this->rights_class.'_'.$r;
		// $this->import_label[$r]="ImportterritoireTranchesMetreDepartement";	// Translation key
		// $this->import_icon[$r]=$this->picto;
		// $this->import_entities_array[$r]=array();		// We define here only fields that use another icon that the one defined into import_icon
		// $this->import_tables_array[$r]=array('ft'=>MAIN_DB_PREFIX.'c_tranches_metre_departements');
		
		// $this->import_fields_array[$r]=array(
		// 	'ft.rowid'=>"Id",
		// 	'ft.fk_transporteur'=>"TransporterId",
		// 	'ft.fk_zone'=>"ZoneId",
		// 	'ft.linear_min'=>"MinLinear",
		// 	'ft.linear_max'=>"MaxLinear",
		// 	'ft.fk_unit_min'=>"MinUnitLinear",
		// 	'ft.fk_unit_max'=>"MaxUnitLinear",
		// 	'ft.montant'=>"Amount",'ft.datec'=>"CreationDate",'ft.tms'=>"Tms",'ft.linear_reel'=>"Metrereel", 'ft.active'=>"Active");
		
		// $this->import_examplevalues_array[$r]=array(
		// 	'ft.rowid'=>"",
		// 	'ft.fk_transporteur'=>"",
		// 	'ft.fk_zone'=>"",
		// 	'ft.linear_min'=>"min. linear with point decimal separator",
		// 	'ft.linear_max'=>"max. linear with point decimal separator",
		// 	'ft.fk_unit_min'=>"0=ml",
		// 	'ft.fk_unit_max'=>"0=ml",
		// 	'ft.montant'=>"amount with point decimal separator",
		// 	'ft.datec'=>"yyyy-mm-dd",
		// 	'ft.tms'=>"yyyy-mm-dd",
		// 	'ft.linear_reel'=>"1=per_meter / 0=per_slice",
		// 	'ft.active'=>"1=active / 0=inactive"
		// );
        // $this->import_updatekeys_array[$r]=array('ft.rowid'=>"Id");

		// $r++;
		// $this->import_code[$r]=$this->rights_class.'_'.$r;
		// $this->import_label[$r]="ImportterritoireTranchesMetreRegion";	// Translation key
		// $this->import_icon[$r]=$this->picto;
		// $this->import_entities_array[$r]=array();		// We define here only fields that use another icon that the one defined into import_icon
		// $this->import_tables_array[$r]=array('ft'=>MAIN_DB_PREFIX.'c_tranches_metre_regions');
		// $this->import_fields_array[$r]=array(
		// 	'ft.rowid'=>"Id",
		// 	'ft.fk_transporteur'=>"TransporterId",
		// 	'ft.fk_zone'=>"ZoneId",
		// 	'ft.linear_min'=>"MinLinear",
		// 	'ft.linear_max'=>"MaxLinear",
		// 	'ft.fk_unit_min'=>"MinUnitLinear",
		// 	'ft.fk_unit_max'=>"MaxUnitLinear",
		// 	'ft.montant'=>"Amount",
		// 	'ft.datec'=>"CreationDate",
		// 	'ft.tms'=>"Tms",
		// 	'ft.linear_reel'=>"Metrereel",
		// 	'ft.active'=>"Active"
		// );
		// $this->import_examplevalues_array[$r]=array(
		// 	'ft.rowid'=>"",
		// 	'ft.fk_transporteur'=>"",
		// 	'ft.fk_zone'=>"",
		// 	'ft.linear_min'=>"min. linear with point decimal separator",
		// 	'ft.linear_max'=>"max. linear with point decimal separator",
		// 	'ft.fk_unit_min'=>"0=ml",
		// 	'ft.fk_unit_max'=>"0=ml",
		// 	'ft.montant'=>"amount with point decimal separator",
		// 	'ft.datec'=>"yyyy-mm-dd",
		// 	'ft.tms'=>"yyyy-mm-dd",
		// 	'ft.linear_reel'=>"1=per_meter / 0=per_slice",
		// 	'ft.active'=>"1=active / 0=inactive"
		// );
        // $this->import_updatekeys_array[$r]=array('ft.rowid'=>"Id");

		// $r++;
		// $this->import_code[$r]=$this->rights_class.'_'.$r;
		// $this->import_label[$r]="ImportterritoireTranchesMetrePays";	// Translation key
		// $this->import_icon[$r]=$this->picto;
		// $this->import_entities_array[$r]=array();		// We define here only fields that use another icon that the one defined into import_icon
		// $this->import_tables_array[$r]=array('ft'=>MAIN_DB_PREFIX.'c_tranches_metre_pays');
		// $this->import_fields_array[$r]=array(
		// 	'ft.rowid'=>"Id",
		// 	'ft.fk_transporteur'=>"TransporterId",
		// 	'ft.fk_zone'=>"ZoneId",
		// 	'ft.linear_min'=>"MinLinear",
		// 	'ft.linear_max'=>"MaxLinear",
		// 	'ft.fk_unit_min'=>"MinUnitLinear",
		// 	'ft.fk_unit_max'=>"MaxUnitLinear",
		// 	'ft.montant'=>"Amount",
		// 	'ft.datec'=>"CreationDate",
		// 	'ft.tms'=>"Tms",
		// 	'ft.linear_reel'=>"Metrereel",
		// 	'ft.active'=>"Active"
		// );
		// $this->import_examplevalues_array[$r]=array(
		// 	'ft.rowid'=>"",
		// 	'ft.fk_transporteur'=>"",
		// 	'ft.fk_zone'=>"",
		// 	'ft.linear_min'=>"min. linear with point decimal separator",
		// 	'ft.linear_max'=>"max. linear with point decimal separator",
		// 	'ft.fk_unit_min'=>"0=ml",
		// 	'ft.fk_unit_max'=>"0=ml",
		// 	'ft.montant'=>"amount with point decimal separator",
		// 	'ft.datec'=>"yyyy-mm-dd",
		// 	'ft.tms'=>"yyyy-mm-dd",
		// 	'ft.linear_reel'=>"1=per_meter / 0=per_slice",
		// 	'ft.active'=>"1=active / 0=inactive"
		// );
        // $this->import_updatekeys_array[$r]=array('ft.rowid'=>"Id");

	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	public function init($options='')
	{
		global $db,$conf;
		dolibarr_set_const($db, "CHECKLASTVERSION_EXTERNALMODULE", '1', 'int', 0, '', $conf->entity);
		
		$sql = array();

		$this->_load_tables('/territoire/sql/');

		// Create extrafields
		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		
		$result = $extrafields->addExtraField('fk_zone', "Zone de livraison préférée", 'int', 1, 10, 'thirdparty', 0, 0, 0, '', 0, '', 0);

		return $this->_init($sql, $options);
	}

	/**
	 * Function called when module is disabled.
	 * Remove from database constants, boxes and permissions from Dolibarr database.
	 * Data directories are not deleted
	 *
	 * @param      string	$options    Options when enabling module ('', 'noboxes')
	 * @return     int             	1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}

}
