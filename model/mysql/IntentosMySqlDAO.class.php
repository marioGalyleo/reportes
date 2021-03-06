<?php
/**
 * Class that operate on table 'intentos'. Database Mysql.
 *
 * @author: http://phpdao.com
 * @date: 2012-01-18 16:29
 */
class IntentosMySqlDAO implements IntentosDAO{
	
	/**
	 * Esta funcion devuelve un par: contenido-porcentaje para un usuario en un quiz dado
	 *
	 * @author cgajardo
	 * @param int $id_quiz
	 * @param int $id_usuario
	 */
	public function getLogroPorContenido($id_quiz, $id_usuario){
		
		$sql = 'SELECT p.id_contenido as contenido, floor(sum(i.puntaje_alumno)/sum(i.maximo_puntaje)*100) as logro, count(qp.id_pregunta) as numero_preguntas '.
				'FROM intentos  AS i, preguntas as p, quizes_has_preguntas as qp '.
				'WHERE p.id = i.id_pregunta AND i.id_persona = ? AND i.id_quiz = ? AND p.id = qp.id_pregunta '.
				'GROUP BY p.id_contenido';
		
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($id_usuario);
		$sqlQuery->set($id_quiz);
		
		return $this->getContenidoLogroArray($sqlQuery);
	}
        
        /*
         * jtoro: obtiene las notas de todos los usuario en un quiz
         * @param int $quiz_id
         */
        
        public function getLogroPorContenidoGrupo($id_quiz){
		
		$sql = 'SELECT p.id_contenido as contenido, floor(sum(i.puntaje_alumno)/sum(i.maximo_puntaje)*100) as logro, count(qp.id_pregunta) as numero_preguntas '.
				'FROM intentos  AS i, preguntas as p, quizes_has_preguntas as qp '.
				'WHERE p.id = i.id_pregunta AND i.id_quiz = ? AND p.id = qp.id_pregunta '.
				'GROUP BY p.id_contenido';
		
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($id_quiz);
		return $this->getContenidoLogroArray($sqlQuery);
	}
	
	/**
	 * cgajardo: obtiene la nota de un usuario en un quiz
	 *
	 * @param int $quiz_id
	 * @param int $usuario_id
	 * @return NotaLogro $logro
	 */
	public function getNotaInQuizByPersona($quiz_id, $usuario_id){
		$sql = 'SELECT nc.id_persona, max(nc.nota) as nota, nc.nmax as nota_maxima '.
				'FROM (SELECT id_persona, q.nota_maxima AS nmax, sum(puntaje_alumno)*q.nota_maxima/q.puntaje_maximo AS nota, numero_intento '.
				'FROM intentos, quizes as q WHERE id_quiz = ? AND q.id = ? AND id_persona = ? '.
				'GROUP BY id_persona, numero_intento) AS nc '.
				'WHERE nc.nota <= nc.nmax GROUP BY nc.id_persona';
	
		//TODO: revisar por qu� algunos valores se escapan de rango y mejorar esta consulta
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($quiz_id);
		$sqlQuery->set($quiz_id);
		$sqlQuery->set($usuario_id);
		return $this->getNotaLogro($sqlQuery);
	}
	
	/**
	 * cgajardo: devuelve un arreglo de notas del grupo, ordenadas de mayor a menor
	 * @param int $quiz
	 * @param int $grupo
	 */
	public function getNotasGrupo($quiz,$grupo){
		$sql = 'SELECT nc.id_persona, max(nc.nota) as nota, nc.nmax as nota_maxima  '.
				'FROM (SELECT id_persona, q.nota_maxima AS nmax, sum(puntaje_alumno)*q.nota_maxima/q.puntaje_maximo AS nota, numero_intento '.
				'FROM intentos, quizes as q '.
				' WHERE id_quiz = ? AND q.id = ? AND id_persona in ('.
				'SELECT id_persona FROM grupos_has_estudiantes WHERE id_grupo = ?) '.
				'GROUP BY id_persona, numero_intento) AS nc '.
				'WHERE nc.nota <= nc.nmax GROUP BY nc.id_persona ORDER BY nota DESC';
	
		//TODO: revisar por qu� algunos valores se escapan de rango y mejorar esta consulta
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($quiz);
		$sqlQuery->set($quiz);
		$sqlQuery->set($grupo);
		return $this->getNotaLogro($sqlQuery);
	}
	
            /*
             * jtoro:devuelve una arreglo de nombres,apellidos y notas del grupo ordenadas de mayor a menor
             * @param int $quiz
             * @param int $grupo
             */
        
