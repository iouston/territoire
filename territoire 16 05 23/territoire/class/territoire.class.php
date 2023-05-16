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
 *  \file       htdocs/fraistransport/class/fraistransport.class.php
 *  \ingroup    fraistransport
 *  \brief      File of class to manage predefined products sets
 */
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';


/**
 * Class to manage products or services
 */
class Territoire extends CommonObject
{
	public $element='territoire';
	public $table_element='c_territoire';
	public $fk_element='fk_transporteur';
	public $picto = 'generic';
	public $ismultientitymanaged = 1;	// 0=No test on entity, 1=Test with field entity, 2=Test with link by societe

	/**
	 * {@inheritdoc}
	 */
	protected $table_ref_field = 'rowid';

	/**
	 * territoire id
	 * @var int
	 */
	public $rowid;

	/**
	 * territoire geographic code
	 * @var int
	 */
	public $codecollectivite;
	
	/**
	 * territoire name
	 * @var string
	 */
	public $nomcollectivite;


	/**
	* Dolibarr entity
	* @var int
	*/
	public $entity;

	

	/**
	 * Do not retain this transporter if outside range
	 */
	const DO_NOT_TAKE = 0;

	/**
	 * Take min for this transporter if outside range
	 */
	const TAKE_MIN = 1;

	/**
	 * Take max for this transporter if outside range
	 */
	const TAKE_MAX = 2;

	/**
	 * Take max for this transporter if outside range
	 */
	const FORCE_TO_ZERO = 3;

	/**
	 * Subject_to_fuel_tax possible value
	 */
	 const TYPE_FUEL_TAX = array(
	 	'TransporterSubjectToFuelTaxNo',
	 	'SubjectToFuelTax',
	 	'SubjectToFuelTaxMonthly',
	);


