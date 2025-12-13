<?php
/**
 * Controlador de Instructores
 * 
 * Este controlador maneja las peticiones HTTP del CLIENTE y coordina
 * la comunicación entre la VISTA (cliente) y el MODELO (servidor).
 */

require_once __DIR__ . '/../models/Instructor.php';

class InstructorController {
    private $instructorModel;
    
    public function __construct() {
        $this->instructorModel = new Instructor();
    }
    
    /**
     * Maneja la petición GET del CLIENTE para listar instructores
     */
    public function index() {
        // SERVIDOR: Obtiene datos de la base de datos
        $instructors = $this->instructorModel->getAll();
        
        // SERVIDOR: Genera respuesta HTML para el CLIENTE
        require_once __DIR__ . '/../views/instructors/index.php';
    }
    
    /**
     * Maneja la petición GET del CLIENTE para mostrar formulario de creación
     */
    public function create() {
        $errors = [];
        require_once __DIR__ . '/../views/instructors/create.php';
    }
    
    /**
     * Maneja la petición POST del CLIENTE para crear un nuevo instructor
     */
    public function store() {
        // SERVIDOR: Valida datos recibidos del CLIENTE
        $errors = $this->validate($_POST);
        
        if (empty($errors)) {
            // SERVIDOR: Guarda en base de datos
            $id = $this->instructorModel->create($_POST);
            
            // SERVIDOR: Redirige al CLIENTE a la lista
            header('Location: /index.php?controller=instructor&action=index&success=created');
            exit;
        }
        
        // SERVIDOR: Devuelve formulario con errores al CLIENTE
        require_once __DIR__ . '/../views/instructors/create.php';
    }
    
    /**
     * Maneja la petición GET del CLIENTE para mostrar formulario de edición
     */
    public function edit() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: /index.php?controller=instructor&action=index&error=not_found');
            exit;
        }
        
        // SERVIDOR: Obtiene datos del instructor
        $instructor = $this->instructorModel->getById($id);
        
        if (!$instructor) {
            header('Location: /index.php?controller=instructor&action=index&error=not_found');
            exit;
        }
        
        $errors = [];
        require_once __DIR__ . '/../views/instructors/edit.php';
    }
    
    /**
     * Maneja la petición POST del CLIENTE para actualizar un instructor
     */
    public function update() {
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            header('Location: /index.php?controller=instructor&action=index&error=not_found');
            exit;
        }
        
        // SERVIDOR: Valida datos del CLIENTE
        $errors = $this->validate($_POST, $id);
        
        if (empty($errors)) {
            // SERVIDOR: Actualiza en base de datos
            $this->instructorModel->update($id, $_POST);
            
            // SERVIDOR: Redirige al CLIENTE
            header('Location: /index.php?controller=instructor&action=index&success=updated');
            exit;
        }
        
        // SERVIDOR: Devuelve formulario con errores
        $instructor = $this->instructorModel->getById($id);
        require_once __DIR__ . '/../views/instructors/edit.php';
    }
    
    /**
     * Maneja la petición GET del CLIENTE para eliminar un instructor
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            // SERVIDOR: Elimina de la base de datos
            $this->instructorModel->delete($id);
            header('Location: /index.php?controller=instructor&action=index&success=deleted');
        } else {
            header('Location: /index.php?controller=instructor&action=index&error=delete_failed');
        }
        exit;
    }
    
    /**
     * SERVIDOR: Valida los datos recibidos del CLIENTE
     * 
     * @param array $data Datos del formulario del CLIENTE
     * @param int|null $excludeId ID a excluir en validación de email
     * @return array Errores de validación
     */
    private function validate($data, $excludeId = null) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es requerido';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'El email es requerido';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no es válido';
        } elseif ($this->instructorModel->emailExists($data['email'], $excludeId)) {
            $errors['email'] = 'El email ya está registrado';
        }
        
        if (empty($data['hire_date'])) {
            $errors['hire_date'] = 'La fecha de contratación es requerida';
        }
        
        return $errors;
    }
}