        public function getNotasNombreGrupo($quiz,$grupo){
		$sql = 'SELECT p1.nombre,p1.apellido,p1.id,p2.nota,p2.nota_maxima
                        FROM (SELECT p.nombre,p.apellido,p.id
                        FROM personas p
                        JOIN grupos_has_estudiantes ON p.id=id_persona
                        WHERE id_grupo=?) AS p1
                        LEFT JOIN (SELECT p.nombre,p.apellido,nc.id_persona, max(nc.nota) as nota, nc.nmax as nota_maxima
                        FROM (SELECT id_persona, q.nota_maxima AS nmax, sum(puntaje_alumno)*q.nota_maxima/q.puntaje_maximo AS nota, numero_intento
                        FROM intentos, quizes as q
                        WHERE id_quiz = ? AND q.id = ? AND id_persona in (
                        SELECT id_persona FROM grupos_has_estudiantes WHERE id_grupo = ?)
                        GROUP BY id_persona, numero_intento) AS nc
                        JOIN personas AS p ON nc.id_persona=p.id
                        WHERE nc.nota <= nc.nmax GROUP BY nc.id_persona, p.nombre, p.apellido) AS p2
                        ON p1.nombre=p2.nombre
                        ORDER BY p2.nota DESC';
	
		//TODO: revisar por qu� algunos valores se escapan de rango y mejorar esta consulta
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($grupo);
                $sqlQuery->set($quiz);
		$sqlQuery->set($quiz);
		$sqlQuery->set($grupo);
		return $this->getNotaNombreLogro($sqlQuery);
	}
	/**
	 * cgajardo: Devuelve una lista de los quizes que ha respondido un usuario
	 * @param $idUsuarioGalyleo en identificador del usuario en la plataforma de reportes
	 * @return $quizes_id devuelve una lista de intentos
	 */
	public function getIntentosByUsuario($idUsuarioGalyleo){
		$sql = 'SELECT * FROM intentos WHERE id_persona = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($idUsuarioGalyleo);
	
		return $this->getList($sqlQuery);
	}
	
	/**
	 * cgajardo:
	 * obtener todos los intentos de un usuario para un control y una pregunta dada
	 */
	public function getIntentosByUsuarioQuizPregunta($idPersona, $idQuiz, $idPregunta){
		$sql = 'SELECT * FROM intentos WHERE id_persona = ?  AND id_quiz = ?  AND id_pregunta = ? ';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($idPersona);
		$sqlQuery->setNumber($idQuiz);
		$sqlQuery->setNumber($idPregunta);
	
		return $this->getRow($sqlQuery);
	}
	
	
	/**
	 * Get Domain object by primry key
	 *
	 * @param String $id primary key
	 * @return IntentosMySql 
	 */
	public function load($id, $idPersona, $idQuiz, $idPregunta){
		$sql = 'SELECT * FROM intentos WHERE id = ?  AND id_persona = ?  AND id_quiz = ?  AND id_pregunta = ? ';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		$sqlQuery->setNumber($idPersona);
		$sqlQuery->setNumber($idQuiz);
		$sqlQuery->setNumber($idPregunta);

		return $this->getRow($sqlQuery);
	}

