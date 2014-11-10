<?php
/*
 *  Copyright (C) 2012
 *     Ed Rackham (http://github.com/a1phanumeric/PHP-MySQL-Class)
 *  Changes to Version 0.8.1 copyright (C) 2013
 *	Christopher Harms (http://github.com/neurotroph)
*/

// MySQL Class
class MySQL {
	// Base variables

	var $sLastError;				// Holds the last error
	var $sLastQuery;				// Holds the last query
	var $aResult;					// Holds the MySQL query result
	var $iRecords;					// Holds the total number of records returned
	var $iAffected;					// Holds the total number of records affected
	var $aRawResults;				// Holds raw 'arrayed' results
	var $aArrayedResult;			// Holds a single 'arrayed' result
	var $aArrayedResults;			// Holds multiple 'arrayed' results (usually with a set key)
	var $QueryCount;
	
	var $sHostname = 'localhost';	// MySQL Hostname
	var $sUsername = 'root';		// MySQL Username
	var $sPassword = 'root';		// MySQL Password
	var $sDatabase = 'waldir_db';	// MySQL Database
	var $sDBLink;					// Database Connection Link
	var $ErrorC;
	// Class Constructor
	// Assigning values to variables
	function __construct()
	{
		$this->Connect();
	}
	
	// Connects class to database
	// $bPersistant (boolean) - Use persistant connection?
	function Connect( $bPersistant = false )
	{
		if( $this->sDBLink )
		{
			mysql_close( $this->sDBLink );
		}
		
		if($bPersistant)
		{
			$this->sDBLink = mysql_pconnect( $this->sHostname, $this->sUsername, $this->sPassword );
		}else{
			$this->sDBLink = mysql_connect( $this->sHostname, $this->sUsername, $this->sPassword );
		}
		
		if (!$this->sDBLink)
		{
   			$this->sLastError = 'Could not connect to server: ' . mysql_error( $this->sDBLink );
			return false;
		}
		
		if(!$this->UseDB())
		{
			$this->sLastError = 'Could not connect to database: ' . mysql_error( $this->sDBLink );
			return false;
		}
		return true;
	}
	
	// Select database to use
	function UseDB()
	{
		if (!mysql_select_db( $this->sDatabase, $this->sDBLink ) )
		{
			$this->sLastError ='Cannot select database: ' . mysql_error($this->sDBLink);
			return false;
		} else {
			return true;
		}
	}

	// Executes MySQL query
	function ExecuteSQL( $sSQLQuery )
	{
		$this->sLastQuery		= $sSQLQuery;
		if( $this->aResult 		= mysql_query($sSQLQuery, $this->sDBLink))
		{
			$this->iRecords 	= @mysql_num_rows( $this->aResult );
			$this->iAffected	= @mysql_affected_rows( $this->sDBLink );	
			++$this->QueryCount;
			return true;
		} else {
			$this->sLastError = mysql_error( $this->sDBLink );
			return false;
		}
	}
	
	// Adds a record to the database
	// based on the array key names
	function Insert( $aVars, $sTable, $aExclude = '' )
	{
		// Catch Exceptions
		if($aExclude == '') { $aExclude = array(); }
		array_push( $aExclude, 'MAX_FILE_SIZE' );
		
		// Prepare Variables
		$aVars = $this->SecureData( $aVars );
		
		$sSQLQuery = 'INSERT INTO `' . $sTable . '` SET ';
		foreach( $aVars as $iKey => $sValue )
		{
			if( in_array( $iKey, $aExclude ) )
			{
				continue;
			}
			$sSQLQuery .= '`' . $iKey . '` = "' . $sValue . '", ';
		}
		
		$sSQLQuery = substr( $sSQLQuery, 0, -2 );
		
		if( $this->ExecuteSQL( $sSQLQuery ) )
		{
			return true;
		} else {
			return false;
		}
	}
	
	// Deletes a record from the database
	function Delete( $sTable, $aWhere='', $sLimit='', $bLike=false )
	{
		$sSQLQuery = 'DELETE FROM `' . $sTable . '` WHERE ';
		if( is_array( $aWhere ) && $aWhere != '' )
		{
			// Prepare Variables
			$aWhere = $this->SecureData( $aWhere );
			foreach( $aWhere as $iKey => $sValue)
			{
				$sSQLQuery .= ( $bLike ) ? '`' . $iKey . '` LIKE "%' . $sValue . '%" AND ' : '`' . $iKey . '` = "' . $sValue . '" AND ';
			}
			$sSQLQuery = substr( $sSQLQuery, 0, -5 );
		}
		
		$sSQLQuery .= $sLimit != '' ? ' LIMIT ' .$sLimit : ''; // If Like is present
		return ( $this->ExecuteSQL( $sSQLQuery ) ) ? true : false; // Return true or false.
	}
	
