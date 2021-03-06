<?php
/**
 * Intreface DAO
 *
 * @author: http://phpdao.com
 * @date: 2012-01-18 16:29
 */
interface QuizesDAO{
	
	/**
	* cgajardo:
	* retorna la lista de quizes que ha rendido un usuario
	* @param int $idUsuario
	* @return Quize list
	*/
	public function getQuizesByUsuario($idUsuario);
	

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Quizes 
	 */
	public function load($id);

	/**
	 * Get all records from table
	 */
	public function queryAll();
	
	/**
	 * Get all records from table ordered by field
	 * @Param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn);
	
	/**
 	 * Delete record from table
 	 * @param quize primary key
 	 */
	public function delete($id);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Quizes quize
 	 */
	public function insert($quize);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Quizes quize
 	 */
	public function update($quize);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByNombre($value);

	public function queryByIdCurso($value);

	public function queryByFechaCierre($value);

	public function queryByPuntajeMaximo($value);

	public function queryByNotaMaxima($value);


	public function deleteByNombre($value);

	public function deleteByIdCurso($value);

	public function deleteByFechaCierre($value);

	public function deleteByPuntajeMaximo($value);

	public function deleteByNotaMaxima($value);


}
?>