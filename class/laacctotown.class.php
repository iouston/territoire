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
 *  \file       htdocs/territoire/class/laactotown.class.php
 *  \ingroup    territoire
 *  \brief      File of class to manage predefined towns according to local 
 *  authorities sets
 */
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';


/**
 * Class to manage towns according to the local authority linked to it
 */
class LAAccToTown extends CommonObject
{
	public $element='laacctotown';
	public $table_element='laacctotown';
	//public $fk_element='fk_zone';
	public $picto = 'generic';
	public $ismultientitymanaged = 1;	// 0=No test on entity, 1=Test with field entity, 2=Test with link by societe

	
	/**
	 * Collectivite id
	 * @var int
	 */
	public $rowid;

	/**
	 * Collectivite code
	 * @var string
	 */
	public $fk_code;
	
	/**
	 * Collectivite name
	 * @var string
	 */
	public $fk_localauthoritycode;

	/**
	*Dolibarr entity
	* @var int
	*/
	public $entity;

   	
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
	 *	Insert town according to local authority into database
	 *
	 *	@param	User	$user     		User making insert
	 *  @return int			     		Id of product/service if OK, < 0 if KO
	 */
	function create($user)
	{
		global $conf, $langs, $mysoc;

        	$error=0;

		//$this->label_holiday = trim($this->label_holiday);

		dol_syslog(get_class($this)."::create", LOG_DEBUG);

		$this->db->begin();


				$sql = "INSERT INTO ".MAIN_DB_PREFIX."c_territoire_collecparville (";
				$sql.= "fk_code";
				$sql.= ", fk_localauthoritycode";
				$sql.= ", entity";
				$sql.= ") VALUES ('" .$this->fk_code."'";
				$sql.= ", '".$this->fk_localauthoritycode."'";
				$sql.= ", '". $conf->entity . "'";
				$sql.= ")";

				$result = $this->db->query($sql);
				if ( $result )
				{
					$id = $this->db->last_insert_id(MAIN_DB_PREFIX."c_territoire_collecparville");

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
		if (! $this->fk_localauthoritycode) $this->fk_localauthoritycode = 'MISSING LABEL';

		// Clean parameters
		//$this->label_holiday = trim($this->label_holiday);
		

				$sql = "UPDATE ".MAIN_DB_PREFIX."c_territoire_collecparville";
				$sql.= " SET fk_code = '" . $this->fk_code ."'";
				$sql.= ", fk_localauthoritycode = '". $this->fk_localauthoritycode. "'";
				$sql.= ", entity = '" . $conf->entity. "'";
				$sql.= " WHERE rowid = '" . $id . "'";

				
				dol_syslog(get_class($this)."::update", LOG_DEBUG);


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
	 *	Update a record into database from the geographical code.
	 *
	 *	@param	int		$code       Id of product set
	 *	@param  User	$user       Object user making update
	 *	@param	int		$notrigger	Disable triggers
	 *	@param	string	$action		Current action for hookmanager ('add' or 'update')
	 *	@return int         		1 if OK, -1 if ref already exists, -2 if other error
	 */
	function updateFromCode($code, $user, $action='update')
	{
		global $langs, $conf, $hookmanager, $mysoc;

		//$error=0;

		// Check parameters
		if (! $this->fk_localauthoritycode) $this->fk_localauthoritycode = 'MISSING LABEL';

		// Clean parameters
		//$this->label_holiday = trim($this->label_holiday);
		

				$sql = "UPDATE ".MAIN_DB_PREFIX."c_territoire_collecparville";
				$sql.= " SET fk_code = '" . $this->fk_code ."'";
				$sql.= ", fk_localauthoritycode = '". $this->fk_localauthoritycode. "'";
				$sql.= ", entity = '" . $conf->entity. "'";
				$sql.= " WHERE fk_code = '" . $code . "'";

				
				dol_syslog(get_class($this)."::update", LOG_DEBUG);


				// echo $sql;
				// exit();

				$this->db->begin();

				$result = $this->db->query($sql);

				
				if ($result)
				{

						//$this->fk_code = $code;

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
		if (empty($this->rowid))
		{
			$this->error = "Object must be fetched before calling delete";
			return -1;
		}
		
		$this->db->begin();

		
			$sqlz = "DELETE FROM ".MAIN_DB_PREFIX."c_territoire_collecparville";
			$sqlz.= " WHERE rowid = ".$this->rowid;
			

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

		
		 if ($id) 
        {

			$sql = "SELECT rowid, fk_code, fk_localauthoritycode, entity";
			$sql.= " FROM ".MAIN_DB_PREFIX."c_territoire_collecparville";
       

        	$sql.= " WHERE rowid = ".$this->db->escape($id);
			

			$resql = $this->db->query($sql);
			if ( $resql )
			{
				if ($this->db->num_rows($resql) > 0)
				{
					$obj = $this->db->fetch_object($resql);

					$this->rowid					= $obj->rowid;
					$this->fk_code       		    = $obj->fk_code;
	                $this->fk_localauthoritycode    = $obj->fk_localauthoritycode;
					$this->entity					= $obj->entity;

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

				$collectiviteListe=array();

				$sql = "SELECT rowid, fk_code, fk_localauthoritycode, entity";
				$sql.= " FROM ".MAIN_DB_PREFIX."c_territoire_collecparville";

				$resql = $this->db->query($sql);

				// echo $sql;
				// exit();


				if ( $resql )
				{

					for($i = 0 ; $i < $this->db->num_rows($resql) ; ++$i)
					{ 
						$obj = $this->db->fetch_object($resql);
				
								$this->rowid					= $obj->rowid;
								$this->fk_code       		    = $obj->fk_code;
	                			$this->fk_localauthoritycode    = $obj->fk_localauthoritycode;
								$this->entity					= $obj->entity;

						$collectiviteListe[$i] = $obj;

					}
				}
	
				$this->db->free($resql2);

				return $collectiviteListe;
			}	
	
		}

	}	


	/**
	 *  Load a town linked to a local authority set in memory from database
	 *  from geographical code
	 *
	 *  @param	int		$id      			Id of product/service to load
	 *  @param  string	$ref     			Ref of product/service to load
	 *  @return int     					<0 if KO, 0 if not found, >0 if OK
	 */
	function fetchFromCode($code='')
	{
		global $langs, $conf;

		dol_syslog(get_class($this)."::fetch id=".$id);

		$sql = "SELECT rowid, fk_code, fk_localauthoritycode, entity";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_territoire_collecparville";
       

        $sql.= " WHERE fk_code = ".$this->db->escape($code);
			

		$resql = $this->db->query($sql);
		if ( $resql )
		{
				if ($this->db->num_rows($resql) > 0)
				{
					$obj = $this->db->fetch_object($resql);

					$this->rowid					= $obj->rowid;
					$this->fk_code       		    = $obj->fk_code;
	                $this->fk_localauthoritycode    = $obj->fk_localauthoritycode;
					$this->entity					= $obj->entity;

					$this->db->free($resql);

					return 1;
				}
				else
				{
					return 0;
				}
		}
	}	


	function fetchAll()
	{
		global $langs, $conf;

		$collectiviteListe=array();

		$sql = "SELECT rowid, codecollectivite, nomcollectivite, active, entity FROM ".MAIN_DB_PREFIX."c_territoire_collectivites";

		
		$resql = $this->db->query($sql);
		if ( $resql )
		{

			for($i = 0 ; $i < $this->db->num_rows($resql) ; ++$i)
			{
				 
				$obj = $this->db->fetch_object($resql);
				


						$this->rowid						= $obj->rowid;
						$this->codeCollectivite                = $obj->codecollectivite;
		                $this->nomCollectivite                = $obj->nomcollectivite;
						$this->active					= $obj->active;
						$this->entity						= $obj->entity;


				$collectiviteListe[$i] = $obj;

			}
		}

		
		$this->db->free($resql);

		return $collectiviteListe;
	}



}
