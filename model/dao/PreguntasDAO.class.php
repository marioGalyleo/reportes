<?php
/**
 * Intreface DAO
 *
 * @author: http://phpdao.com
 * @date: 2012-01-18 16:29
 */
interface PreguntasDAO{
	
	
	/**
	 * @author: cgajardo
	 *
	 * Esta funci�n devuelve todas las preguntas que aun no se han asociado a un contenido
	 */
	public function getAllSinContenido();

	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @Return Preguntas 
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
 	 * @param pregunta primary key
 	 */
	public function delete($id);
	
	/**
 	 * Insert record to table
 	 *
 	 * @param Preguntas pregunta
 	 */
	public function insert($pregunta);
	
	/**
 	 * Update record in table
 	 *
 	 * @param Preguntas pregunta
 	 */
	public function update($pregunta);	

	/**
	 * Delete all rows
	 */
	public function clean();

	public function queryByIdentificadorMoodle($value);

	public function queryByIdContenido($value);


	public function deleteByIdentificadorMoodle($value);

	public function deleteByIdContenido($value);


}
?>