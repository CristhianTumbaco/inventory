<?php

class UsersDAO
{
    private $conexion = null;
    private $tabla = "users";
    private $clase = "Users";
    function __construct()
    {
        $this->conexion = Conexion::getConexion();
    }
    public function login($email)
    {
        $sql = "SELECT * FROM $this->tabla WHERE email=? AND status=?";
        $result = $this->conexion->ejecutarConsultaClase($sql, array($email, 1), $this->clase);
        return $result;
    }
    public function consult()
    {
        $array = array();
        $sql = "SELECT * FROM $this->tabla";
        $resulset = $this->conexion->ejecutarConsultaClase($sql, $array, $this->clase);
        return $resulset;
    }
    public function create(Users $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `name`, `email`, `password`, `rol`) VALUES (NULL, ?, ?, ?, ?)";
        $array = array(
            $data->getName(),
            $data->getEmail(),
            $data->getPassword(),
            $data->getRol()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function update(Users $data)
    {
        $sql = "UPDATE `$this->tabla` SET `name`=?";
        $array = array($data->getName());
        if ($data->getPassword() !== null) {
            $sql .= ", `password`=?";
            $array[] = $data->getPassword();
        }

        $sql .= ", `email`=?, `rol`=?, `status`=? WHERE `id`=?";
        $array[] = $data->getEmail();
        $array[] = $data->getRol();
        $array[] = $data->getStatus();
        $array[] = $data->getId();
        $num_filas_afectadas = $this->conexion->ejecutarActualizacion($sql, $array);
        return $num_filas_afectadas;
    }
    public function delete($id)
    {
        $num_filas_afectadas = $this->conexion->ejecutarActualizacion("DELETE FROM $this->tabla WHERE id=?", array($id));
        return $num_filas_afectadas;
    }
    public function consultId($id)
    {
        $sql = "SELECT * FROM $this->tabla WHERE id=?";
        $result = $this->conexion->ejecutarConsultaClase($sql, array($id), $this->clase);
        return $result;
    }
    public function repeated($data)
    {
        $sql = "SELECT * FROM $this->tabla WHERE email=?";
        $result = $this->conexion->ejecutarConsultaClase($sql, $data, $this->clase);
        if (sizeof($result) > 0) {
            return false;
        } else {
            return true;
        }
    }
}
