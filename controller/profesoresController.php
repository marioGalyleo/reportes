<?php

Class profesoresController Extends baseController {


public function view(){

	/*** should not have to call this here.... FIX ME ***/

	$this->registry->template->blog_heading = 'This is the blog heading';
	$this->registry->template->blog_content = 'This is the blog content';
	$this->registry->template->show('blog_view');
}

public function reporte(){
	//print $this->encrypter->encode("plataforma=utfsm&usuario=1104&grupo=24&quiz=71")."</br>";
        //print $this->encrypter->encode("platform=utfsm&user=618")."</br>";

	$PARAMS = $this->encrypter->decodeURL($_GET['params']);
	//$PARAMS=$_GET;
	if(isset($PARAMS['platform'])){
		$user_id_in_moodle = $PARAMS['user'];
		$platform = $PARAMS['platform'];
		$grupo_id_in_moodle=$PARAMS['group'];
		$grupo = DAOFactory::getGruposDAO()->getGrupoByIdEnMoodle($platform,$grupo_id_in_moodle);
		$quiz_id_in_moodle = $PARAMS['quiz'];

		//recuperamos los objetos que nos interesan
		$usuario = DAOFactory::getPersonasDAO()->getUserInPlatform($platform,$user_id_in_moodle);
		$quiz = DAOFactory::getQuizesDAO()->getGalyleoQuizByMoodleId($platform, $quiz_id_in_moodle);

	}
	elseif(isset($PARAMS['plataforma'])){
		$user_id = $PARAMS['usuario'];
		$platform = $PARAMS['plataforma'];
		$grupo_id=$PARAMS['grupo'];
		$quiz_id = $PARAMS['quiz'];

		//recuperamos los objetos que nos interesan
		$grupo = DAOFactory::getGruposDAO()->load($grupo_id);
		$usuario = DAOFactory::getPersonasDAO()->load($user_id);
		$quiz = DAOFactory::getQuizesDAO()->load($quiz_id);
	}
	if(DAOFactory::getGruposHasProfesoresDAO()->load($user_id,$grupo_id)==NULL){
		$this->registry->template->mesaje_personalizado="<h1>Usted no es Profesor</h1>";
		$this->registry->template->show('error404');
		return;

	}

	session_start();
	//recuperamos los objetos que nos interesan
	$institucion = DAOFactory::getInstitucionesDAO()-> getInstitucionByNombrePlataforma($platform);
	$curso = DAOFactory::getCursosDAO()->getCursoByGrupoId($grupo->id);
	$estudiantes_en_grupo = DAOFactory::getPersonasDAO()->getEstudiantesInGroup($grupo->id);
	$notas_grupo = DAOFactory::getIntentosDAO()->getNotasNombreGrupo($quiz->id,$grupo->id);
	$contenido_logro = DAOFactory::getIntentosDAO()->getLogroPorContenidoGrupo($quiz->id);
	//$nota_maxima= DAOFactory::getNotasDAO()->getMaxNotaInQuiz($quiz->id);

	//enviamos los siguientes valores a la vista
	$this->registry->template->titulo = 'Reporte Profesor';
	$_SESSION['usuario'] = $usuario;
	$_SESSION['curso'] = $curso;
	$_SESSION['grupo'] = $grupo;
	$_SESSION['platform'] = $platform;
	$_SESSION['notas_grupo'] = $notas_grupo;
	$_SESSION['nota_maxima'] = 100;
	$_SESSION['quiz']=$quiz;
	$this->registry->template->estudiantes =$estudiantes_en_grupo;
	$this->registry->template->total_estudiantes_grupo = count($estudiantes_en_grupo);
	$_SESSION['nombre_actividad'] = $quiz->nombre;
	$this->registry->template->fecha_cierre = $quiz->fechaCierre;
	$this->registry->template->contenido_logro = $contenido_logro;
	$this->registry->template->nombre_curso = $curso->nombre;
	$this->registry->template->nombre_grupo = $grupo->nombre;
	$this->registry->template->institucion = $institucion;

	// esto es lo necesario para la matriz de desempeño, TODO: deber�a tener su vista propia?
	$quizes_en_curso = DAOFactory::getQuizesDAO()->queryCerradosByIdCurso($curso->id);
	$matriz_desempeno = array();
	foreach ($quizes_en_curso as $quiz_en_curso){
		$contenidos=DAOFactory::getIntentosDAO()->getLogroPorContenidoGrupo($quiz_en_curso->id);
		$matriz_desempeno[$quiz_en_curso->nombre] = DAOFactory::getIntentosDAO()->getPromedioLogroPorContenido($quiz_en_curso->id, $grupo->id);
	}
        $promedio_grupo = 0;
        $numero_preguntas=0;
	foreach($matriz_desempeno[$quiz->nombre] as $contenido){
                $promedio_grupo += $contenido['logro']*$contenido['numero_preguntas'];
                $numero_preguntas+=$contenido['numero_preguntas'];
		$matriz_contenidos[$contenido['contenido']->nombre] = DAOFactory::getIntentosDAO()->getLogroPorContenido2($grupo->id, $quiz->id, $contenido['contenido']->id);
	}
        $_SESSION['promedio_grupo'] = round($promedio_grupo/$numero_preguntas);
	$tiempo = DAOFactory::getLogsDAO()->getTiempoTarea($quiz->fechaCierre, $grupo->id);
	//enviamos estos elementos a la vista
	$_SESSION['matriz_desempeño'] = $matriz_desempeno;
	$_SESSION['matriz_contenidos'] = $matriz_contenidos;
	$_SESSION['tiempos'] = $tiempo;
	//tiempo dedicado frente a cada quiz

	//finally
	$this->registry->template->show('profesor/reporte');
}

public function quiz_profesor(){
	//print $this->encrypter->encode("plataforma=utfsm&usuario=1104&grupo=24&quiz=71")."</br>";
	//$PARAMS = $this->encrypter->decodeURL($_GET['params']);
	session_start();

	$platform = $_SESSION['platform'];
	$usuario = $_SESSION['usuario'];
	$grupo = $_SESSION['grupo'];
	$quiz = $_SESSION['quiz'];
	if(DAOFactory::getGruposHasProfesoresDAO()->load($usuario->id,$grupo->id)==NULL){
		$this->registry->template->mesaje_personalizado="<h1>Usted no es Profesor</h1>";
		$this->registry->template->show('error404');
		return;

	}

	//recuperamos los objetos que nos interesan
	$institucion = DAOFactory::getInstitucionesDAO()-> getInstitucionByNombrePlataforma($platform);
	$curso = DAOFactory::getCursosDAO()->getCursoByGrupoId($grupo->id);
	$estudiantes_en_grupo = DAOFactory::getPersonasDAO()->getEstudiantesInGroup($grupo->id);
	$notas_grupo = DAOFactory::getIntentosDAO()->getNotasNombreGrupo($quiz->id,$grupo->id);
	$contenido_logro = DAOFactory::getIntentosDAO()->getLogroPorContenidoGrupo($quiz->id);
	//$nota_maxima= DAOFactory::getNotasDAO()->getMaxNotaInQuiz($quiz->id);

	//enviamos los siguientes valores a la vista
	$this->registry->template->titulo = 'Reporte Profesor';
	$_SESSION['usuario'] = $usuario;
	$_SESSION['notas_grupo'] = $notas_grupo;
	$_SESSION['nota_maxima'] = 100;
	$this->registry->template->estudiantes =$estudiantes_en_grupo;
	$this->registry->template->total_estudiantes_grupo = count($estudiantes_en_grupo);
	$_SESSION['nombre_actividad'] = $quiz->nombre;
	$this->registry->template->fecha_cierre = $quiz->fechaCierre;
	$this->registry->template->contenido_logro = $contenido_logro;
	$this->registry->template->nombre_curso = $curso->nombre;
	$this->registry->template->nombre_grupo = $grupo->nombre;
	$this->registry->template->institucion = $institucion;

	// esto es lo necesario para la matriz de desempeño, TODO: deber�a tener su vista propia?
	$quizes_en_curso = DAOFactory::getQuizesDAO()->queryCerradosByIdCurso($curso->id);
	$matriz_desempeno = array();
	foreach ($quizes_en_curso as $quiz_en_curso){
		$contenidos=DAOFactory::getIntentosDAO()->getLogroPorContenidoGrupo($quiz_en_curso->id);
		$matriz_desempeno[$quiz_en_curso->nombre] = DAOFactory::getIntentosDAO()->getPromedioLogroPorContenido($quiz_en_curso->id, $grupo->id);
	}
	//$_SESSION['promedio_grupo'] = $matriz_desempeño[$quiz->nombre]['logro'];
	$_SESSION['promedio_grupo'] = 0;
	$matriz_contenidos = array();
	foreach($matriz_desempeno[$quiz->nombre] as $contenido){
		$matriz_contenidos[$contenido['contenido']->nombre] = DAOFactory::getIntentosDAO()->getLogroPorContenido2($grupo->id, $quiz->id, $contenido['contenido']->id);
	}

	$tiempo = DAOFactory::getLogsDAO()->getTiempoTarea($quiz->fechaCierre, $grupo->id);
	//enviamos estos elementos a la vista
	$_SESSION['matriz_desempeño'] = $matriz_desempeno;
	$_SESSION['matriz_contenidos'] = $matriz_contenidos;
	$_SESSION['tiempos'] = $tiempo;

	$this->registry->template->show('profesor/quiz_profesor');

}

public function index(){
	session_start();
	//print $this->encrypter->encode("platform=utfsm&user=618");
	//578, 586, 587, 599, 581, 574
	$PARAMS = $this->encrypter->decodeURL($_GET['params']);
	//var_dump($PARAMS);
	if(isset($_SESSION['usuario'])){
		$usuario = $_SESSION['usuario'];
		$platform = $_SESSION['plataforma'];
	}
	elseif(isset($PARAMS['platform'])){
		$user_id_in_moodle = $PARAMS['user'];
		$platform = $PARAMS['platform'];
		$usuario = DAOFactory::getPersonasDAO()->getUserInPlatform($platform,$user_id_in_moodle);
		//lo agregamos a la session
		$_SESSION['usuario'] = $usuario;
		$_SESSION['plataforma'] = $platform;
	}
	elseif(isset($PARAMS['plataforma'])){
		$user_id = $PARAMS['usuario'];
		$platform = $PARAMS['plataforma'];
		$usuario = DAOFactory::getPersonasDAO()->load($user_id);
		//lo agregamos a la session
		$_SESSION['usuario'] = $usuario;
		$_SESSION['plataforma'] = $platform;
	}
	
	$cursos_usuarios = DAOFactory::getCursosDAO()->getCursosByProfesor($usuario->id);
	$institucion = DAOFactory::getInstitucionesDAO()-> getInstitucionByNombrePlataforma($platform);
	$this->registry->template->institucion = $institucion;
	
	// redireccionamos al 404 si usuario no existe
	if($usuario == null){
		$this->registry->template->mesaje_personalizado = "Debes ser un usuario de Galyleo para visitar esta p&aacute;gina.</br>".
				"Si tu cuenta fue creada recientemente debes esperar un par de minutos a que nuestros sistemas se actualicen.";
		//finally
		$this->registry->template->show('error404');
		return;
	}
	
	// o si no tiene cursos asociados
	elseif ($cursos_usuarios == null){
		$this->registry->template->mesaje_personalizado = "Tu cuenta no est&aacute; asociada a ning&uacute;n curso.</br>".
				"Probablemente llegaste hasta ac&aacute; por error.";
		//finally
		$this->registry->template->show('error404');
		return;
	}
	/* caso en que el usuario ya selecciono el curso desde la plataforma galyleo */
	elseif (isset($PARAMS['grupo'])){
		$id_grupo = $PARAMS['grupo'];
		$id_curso = $PARAMS['curso'];
		$quizes = DAOFactory::getQuizesDAO()->queryEvaluacionesByIdCurso($id_curso);
		
		$this->registry->template->titulo = 'Tus evaluaciones';
		$this->registry->template->usuario = $usuario;
	
                $this->registry->template->cursos = $cursos_usuarios;
		$this->registry->template->origen = '&plataforma='.$platform.'&usuario='.$usuario->id;
		$this->registry->template->encrypter = $this->encrypter;
		$this->registry->template->quizes = $quizes;
		$this->registry->template->id_curso = $id_curso;
		$this->registry->template->id_grupo = $id_grupo;
		$this->registry->template->retorno = $this->encrypter->encode('&plataforma='.$platform.'&usuario='.$usuario->id);
		//finally
		$this->registry->template->show('profesor/index_quizes');
		return;
	}
	
	/* caso en que el usuario ya selecciono el curso desde la plataforma moodle */
	elseif (isset($PARAMS['group'])){
                $grupo_moodle = $PARAMS['group'];
		$curso_moodle = $PARAMS['course'];
                $grupo = DAOFactory::getGruposDAO()->queryByIdentificadorMoodle($platform.'_'.$grupo_moodle);
		$curso = DAOFactory::getCursosDAO()->queryByIdentificadorMoodle($platform.'_'.$curso_moodle);	
		$quizes = DAOFactory::getQuizesDAO()->queryEvaluacionesByIdCurso($curso->id);
		
		$this->registry->template->titulo = 'Tus evaluaciones';
		$this->registry->template->usuario = $usuario;
		$this->registry->template->cursos = $cursos_usuarios;
		$this->registry->template->origen = '&plataforma='.$platform.'&usuario='.$usuario->id;
		$this->registry->template->encrypter = $this->encrypter;
		$this->registry->template->quizes = $quizes;
		$this->registry->template->id_curso = $curso->id;
		$this->registry->template->id_grupo = $id_grupo;
		$this->registry->template->retorno = $this->encrypter->encode('&plataforma='.$platform.'&usuario='.$usuario->id);
		//finally
		$this->registry->template->show('profesor/index_quizes');
		return;
	}
        
	
	$this->registry->template->titulo = 'Tus cursos';
	$this->registry->template->usuario = $usuario;
	$this->registry->template->cursos = $cursos_usuarios;
	$this->registry->template->origen = '&plataforma='.$platform.'&usuario='.$usuario->id;
	$this->registry->template->encrypter = $this->encrypter;
    //finally
    
    
    $this->registry->template->show('profesor/index_cursos');
}

public function data(){
    
    session_start();
    $usuario = explode(', ',$_GET['alumno']);
    
    foreach($_SESSION['notas_grupo'] as $id=>$nota){
        if($nota->nombre==$usuario[1] && $nota->apellido==$usuario[0]){
            $id_usuario = $id;
            break;
        }
    }
    print $this->encrypter->encode("plataforma=".$_SESSION['plataforma'].'&grupo='.$_SESSION['grupo']->id.'&curso='.$_SESSION['curso']->id.'&usuario='.$id_usuario.'&quiz='.$_SESSION['quiz']->id);
    $this->registry->template->show('debug');
}
}
?>
