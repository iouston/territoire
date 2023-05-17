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
 *  \file       htdocs/fraistransport/class/fraistransport.class.php
 *  \ingroup    fraistransport
 *  \brief      File of class to manage predefined products sets
 */
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';


/**
 * Class to manage products or services
 */
class City extends CommonObject
{
	public $element='zones';
	public $table_element='';
	public $fk_element='fk_zone';
	public $picto = 'generic';
	public $ismultientitymanaged = 1;	// 0=No test on entity, 1=Test with field entity, 2=Test with link by societe

	/**
	 * {@inheritdoc}
	 */
	protected $table_ref_field = 'rowid';



	/**
     * Product id link
     * @var string
     */
	public $fk_object;


	/**
     * Transporter id
     * @var string
     */
	public $id = 0;

	/**
     * Active
     * @var string
     */
	public $active = 1;

	/**
     * Usesaison
     * @var tinyint
     */
	public $usesaison = 0;

	/**
	 *  Constructor
	 *
	 *  @param      DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		global $langs, $conf;

		if ($conf->global->TRANSPORTER_ZONES_MODE == 'departements') {
			$this->table_element = 'c_zones_departements';
			$this->table_element2 = 'c_departements';
			$this->field_name ='_departement';
		} else if ($conf->global->TRANSPORTER_ZONES_MODE == 'regions') {
			$this->table_element = 'c_zones_regions';
			$this->table_element2 = 'c_regions';
			$this->field_name ='_region';
		} else {
			$this->table_element = 'c_zones_pays';
			$this->table_element2 = 'c_country';
			$this->field_name ='';
		}

		$this->db = $db;
	}

    /**
     *    Check properties of product are ok (like name, barcode, ...).
     *    All properties must be already loaded on object (this->barcode, this->barcode_type_code, ...).
     *
     *    @return     int		0 if OK, <0 if KO
     */
    function verify()
    {
        $this->errors=array();

        $result = 0;

        return $result;
    }

	/**
	 *	Insert product into database
	 *
	 *	@param	User	$user     		User making insert
	 *  @param	int		$notrigger		Disable triggers
	 *	@return int			     		Id of product/service if OK, < 0 if KO
	 */
	function create($user,$notrigger=0)
	{
		global $conf, $langs, $mysoc;

        $error=0;

		
		dol_syslog(get_class($this)."::create", LOG_DEBUG);

		$now=dol_now();

		$this->db->begin();

		// Check more parameters
		// If error, this->errors[] is filled
		$result = $this->verify();

		if ($result >= 0)
		{

			$sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_element." (";
			$sql.= " datec";
            $sql.= ", tms";
			$sql.= ", entity";
			$sql.= ", fk_object";
			$sql.= ", usesaison";
			$sql.= ", ismountain";
			$sql.= ", ishighmountain";
			$sql.= ", isonisland";
			$sql.= ") VALUES (";
			$sql.= "'".$this->db->idate($now)."'";
            $sql.= ", '".$this->db->idate($now)."'";
			$sql.= ", ".$conf->entity;
			$sql.= ", ".$this->fk_object;
			$sql.= ", ".$this->usesaison;
			$sql.= ", ".$this->ismountain;
			$sql.= ", ".$this->ishighmountain;
			$sql.= ", ".$this->isonisland;
			$sql.= ")";	

			dol_syslog(get_class($this)."::Create", LOG_DEBUG);
			$result = $this->db->query($sql);
			if ( $result )
			{
				$id = $this->db->last_insert_id(MAIN_DB_PREFIX.$this->table_element);

				if ($id > 0)
				{
					$this->id = $id;

				}
				else
				{
					$error++;
					$this->error='ErrorFailedToGetInsertedId';
				}
			}
			else
			{
				$error++;
				$this->error=$this->db->lasterror();
			}
				

			if (! $error)
			{
				$this->db->commit();
				return $this->id;
			}
			else
			{
				$this->db->rollback();
				return -$error;
			}
        }
        else
       {
            $this->db->rollback();
            dol_syslog(get_class($this)."::Create fails verify ".join(',',$this->errors), LOG_WARNING);
            return -3;
        }

	}