	/**
	 * Get all records from table
	 */
	public function queryAll(){
		$sql = 'SELECT * FROM intentos';
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
	 * Get all records from table ordered by field
	 *
	 * @param $orderColumn column name
	 */
	public function queryAllOrderBy($orderColumn){
		$sql = 'SELECT * FROM intentos ORDER BY '.$orderColumn;
		$sqlQuery = new SqlQuery($sql);
		return $this->getList($sqlQuery);
	}
	
	/**
 	 * Delete record from table
 	 * @param intento primary key
 	 */
	public function delete($id, $idPersona, $idQuiz, $idPregunta){
		$sql = 'DELETE FROM intentos WHERE id = ?  AND id_persona = ?  AND id_quiz = ?  AND id_pregunta = ? ';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($id);
		$sqlQuery->setNumber($idPersona);
		$sqlQuery->setNumber($idQuiz);
		$sqlQuery->setNumber($idPregunta);

		return $this->executeUpdate($sqlQuery);
	}
	
	/**
 	 * Insert record to table
 	 *
 	 * @param IntentosMySql intento
 	 */
	public function insert($intento){
		$sql = 'INSERT INTO intentos (puntaje_alumno, fecha, numero_intento, id, id_persona, id_quiz, id_pregunta) VALUES (?, ?, ?, ?, ?, ?, ?)';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($intento->puntajePregunta);
		$sqlQuery->set($intento->fecha);
		$sqlQuery->setNumber($intento->numeroIntento);

		
		$sqlQuery->setNumber($intento->id);

		$sqlQuery->setNumber($intento->idPersona);

		$sqlQuery->setNumber($intento->idQuiz);

		$sqlQuery->setNumber($intento->idPregunta);

		$this->executeInsert($sqlQuery);	
		//$intento->id = $id;
		//return $id;
	}
	
	/**
 	 * Update record in table
 	 *
 	 * @param IntentosMySql intento
 	 */
	public function update($intento){
		$sql = 'UPDATE intentos SET puntaje_alumno = ?, fecha = ?, numero_intento = ? WHERE id = ?  AND id_persona = ?  AND id_quiz = ?  AND id_pregunta = ? ';
		$sqlQuery = new SqlQuery($sql);
		
		$sqlQuery->set($intento->puntajePregunta);
		$sqlQuery->set($intento->fecha);
		$sqlQuery->setNumber($intento->numeroIntento);

		
		$sqlQuery->setNumber($intento->id);

		$sqlQuery->setNumber($intento->idPersona);

		$sqlQuery->setNumber($intento->idQuiz);

		$sqlQuery->setNumber($intento->idPregunta);

		return $this->executeUpdate($sqlQuery);
	}

	/**
 	 * Delete all rows
 	 */
	public function clean(){
		$sql = 'DELETE FROM intentos';
		$sqlQuery = new SqlQuery($sql);
		return $this->executeUpdate($sqlQuery);
	}

	public function queryByPuntajePregunta($value){
		$sql = 'SELECT * FROM intentos WHERE puntaje_alumno = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByFecha($value){
		$sql = 'SELECT * FROM intentos WHERE fecha = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->getList($sqlQuery);
	}

	public function queryByNumeroIntento($value){
		$sql = 'SELECT * FROM intentos WHERE numero_intento = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->getList($sqlQuery);
	}


	public function deleteByPuntajePregunta($value){
		$sql = 'DELETE FROM intentos WHERE puntaje_alumno = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByFecha($value){
		$sql = 'DELETE FROM intentos WHERE fecha = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->set($value);
		return $this->executeUpdate($sqlQuery);
	}

	public function deleteByNumeroIntento($value){
		$sql = 'DELETE FROM intentos WHERE numero_intento = ?';
		$sqlQuery = new SqlQuery($sql);
		$sqlQuery->setNumber($value);
		return $this->executeUpdate($sqlQuery);
	}


	
	/**
	 * Read row
	 *
	 * @return IntentosMySql 
	 */
	protected function readRow($row){
		$intento = new Intento();
		
		$intento->id = $row['id'];
		$intento->idPersona = $row['id_persona'];
		$intento->idQuiz = $row['id_quiz'];
		$intento->idPregunta = $row['id_pregunta'];
		$intento->puntajePregunta = $row['puntaje_alumno'];
		$intento->fecha = $row['fecha'];
		$intento->numeroIntento = $row['numero_intento'];

		return $intento;
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
	 * @return IntentosMySql 
	 */
	protected function getRow($sqlQuery){
		$tab = QueryExecutor::execute($sqlQuery);
		if(count($tab)==0){
			return null;
		}
		return $this->readRow($tab[0]);		
	}
	
	/**
	 * @author cgajardo
	 * @param object $sqlQuery
	 * @return NotaLogro $ret
	 */
	protected function getNotaLogro($sqlQuery){
	
		$tab = QueryExecutor::execute($sqlQuery);
		$ret = array();
		for($i=0;$i<count($tab);$i++){
			$notaLogro = new NotaLogro();
			$notaLogro->id = $tab[$i]['id_persona'];
			$notaLogro->nota = round($tab[$i]['nota']);
			$notaLogro->logro =  round($tab[$i]['nota']*100/$tab[$i]['nota_maxima']);
                        $ret[$i] = $notaLogro;
		}
		return $ret;
	}
                
        protected function getNotaNombreLogro($sqlQuery){
	
		$tab = QueryExecutor::execute($sqlQuery);
		$ret = array();
		for($i=0;$i<count($tab);$i++){
			$notaLogro = new NotaLogro();
			$notaLogro->id = $tab[$i]['id_persona'];
			$notaLogro->nota = round($tab[$i]['nota']);
			$notaLogro->logro =  round($tab[$i]['nota']*100/$tab[$i]['nota_maxima']);
                        $notaLogro->nombre = $tab[$i]['nombre'];
                        $notaLogro->apellido = $tab[$i]['apellido'];
                        $ret[$i] = $notaLogro;
		}
		return $ret;
	}
	
	/**
	 * @author cgajardo
	 * @param object $sqlQuery
	 */
	protected function getArray($sqlQuery){
		$tab = QueryExecutor::execute($sqlQuery);
		$ret = array();
		for($i=0;$i<count($tab);$i++){
			$ret[$i] = $tab[$i]['nota'];
		}
		return $ret;
	}
	
	protected function getContenidoLogroArray($sqlQuery){
		$tab = QueryExecutor::execute($sqlQuery);
		$ret = array();
		for($i=0;$i<count($tab);$i++){
			$contenido = DAOFactory::getContenidosDAO()->load($tab[$i]['contenido']);
			$ret[$i] = array('contenido' => $contenido, 'logro'=> $tab[$i]['logro'], 'numero_preguntas' => $tab[$i]['numero_preguntas']);
		}
		return $ret;
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