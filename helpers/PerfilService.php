<?php
class PerfilService {
    private $perfilModel;

    public function __construct($perfilModel) {
        $this->perfilModel = $perfilModel;
    }

    public function obtenerPerfil($usuarioID) {
        return $this->perfilModel->obtenerPerfil($usuarioID);
    }
}
?>