	// Gets a single row from $1
	// where $2 is true
	function Select( $sFrom, $aWhere='', $sWhat='*', $sOrderBy='', $sLimit='', $bLike=false, $sOperand='AND' )
	{
		// Catch Exceptions
		if( trim( $sFrom ) == '' ){ return false; }
		
		$sSQLQuery = 'SELECT '.$sWhat.' FROM `' . $sFrom . '` WHERE ';
		
		if( is_array( $aWhere ) && $aWhere != '' )
		{
			// Prepare Variables
			$aWhere = $this->SecureData($aWhere);
			foreach($aWhere as $iKey=>$sValue)
			{
				$sSQLQuery .= ( $bLike ) ? '`'.$iKey.'` LIKE "%'.$sValue.'%" '.$sOperand.' ' : '`'.$iKey.'` = "'.$sValue.'" ' .$sOperand. ' ';
			}
			$sSQLQuery = substr( $sSQLQuery, 0, -5 );
		} else {
			$sSQLQuery = substr( $sSQLQuery, 0, -7 );
		}
		$sSQLQuery .= ( $sOrderBy != '' ) ? ' ORDER BY ' .$sOrderBy : ''; // If Order is present
		$sSQLQuery .= ( $sLimit != '' ) ? ' LIMIT ' .$sLimit : ''; // If Limit is present
		
		if( $this->ExecuteSQL( $sSQLQuery ) )
		{
			if($this->iRecords > 1){ $this->ArrayResults(); }
			if($this->iRecords == 1){ $this->ArrayResult(); }
			return true;
		} else {
			return false;
		}
		
	}
	
	// Updates a record in the database
	// based on WHERE
	function Update( $sTable, $aSet, $aWhere ='', $aExclude = '' )
	{
		// Catch Exceptions
		if(trim($sTable) == '' || !is_array($aSet) || empty( $aSet ) ){ return false; }
		if( $aExclude == '' ){ $aExclude = array(); }
		if( $aWhere == '' ){ $aWhere = array(); }

		array_push( $aExclude, 'MAX_FILE_SIZE' );
		
		$aSet 	= $this->SecureData( $aSet );
		$aWhere = $this->SecureData( $aWhere );
		
		// SET
		$sSQLQuery = 'UPDATE `' . $sTable . '` SET ';
		foreach( $aSet as $iKey => $sValue )
		{
			if( in_array( $iKey, $aExclude ) )
			{
				continue;
			}
			$sSQLQuery .= '`' . $iKey . '` = "' . $sValue . '", ';
		}
		
		$sSQLQuery = substr( $sSQLQuery, 0, -2 );
		
		// WHERE
		if( $aWhere && is_array( $aWhere ) )
		{
			$sSQLQuery .= ' WHERE ';
			foreach( $aWhere as $iKey => $sValue )
			{
				$sSQLQuery .= '`' . $iKey . '` = "' . $sValue . '" AND ';
			}
			$sSQLQuery = substr( $sSQLQuery, 0, -5 );
		}
		return ( $this->ExecuteSQL( $sSQLQuery ) ) ? true : false;
	}
	
	// 'Arrays' a single result
	function ArrayResult()
	{
		$this->aArrayedResult = mysql_fetch_assoc( $this->aResult ) or die ( mysql_error( $this->sDBLink ) );
		return $this->aArrayedResult;
	}

	// 'Arrays' multiple result
	function ArrayResults()
	{
		$this->aArrayedResults = array();
		while ( $aData = mysql_fetch_assoc( $this->aResult ) )
		{
			$this->aArrayedResults[] = $aData;
		}
		return $this->aArrayedResults;
	}
	
	// 'Arrays' multiple results with a key
	function ArrayResultsWithKey($sKey='id')
	{
		if( isset( $this->aArrayedResults ) )
		{
			unset($this->aArrayedResults);
		}
		$this->aArrayedResults = array();
		while( $aRow = mysql_fetch_assoc( $this->aResult ) )
		{
			foreach( $aRow as $sTheKey => $sTheValue )
			{
				$this->aArrayedResults[$aRow[$sKey]][$sTheKey] = $sTheValue;
			}
		}
		return $this->aArrayedResults;
	}
	
	// Performs a 'mysql_real_escape_string' on the entire array/string
	function SecureData( $aData )
	{
		if( is_array( $aData ) )
		{
			foreach( $aData as $iKey => $sVal )
			{
				if( !is_array( $aData[$iKey] ) )
				{
					$aData[$iKey] = mysql_real_escape_string( $aData[$iKey], $this->sDBLink );
					$aData[$iKey] = preg_replace( '/[^(\x20-\x7F)]*/', '', $aData[$iKey] );
				}
			}
		} else {
			$aData = mysql_real_escape_string( $aData, $this->sDBLink );
			$aData = preg_replace( '/[^(\x20-\x7F)]*/', '', $aData );
		}
		return $aData;
	}
	
}

$oMySQL = new MySQL();  

?>