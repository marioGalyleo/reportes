<?php
/**
 * Class that operate on table 'contenidos'. Database Mysql.
 *
 * @author: http://phpdao.com
 * @date: 2012-01-18 16:29
 */
class ContenidosMySqlDAO implements ContenidosDAO{

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return ContenidosMySql 
	 */
	public function load($id){
		$sql = 'SELECT * FROM contenidos WHERE id = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM contenidos';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM contenidos ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param contenido primary key
 	 */
	public function delete($id){
		$sql = 'DELETE FROM contenidos WHERE id = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param ContenidosMySql contenido
 	 */
	public function insert($contenido){
		$sql = 'INSERT INTO contenidos (nombre, link_repaso, frase_no_logrado, frase_logrado, contenido_padre) VALUES (?, ?, ?, ?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($contenido->nombre);
		$sqlQuery->set($contenido->linkRepaso);
		$sqlQuery->set($contenido->fraseNoLogrado);
		$sqlQuery->set($contenido->fraseLogrado);
		$sqlQuery->setNumber($contenido->contenidoPadre);

		$id = $this->executeInsert($sqlQuery);	
		$contenido->id = $id;
		return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param ContenidosMySql contenido
 	 */
	public function update($contenido){
		$sql = 'UPDATE contenidos SET nombre = ?, link_repaso = ?, frase_no_logrado = ?, frase_logrado = ?, contenido_padre = ? WHERE id = ?';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($contenido->nombre);
		$sqlQuery->set($contenido->linkRepaso);
		$sqlQuery->set($contenido->fraseNoLogrado);
		$sqlQuery->set($contenido->fraseLogrado);
		$sqlQuery->setNumber($contenido->contenidoPadre);

		$sqlQuery->setNumber($contenido->id);
		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM contenidos';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByNombre($value){
		$sql = 'SELECT * FROM contenidos WHERE nombre = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByLinkRepaso($value){
		$sql = 'SELECT * FROM contenidos WHERE link_repaso = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByFraseNoLogrado($value){
		$sql = 'SELECT * FROM contenidos WHERE frase_no_logrado = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByFraseLogrado($value){
		$sql = 'SELECT * FROM contenidos WHERE frase_logrado = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByContenidoPadre($value){
		$sql = 'SELECT * FROM contenidos WHERE contenido_padre = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByNombre($value){
		$sql = 'DELETE FROM contenidos WHERE nombre = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByLinkRepaso($value){
		$sql = 'DELETE FROM contenidos WHERE link_repaso = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByFraseNoLogrado($value){
		$sql = 'DELETE FROM contenidos WHERE frase_no_logrado = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByFraseLogrado($value){
		$sql = 'DELETE FROM contenidos WHERE frase_logrado = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByContenidoPadre($value){
		$sql = 'DELETE FROM contenidos WHERE contenido_padre = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return ContenidosMySql 
	 */
	protected function readRow($row){
		$contenido = new Contenido();
		
		$contenido->id = $row['id'];
		$contenido->nombre = $row['nombre'];
		$contenido->linkRepaso = $row['link_repaso'];
		$contenido->fraseNoLogrado = $row['frase_no_logrado'];
		$contenido->fraseLogrado = $row['frase_logrado'];
		$contenido->contenidoPadre = $row['contenido_padre'];

		return $contenido;
	}
	
	protected function getList($sqlQuery){
		$tab = QueryExecutor::execute($sqlQuery);
		$ret = array();
		for($i=0;$i<count($tab);$i++){
			$ret[$i] = $this->readRow($tab[$i]);
		}
		return $ret;
	}
	
	/**
	 * Get row
	 *
	 * @return ContenidosMySql 
	 */
	protected function getRow($sqlQuery){
		$tab = QueryExecutor::execute($sqlQuery);
		if(count($tab)==0){
			return null;
		}
		return $this->readRow($tab[0]);		
	}
	
	/**
	 * Execute sql query
	 */
	protected function execute($sqlQuery){
		return QueryExecutor::execute($sqlQuery);
	}
	
		
	/**
	 * Execute sql query
	 */
	protected function executeUpdate($sqlQuery){
		return QueryExecutor::executeUpdate($sqlQuery);
	}

	/**
	 * Query for one row and one column
	 */
	protected function querySingleResult($sqlQuery){
		return QueryExecutor::queryForString($sqlQuery);
	}

	/**
	 * Insert row to table
	 */
	protected function executeInsert($sqlQuery){
		return QueryExecutor::executeInsert($sqlQuery);
	}
}
?>