	/**
	 *	Update a record into database.
	 *
	 *	@param	int		$id         Id of product set
	 *	@param  User	$user       Object user making update
	 *	@param	int		$notrigger	Disable triggers
	 *	@param	string	$action		Current action for hookmanager ('add' or 'update')
	 *	@return int         		1 if OK, -1 if ref already exists, -2 if other error
	 */
	function update($id, $user, $notrigger=false, $action='update')
	{
		global $langs, $conf, $hookmanager;

		$error=0;
		


        $this->db->begin();

        // Check name is required and codes are ok or unique.
        // If error, this->errors[] is filled
        $result = 0;
        if ($action != 'add')
        {
        	$result = $this->verify();	// We don't check when update called during a create because verify was already done
        }

        if ($result >= 0)
        {

			$sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
			$sql.= " SET fk_object = " . $this->fk_object;
			$sql.= ", seasondaynumberstart = " .$this->seasondaynumberstart;
			$sql.= ", seasonmonthnumberstart = " .$this->seasonmonthnumberstart;
			$sql.= ", seasondaynumberend = " .$this->seasondaynumberend;
			$sql.= ", seasonmonthnumberend = " .$this->seasonmonthnumberend;
			$sql.= ", seasonrate = " .$this->seasonrate;
			$sql.= " WHERE rowid = " . $id;

			dol_syslog(get_class($this)."::update", LOG_DEBUG);

			$resql=$this->db->query($sql);
			if ($resql)
			{
				$this->id = $id;

				$this->db->commit();
				return 1;
			}
			else
			{
				$this->error=$langs->trans("Error")." : ".$this->db->error()." - ".$sql;
				$this->errors[]=$this->error;
				$this->db->rollback();

				return -2;				
			}           			


			if (! $error)
			{
				$this->db->commit();
				return $this->id;
			}
			else
			{
				$this->db->rollback();
				return -$error;
			}

        }
        else
        {
            $this->db->rollback();
            dol_syslog(get_class($this)."::Update fails verify ".join(',',$this->errors), LOG_WARNING);
            return -3;
        }
	}