	/**
	 *  Constructor
	 *
	 *  @param      DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		global $langs;

		$this->db = $db;
	}

	/**
	 *    Check that ref and label are ok
	 *
	 *    @return     int         >1 if OK, <=0 if KO
	 */
	function check()
	{

		$this->ref = dol_sanitizeFileName(stripslashes($this->ref));

		$err = 0;
		if (dol_strlen(trim($this->ref)) == 0)
		$err++;

		if (dol_strlen(trim($this->label)) == 0)
		$err++;

		if ($err > 0)
		{
			return 0;
		}
		else
		{
			return 1;
		}
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

		$this->label_holiday = trim($this->label_holiday);

		dol_syslog(get_class($this)."::create", LOG_DEBUG);

		$this->db->begin();


				$sql = "INSERT INTO ".MAIN_DB_PREFIX."territoire (";
				//$sql.= " rowid";
				$sql.= "label_holiday";
				$sql.= ", start_holiday";
				$sql.= ", end_holiday";
				$sql.= ", yearscol";
                $sql.= ", entity";
                $sql.= ", fk_zone";
				//$sql.= ", tms";
				$sql.= ") VALUES ('" .$this->label_holiday."'";
				$sql.= ", '".$this->start_holiday."'";
				$sql.= ", '".$this->end_holiday."'";
				$sql.= ", '".$this->yearscol."'";
				$sql.= ", '". $conf->entity . "'";
				$sql.= ", ".$this->fk_zone;
				$sql.= ")";

				// echo $sql;
				// exit();


				$result = $this->db->query($sql);
				if ( $result )
				{
					$id = $this->db->last_insert_id(MAIN_DB_PREFIX."territoire");

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


	/**
	 *	Update a record into database.
	 *
	 *	@param	int		$id         Id of product set
	 *	@param  User	$user       Object user making update
	 *	@param	int		$notrigger	Disable triggers
	 *	@param	string	$action		Current action for hookmanager ('add' or 'update')
	 *	@return int         		1 if OK, -1 if ref already exists, -2 if other error
	 */
	function update($id, $user, $action='update')
	{
		global $langs, $conf, $hookmanager, $mysoc;

		//$error=0;

		// Check parameters
		if (! $this->label_holiday) $this->label_holiday = 'MISSING LABEL';

		// Clean parameters
		//$this->ref = dol_string_nospecial(trim($this->ref));
		$this->label_holiday = trim($this->label_holiday);
		

				$sql = "UPDATE ".MAIN_DB_PREFIX."territoire";
				$sql.= " SET label_holiday = '" . $this->label_holiday ."'";
				$sql.= ", start_holiday = '". $this->start_holiday. "'";
				$sql.= ", end_holiday = '" . $this->end_holiday. "'";
				$sql.= ", yearscol = '" . $this->yearscol. "'";
				$sql.= ", entity = '" . $conf->entity. "'";
                $sql.= ", fk_zone = '" . $this->fk_zone. "'";
				$sql.= " WHERE rowid = '" . $id . "'";

				
				dol_syslog(get_class($this)."::update", LOG_DEBUG);


				// echo $sql;
				// exit();

				$this->db->begin();

				$result = $this->db->query($sql);

				
				if ($result)
				{

						$this->rowid = $id;

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
			
	}



	/**
	 *  Delete a product set from database (if not used)
	 *
	 *	@param      User	$user       Product id (usage of this is deprecated, delete should be called without parameters on a fetched object)
	 *  @param      int     $notrigger  Do not execute trigger
	 * 	@return		int					< 0 if KO, 0 = Not possible, > 0 if OK
	 */
	// function delete(User $user, $notrigger=0)
	function delete(User $user)
	{
		global $conf, $langs;

		$error=0;

		// Check parameters
		if (empty($this->rowId))
		{
			$this->error = "Object must be fetched before calling delete";
			return -1;
		}
		
		$this->db->begin();

		
			$sqlz = "DELETE FROM ".MAIN_DB_PREFIX."territoire";
			$sqlz.= " WHERE rowid = ".$this->rowId;
			

			dol_syslog(get_class($this).'::delete', LOG_DEBUG);
			

			$resultz = $this->db->query($sqlz);
			

			

			if ( ! $resultz )
			{
				$error++;
				$this->errors[] = $this->db->lasterror();
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
	function fetch($id='')
	{
		global $langs, $conf;

		dol_syslog(get_class($this)."::fetch id=".$id);

		// Check parameters
		// if (! $id )
		// {
		// 	$this->error='ErrorWrongParameters';
		// 	dol_print_error(get_class($this)."::fetch ".$this->error);
		// 	return -1;
		// }

		 if ($id) 
        {

			$sql = "SELECT rowid, label_holiday, start_holiday, end_holiday, yearscol, fk_zone";
			$sql.= " FROM ".MAIN_DB_PREFIX."territoire";
       

        	$sql.= " WHERE rowid = ".$this->db->escape($id);
			

			$resql = $this->db->query($sql);
			if ( $resql )
			{
				if ($this->db->num_rows($resql) > 0)
				{
					$obj = $this->db->fetch_object($resql);

					$this->rowid						= $obj->rowid;
					//$this->ref		       			= $obj->ref;
	                $this->label_holiday                = $obj->label_holiday;
	                $this->start_holiday                = $obj->start_holiday;
					$this->end_holiday					= $obj->end_holiday;
					$this->yearscol						= $obj->yearscol;
					$this->fk_zone           			= $obj->fk_zone;
					$this->entity						= $obj->entity;

					$this->db->free($resql);

					return 1;
				}
				else
				{
					return 0;
				}
			}
		}
		else
		{
			if(! $id)
			{
				global $langs, $conf;

				$territoireListe=array();

				$sql = "SELECT rowid, label_holiday, start_holiday, end_holiday, yearscol, fk_zone FROM ".MAIN_DB_PREFIX."territoire";

				
				$resql = $this->db->query($sql);
				if ( $resql )
				{

					for($i = 0 ; $i < $this->db->num_rows($resql) ; ++$i)
					{
						 
						$obj = $this->db->fetch_object($resql);
						


								$this->rowid						= $obj->rowid;
								$this->label_holiday                = $obj->label_holiday;
				                $this->start_holiday                = $obj->start_holiday;
								$this->end_holiday					= $obj->end_holiday;
								$this->yearscol						= $obj->yearscol;
								$this->fk_zone           			= $obj->fk_zone;
								
								$this->entity						= $obj->entity;


						$territoireListe[$i] = $obj;

					}
				}

				
				$this->db->free($resql);

				return $territoireListe;

			}	
			

		}


	}	


	function fetchAll()
	{
		global $langs, $conf;

		$territoireListe=array();

		$sql = "SELECT rowid, label_holiday, start_holiday, end_holiday, yearscol, fk_zone FROM ".MAIN_DB_PREFIX."territoire";

		
		$resql = $this->db->query($sql);
		if ( $resql )
		{

			for($i = 0 ; $i < $this->db->num_rows($resql) ; ++$i)
			{
				 
				$obj = $this->db->fetch_object($resql);
				


						$this->rowid						= $obj->rowid;
						$this->label_holiday                = $obj->label_holiday;
		                $this->start_holiday                = $obj->start_holiday;
						$this->end_holiday					= $obj->end_holiday;
						$this->yearscol						= $obj->yearscol;
						$this->fk_zone           			= $obj->fk_zone;
						
						$this->entity						= $obj->entity;


				$territoireListe[$i] = $obj;

			}
		}

		
		$this->db->free($resql);

		return $territoireListe;
	}



/**
	 *  Load a product set in memory from database
	 *
	 *  @param	int		$id      			Id of product/service to load
	 *  @param  string	$ref     			Ref of product/service to load
	 *  @return int     					<0 if KO, 0 if not found, >0 if OK
	 */
	function fetch_from_city_name($cityName='')
	{
		global $langs, $conf;

		dol_syslog(get_class($this)."::fetch id=".$id);

		 if ($cityName) 
        {

			$sql = 'SELECT z.code, z.zip, z.town, cpv.fk_code, cpv.fk_localauthoritycode,	c.localauthoritycode, c.localauthorityname';
			$sql.= ' FROM '.MAIN_DB_PREFIX.'c_ziptown as z';
       
       		$sql.= ' INNER JOIN '.MAIN_DB_PREFIX.'c_territoire_collecparville as cpv';

       		$sql.= ' ON cpv.fk_code = z.code';

       		$sql.= ' INNER JOIN '.MAIN_DB_PREFIX.'c_territoire_collectivites as c';

       		$sql.= ' ON c.localauthoritycode = cpv.fk_localauthoritycode';

        	$sql.= ' WHERE z.town = "'.$this->db->escape($cityName).'"';
			

			// echo $sql;
			// exit();


			$resql = $this->db->query($sql);
			if ( $resql )
			{
				if ($this->db->num_rows($resql) > 0)
				{
					$obj = $this->db->fetch_object($resql);

					$this->rowid						= $obj->rowid;
					$this->localauthoritycode             = $obj->localauthoritycode;
	                $this->localauthorityname              = $obj->localauthorityname ;
	                $this->town               			= $obj->town;
					$this->entity						= $obj->entity;

					$this->db->free($resql);

					return 1;
				}
				else
				{
					return 0;
				}
			}
		}
		


	}	




}
