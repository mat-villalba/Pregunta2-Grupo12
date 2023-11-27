<?php
class PerfilModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function obtenerPerfil($usuarioID) {
        $sql = "SELECT u.fullname, u.birth_date, u.gender, c.name AS country, ci.name AS city, u.score, u.register_date
                FROM user u
                INNER JOIN country c ON u.idCountry = c.id
                INNER JOIN city ci ON u.idCity = ci.id
                WHERE u.id = ?";

        $consulta = $this->db->prepare($sql);
        $consulta->bind_param('i', $usuarioID);
        $consulta->execute();
        $result = $consulta->get_result();
        return $result->fetch_assoc();
    }
}
?>