	/**
	 *  Delete a product set from database (if not used)
	 *
	 *	@param      User	$user       Product id (usage of this is deprecated, delete should be called without parameters on a fetched object)
	 *  @param      int     $notrigger  Do not execute trigger
	 * 	@return		int					< 0 if KO, 0 = Not possible, > 0 if OK
	 */
	function delete(User $user, $notrigger=0)
	{
		global $conf, $langs;

		$error=0;

		// Check parameters
		if (empty($this->id))
		{
			$this->error = "Object must be fetched before calling delete";
			return -1;
		}
		
		$this->db->begin();

		// Delete product
		if (! $error)
		{
			$sqlz = "DELETE FROM ".MAIN_DB_PREFIX.$this->table_element;
			$sqlz.= " WHERE rowid = ".$this->id;
			dol_syslog(get_class($this).'::delete', LOG_DEBUG);
			$resultz = $this->db->query($sqlz);

			if ( ! $resultz )
			{
				$error++;
				$this->errors[] = $this->db->lasterror();
			}
		}

		

		if (! $error)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -$error;
		}

	}

	/**
	 *  Load a product set in memory from database
	 *
	 *  @param	int		$id      			Id of product/service to load
	 *  @param  string	$ref     			Ref of product/service to load
	 *  @return int     					<0 if KO, 0 if not found, >0 if OK
	 */
	function fetch($id='', $fk_object = '', $ismountain=0, $ishighmountain=0,$isonisland=0)
	{
		global $langs, $conf;

		dol_syslog(get_class($this)."::fetch id=".$id);

		// Check parameters
		if (! $id && !$fk_object)
		{
			$this->error='ErrorWrongParameters';
			dol_print_error(get_class($this)."::fetch ".$this->error);
			return -1;
		}		

		$sql = "SELECT rowid, entity, fk_object, active, datec, tms, usesaison, ismountain, ishighmountain, isonisland, seasondaynumberstart, seasonmonthnumberstart,seasondaynumberend,seasonmonthnumberend, seasonrate";
		$sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element;
		$sql.= " WHERE entity IN (".getEntity($this->element).")";
		if($ismountain==1){
		$sql.= " AND ismountain = 1";
		}
		if($ishighmountain==1){
		$sql.= " AND ishighmountain = 1";
		}
		if($isonisland==1){
		$sql.= " AND isonisland = 1";
		}

		$sql.= !empty($id) ? " AND rowid = ".(int)$id : "";
		$sql.= !empty($fk_object) ? " AND fk_object = ".(int)$fk_object : "";

		$resql = $this->db->query($sql);
		if ( $resql )
		{
			if ($this->db->num_rows($resql) > 0)
			{
				$obj = $this->db->fetch_object($resql);

				$this->id							= $obj->rowid;
				
				$this->fk_object					= $obj->fk_object;
				$this->usesaison					= $obj->usesaison;
				$this->seasondaynumberstart			= $obj->seasondaynumberstart;
				$this->seasonmonthnumberstart		= $obj->seasonmonthnumberstart;
				$this->seasondaynumberend			= $obj->seasondaynumberend;
				$this->seasonmonthnumberend			= $obj->seasonmonthnumberend;
				$this->seasonrate					= $obj->seasonrate;
				$this->ismountain					= $obj->ismountain;
				$this->ishighmountain				= $obj->ishighmountain;
				$this->isonisland					= $obj->isonisland;
				$this->active						= $obj->active;

				$this->date_creation				= $obj->datec;
				$this->date_modification			= $obj->tms;
				
				$this->entity						= $obj->entity;

				$this->db->free($resql);

				return 1;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

/**
	 *  Load transporters
	 *
	 *  @return int     					<0 if KO, 0 if not found, >0 if OK
	 */
	function getZones($active = 0)
	{
		global $langs, $conf;

		dol_syslog(get_class($this)."::getZones");

		$zones = array();


		$sql = "SELECT c.rowid, c.fk_object, c.ismountain, c.ishighmountain, c.isonisland, c.usesaison";
		if ($conf->global->TRANSPORTER_ZONES_MODE == 'departements') {
			$sql.= ", co.nom as label";
		} else if ($conf->global->TRANSPORTER_ZONES_MODE == 'regions') {
			$sql.= ", co.nom as label";
		} else {
			$sql.= ", co.label as label";
		}

		$sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element." c";

		if ($conf->global->TRANSPORTER_ZONES_MODE == 'departements') {
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements co ON co.rowid = c.fk_object";
		} else if ($conf->global->TRANSPORTER_ZONES_MODE == 'regions') {
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_regions co ON co.rowid = c.fk_object";
		} else {
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_country co ON co.rowid = c.fk_object";
		}

		$sql.= " WHERE c.entity IN (".getEntity($this->element).")";
		$sql.= $active ? " AND active = ".$active : "";

		if ($conf->global->TRANSPORTER_ZONES_MODE == 'departements') {
			$sql.= " ORDER BY co.nom";
		} else if ($conf->global->TRANSPORTER_ZONES_MODE == 'regions') {
			$sql.= " ORDER BY co.nom";
		} else {
			$sql.= " ORDER BY co.label";
		}
		
		$resql = $this->db->query($sql);
		if ( $resql )
		{
			if ($this->db->num_rows($resql) > 0)
			{
				while ($obj = $this->db->fetch_object($resql))
				{
					$obj->label = $this->addLabelMountainHighMountainIsland($obj->label,$obj->ismountain,$obj->ishighmountain, $obj->isonisland);

					$zones[$obj->rowid]			= $obj;				
				}

				$this->db->free($resql);
				
				
				return $zones;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}


/**
	 *  Load transporters
	 *
	 *  @return int     					<0 if KO, 0 if not found, >0 if OK
	 */
	function getRegionId($state_id = 0)
	{
		global $langs, $conf;

		dol_syslog(get_class($this)."::getRegionId");

		$id = 0;


		$sql = "SELECT r.rowid";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_departements d";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_regions r ON d.fk_region = r.code_region";
		$sql.= " WHERE d.rowid = ".$state_id;

		$resql = $this->db->query($sql);
		if ( $resql )
		{
			if ($this->db->num_rows($resql) > 0)
			{
				$obj = $this->db->fetch_object($resql);
				$id = $obj->rowid;

				$this->db->free($resql);
			}
			
			return $id;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	function getZone($zone)
	{
		global $langs, $conf;

		$zonedetail = '';

		$sql = "SELECT c.rowid, c.fk_object, c.ismountain, c.ishighmountain, c.isonisland, c.usesaison, co.nom as label, co.code_departement as codedpt";
		$sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element." c";

		if ($conf->global->TRANSPORTER_ZONES_MODE)
		{
		    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements co ON co.rowid = c.fk_object";
		}
		else
		{
		    $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_regions co ON co.rowid = c.fk_object";
		}

		$sql.= " WHERE c.entity IN (".getEntity($this->element).")";
		$sql.= " AND c.rowid=".$zone;
		$sql.= $active ? " AND active = ".$active : "";
				
		$resql = $this->db->query($sql);
		if ( $resql )
		{
			if ($this->db->num_rows($resql) > 0)
			{
				if ($obj = $this->db->fetch_object($resql))
				{
					
					$obj->label = $this->addLabelMountainHighMountainIsland($obj->label,$obj->ismountain,$obj->ishighmountain, $obj->isonisland);

					$zonedetail = $obj->label.' ('.$obj->codedpt.')';			
				}

				$this->db->free($resql);
			}
		}

		return $zonedetail ? $langs->trans('ZoneDestination', $zonedetail) : '';

	}

	function addLabelMountainHighMountainIsland($label,$ismountain,$ishighmountain,$isisland){
		global $langs;

	$sep =' ';
	if($ismountain){		$label = $label.$sep.$langs->trans('Mountain');}
	if($ishighmountain){	$label = $label.$sep.$langs->trans('HighMountain');}
	if($isisland){			$label = $label.$sep.$langs->trans('Island');}
	return $label;
	}

	/**
	 * @param  $month int month date
	 * @param  $day int day date
	 * @return the date with current year-month-day
	 */
	function get_season_date($month,$day){
		$nowy = date('Y');
	    $date = new DateTime($nowy.'-'.$month.'-'.$day);
	    return $date->format('Y-n-j');

	}
}