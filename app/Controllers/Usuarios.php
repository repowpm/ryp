<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Usuarios extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        // Verificar si el usuario está logueado y es administrador
        if (!session()->get('id_usuario') || session()->get('id_rol') != 1) {
            session()->setFlashdata('swal', [
                'title' => 'Acceso Denegado',
                'text' => 'No tienes permisos para acceder a esta sección',
                'icon' => 'error'
            ]);
            return redirect()->to('dashboard');
        }

        return view('usuarios/index', [
            'title' => 'Gestión de Usuarios - Taller Rápido y Furioso'
        ]);
    }

    public function listar()
    {
        // Debug: Log de la petición
        log_message('debug', 'Petición GET recibida en listar usuarios');
        
        // Obtener parámetros de DataTable
        $draw = $this->request->getGet('draw');
        $start = $this->request->getGet('start') ?? 0;
        $length = $this->request->getGet('length') ?? 10;
        $search = $this->request->getGet('search')['value'] ?? '';
        $orderColumn = $this->request->getGet('order')[0]['column'] ?? 0;
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';

        // Obtener usuarios con paginación y búsqueda (con joins)
        $usuarios = $this->usuarioModel->obtenerUsuariosDataTable($start, $length, $search, $orderColumn, $orderDir);
        $total = $this->usuarioModel->contarUsuarios();
        $totalFiltrado = $this->usuarioModel->contarUsuariosFiltrados($search);

        // Log de información básica
        log_message('info', 'Listando usuarios - Total: ' . $total . ', Filtrado: ' . $totalFiltrado);

        // Preparar datos para DataTable
        $data = [];
        foreach ($usuarios as $usuario) {
            $data[] = [
                $usuario['id_usuario'],
                $usuario['username'],
                $usuario['nombres'] . ' ' . $usuario['apellido_paterno'],
                $usuario['correo'],
                $usuario['telefono'],
                $usuario['nombre_rol'],
                $usuario['nombre_estado'],
                $this->generarBotonesAccion($usuario['id_usuario'])
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $totalFiltrado,
            'data' => $data
        ]);
    }

    public function crear()
    {
        // Verificar permisos
        if (!session()->get('id_usuario') || session()->get('id_rol') != 1) {
            session()->setFlashdata('swal', [
                'title' => 'Acceso Denegado',
                'text' => 'No tienes permisos para acceder a esta sección',
                'icon' => 'error'
            ]);
            return redirect()->to('dashboard');
        }

        return view('usuarios/crear', [
            'title' => 'Crear Usuario - Taller Rápido y Furioso'
        ]);
    }

    public function guardar()
    {
        // Log de la petición
        log_message('info', 'Creando nuevo usuario');
        
        // Verificar permisos
        if (!session()->get('id_usuario') || session()->get('id_rol') != 1) {
            log_message('error', 'Acceso denegado en guardar usuario. id_usuario: ' . session()->get('id_usuario') . ', id_rol: ' . session()->get('id_rol'));
            return $this->response->setJSON(['success' => false, 'message' => 'Acceso denegado']);
        }

        // Validar datos
        $rules = [
            'nombres' => 'required|min_length[2]|max_length[100]|regex_match[/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/]',
            'apellido_paterno' => 'required|min_length[2]|max_length[50]|regex_match[/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/]',
            'apellido_materno' => 'max_length[50]|regex_match[/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]*$/]',
            'run' => 'required|regex_match[/^[0-9]{7,8}-[0-9Kk]{1}$/]|is_unique[wp_md_usuarios.run]',
            'telefono' => 'max_length[20]|regex_match[/^\+?[0-9]{8,15}$/]',
            'username' => 'required|min_length[3]|max_length[50]|alpha_numeric',
            'password' => 'required|min_length[6]',
            'id_rol' => 'required|in_list[1,2]',
            'id_estado_usuario' => 'required|in_list[1,2]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $usuario_creacion = session()->get('username') ?? 'SISTEMA';
            
            $datos = [
                'run' => $this->request->getPost('run'),
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'),
                'nombres' => $this->request->getPost('nombres'),
                'apellido_paterno' => $this->request->getPost('apellido_paterno'),
                'apellido_materno' => $this->request->getPost('apellido_materno'),
                'telefono' => $this->request->getPost('telefono'),
                'direccion' => $this->request->getPost('direccion'),
                'id_rol' => $this->request->getPost('id_rol'),
                'id_estado_usuario' => $this->request->getPost('id_estado_usuario'),
                'usuario_creacion' => $usuario_creacion
            ];

            $id = $this->usuarioModel->crearUsuario($datos);

            log_message('info', 'Usuario creado exitosamente con ID: ' . $id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'id' => $id
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ]);
        }
    }

    public function editar($id = null)
    {
        // Verificar permisos
        if (!session()->get('id_usuario') || session()->get('id_rol') != 1) {
            session()->setFlashdata('swal', [
                'title' => 'Acceso Denegado',
                'text' => 'No tienes permisos para acceder a esta sección',
                'icon' => 'error'
            ]);
            return redirect()->to('dashboard');
        }

        $usuario = $this->usuarioModel->obtenerUsuario($id);
        
        if (!$usuario) {
            session()->setFlashdata('swal', [
                'title' => 'Usuario no encontrado',
                'text' => 'El usuario que buscas no existe',
                'icon' => 'error'
            ]);
            return redirect()->to('usuarios');
        }

        return view('usuarios/editar', [
            'title' => 'Editar Usuario - Taller Rápido y Furioso',
            'usuario' => $usuario
        ]);
    }

    public function actualizar($id = null)
    {
        // Verificar permisos
        if (!session()->get('id_usuario') || session()->get('id_rol') != 1) {
            return $this->response->setJSON(['success' => false, 'message' => 'Acceso denegado']);
        }

        // Validar datos
        $rules = [
            'nombres' => 'required|min_length[2]|max_length[100]|regex_match[/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/]',
            'apellido_paterno' => 'required|min_length[2]|max_length[50]|regex_match[/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/]',
            'apellido_materno' => 'max_length[50]|regex_match[/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]*$/]',
            'run' => 'required|regex_match[/^[0-9]{7,8}-[0-9Kk]{1}$/]|is_unique[wp_md_usuarios.run,id_usuario,' . $id . ']',
            'telefono' => 'max_length[20]|regex_match[/^\+?[0-9]{8,15}$/]',
            'username' => 'required|min_length[3]|max_length[50]|alpha_numeric',
            'id_rol' => 'required|in_list[1,2]',
            'id_estado_usuario' => 'required|in_list[1,2]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $datos = [
                'run' => $this->request->getPost('run'),
                'username' => $this->request->getPost('username'),
                'nombres' => $this->request->getPost('nombres'),
                'apellido_paterno' => $this->request->getPost('apellido_paterno'),
                'apellido_materno' => $this->request->getPost('apellido_materno'),
                'telefono' => $this->request->getPost('telefono'),
                'direccion' => $this->request->getPost('direccion'),
                'id_rol' => $this->request->getPost('id_rol'),
                'id_estado_usuario' => $this->request->getPost('id_estado_usuario'),
                'usuario_modificacion' => session()->get('username')
            ];

            // Si se proporciona una nueva contraseña
            if ($this->request->getPost('password')) {
                $datos['password'] = $this->request->getPost('password');
            }

            $this->usuarioModel->actualizarUsuario($id, $datos);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminar($id = null)
    {
        // Verificar permisos
        if (!session()->get('id_usuario') || session()->get('id_rol') != 1) {
            return $this->response->setJSON(['success' => false, 'message' => 'Acceso denegado']);
        }

        // No permitir eliminar el propio usuario
        if ($id === session()->get('id_usuario')) {
            log_message('error', 'Intento de auto-eliminación: id en sesión = ' . session()->get('id_usuario') . ', id recibido = ' . $id);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No puedes eliminar tu propia cuenta'
            ]);
        }

        try {
            $this->usuarioModel->eliminarUsuario($id, session()->get('username'));

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ]);
        }
    }

    private function generarBotonesAccion($idUsuario)
    {
        $botones = '<div class="btn-group" role="group">';
        $botones .= '<a href="' . base_url('usuarios/editar/' . $idUsuario) . '" class="btn btn-sm btn-warning me-1" title="Editar"><i class="fas fa-edit"></i></a>';
        $botones .= '<button type="button" class="btn btn-sm btn-danger" onclick="eliminarUsuario(\'' . $idUsuario . '\')" title="Eliminar"><i class="fas fa-trash"></i></button>';
        $botones .= '</div>';
        
        return $botones;
    }
} 