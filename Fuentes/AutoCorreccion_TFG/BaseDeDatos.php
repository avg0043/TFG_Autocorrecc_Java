<?php

class BaseDeDatos
{
	/**  @var $conexion . objeto conexión. */
	private $conexion;

	/** @var string. nombre del host. */
	private $host = "localhost";

	/** @var string. nombre del usuario en la base de datos */
	private $usuario = "root";

	/** @var string. password del usuario en la base de datos */
	private $contraseña = "";

	/** @var string. nombre de la base de datos */
	private $basededatos = "autocorrecc_tfg";
	
	public function conectar(){
		$conexion = mysqli_connect($this->host, $this->usuario, $this->contraseña, $this->basededatos) 
								   or die("Problemas con la conexión");
		return $conexion;
	}

}