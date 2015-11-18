<?php

namespace App\Controller;

class ProfesoresController extends AppController{
	
	/**
	 * Función que registra al profesor en el servicio web.
	 * Al registrarse obtendrá los parámetros LTI necesarios
	 * para poder configurar la tarea de tipo herramienta externa
	 * desde Moodle.
	 */
	public function registrarProfesor(){
		
		$nuevo_profesor = $this->Profesores->newEntity();
		
		if ($this->request->is('post')) {				
			$consumer_key = $this->__crearConsumerKey();
			$consumer_key_encriptada = $this->__encriptarCadena($consumer_key);
			$secret_encriptada = $this->__encriptarCadena($this->request->data['contraseña']);
			$nuevo_profesor->consumer_key = $consumer_key_encriptada;
			$nuevo_profesor->secret = $secret_encriptada;		
			$nuevo_profesor = $this->Profesores->patchEntity($nuevo_profesor, $this->request->data);
			
			if ($this->Profesores->save($nuevo_profesor)) {	
				return $this->redirect(['action' => 'mostrarParametrosLti', $this->request->data['correo']]);
			}
			$this->Flash->error(__('No ha sido posible registrar al profesor.'));		
		}
		$this->set('nuevo_profesor', $nuevo_profesor);
		
	}
	
	/**
	 * Función que encripta y devuelve la cadena pasada por parámetro.
	 *
	 * @param string $cadena	cadena a encriptar.
	 */
	private function __encriptarCadena($cadena){
	
		$key='clave_codificacion';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
		$encriptada = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
		return $encriptada; //Devuelve el string encriptado
	
	}
	
	/*
	 private function __desencriptarCadena($cadena){
	
	 $key='clave_codificacion';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
	 $desencriptada = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	 return $desencriptada;  //Devuelve el string desencriptado
	
	 }
	 */
	
	/**
	 * Función que crea un consumer_key aleatoriamente, que se le va a entregar al
	 * profesor al registrarse.
	 */
	private function __crearConsumerKey(){
	
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array();
		$alphaLength = strlen($alphabet) - 1;
	
		for ($i = 0; $i < 7; $i++){
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass);
	}
	
	/**
	 * Función que guarda en una variable los parámetros LTI del profesor
	 * para que puedan ser mostrados desde su vista asociada.
	 *
	 * @param string $correo	correo del profesor
	 */
	public function mostrarParametrosLti($correo){
	
		$this->set('parametros', $this->Profesores->find('all')->where(['correo' => $correo]));
	
	}
	
	/**
	 * Función que guarda en una variable los datos del profesor actual para que
	 * puedan ser mostrados desde su vista asociada.
	 */
	public function mostrarDatosProfesor(){
	
		session_start();
		$this->set('profesor', $this->Profesores->find('all')->where(['correo' => $_SESSION['lti_correo']]));
	
	}
	
	public function mostrarPanel(){
	
	}
	
	public function obtenerProfesorPorKeyCorreo($consumer_key, $correo){
		
		return $this->Profesores->find('all')
					            ->where(['consumer_key' => $consumer_key, 'correo' => $correo])
								->toArray();
		
	}
	
	public function obtenerProfesorPorKey($consumer_key){
		
		return $this->Profesores->find('all')
								->where(['consumer_key' => $consumer_key])
								->toArray();
		
	}
	
	public function obtenerProfesorPorCorreo($correo){
		
		return $this->Profesores->find('all')
								->where(['correo' => $correo])
								->toArray();
		
	}
	
}

?>