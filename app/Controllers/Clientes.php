<?php
namespace App\Controllers;

use App\Models\ClienteModel;

class Clientes extends BaseController
{
    protected $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
    }

    public function index()
    {
        if (!session()->get('id_usuario')) {
            session()->setFlashdata('swal', [
                'title' => 'Acceso Denegado',
                'text' => 'Debes iniciar sesión para acceder a esta sección',
                'icon' => 'error'
            ]);
            return redirect()->to('dashboard');
        }
        return view('clientes/index', [
            'title' => 'Gestión de Clientes - Taller Rápido y Furioso'
        ]);
    }

    public function listar()
    {
        $clientes = $this->clienteModel->obtenerClientes();
        $data = [];
        foreach ($clientes as $cliente) {
            $data[] = [
                $cliente['id_cliente'],
                $cliente['run'],
                $cliente['nombres'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno'],
                $cliente['correo'],
                $cliente['telefono'],
                $cliente['direccion'],
                $this->generarBotonesAccion($cliente['id_cliente'])
            ];
        }
        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function crear()
    {
        if (!session()->get('id_usuario')) {
            session()->setFlashdata('swal', [
                'title' => 'Acceso Denegado',
                'text' => 'Debes iniciar sesión para acceder a esta sección',
                'icon' => 'error'
            ]);
            return redirect()->to('dashboard');
        }
        return view('clientes/crear', [
            'title' => 'Crear Cliente - Taller Rápido y Furioso'
        ]);
    }

    public function guardar()
    {
        if (!session()->get('id_usuario')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Acceso denegado']);
        }
        $rules = [
            'nombres' => 'required|min_length[2]|max_length[100]',
            'apellido_paterno' => 'required|min_length[2]|max_length[100]',
            'apellido_materno' => 'max_length[100]',
            'run' => 'required|regex_match[/^[0-9]{7,8}-[0-9Kk]{1}$/]|is_unique[wp_md_clientes.run]',
            'telefono' => 'max_length[15]',
            'direccion' => 'max_length[255]'
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
                'nombres' => $this->request->getPost('nombres'),
                'apellido_paterno' => $this->request->getPost('apellido_paterno'),
                'apellido_materno' => $this->request->getPost('apellido_materno'),
                'telefono' => $this->request->getPost('telefono'),
                'direccion' => $this->request->getPost('direccion'),
                'usuario_creacion' => $usuario_creacion
            ];
            $cliente = $this->clienteModel->insertarCliente($datos);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'cliente' => $cliente
            ]);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'correo') !== false) {
                $msg = 'No se pudo generar un correo único para el cliente. Por favor, verifique los datos o contacte al administrador.';
            }
            log_message('error', 'Error al crear cliente: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $msg
            ]);
        }
    }

    public function editar($id = null)
    {
        if (!session()->get('id_usuario')) {
            session()->setFlashdata('swal', [
                'title' => 'Acceso Denegado',
                'text' => 'Debes iniciar sesión para acceder a esta sección',
                'icon' => 'error'
            ]);
            return redirect()->to('dashboard');
        }
        $cliente = $this->clienteModel->obtenerCliente($id);
        if (!$cliente) {
            session()->setFlashdata('swal', [
                'title' => 'Cliente no encontrado',
                'text' => 'El cliente que buscas no existe',
                'icon' => 'error'
            ]);
            return redirect()->to('clientes');
        }
        return view('clientes/editar', [
            'title' => 'Editar Cliente - Taller Rápido y Furioso',
            'cliente' => $cliente
        ]);
    }

    public function actualizar($id = null)
    {
        if (!session()->get('id_usuario')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Acceso denegado']);
        }
        $rules = [
            'nombres' => 'required|min_length[2]|max_length[100]',
            'apellido_paterno' => 'required|min_length[2]|max_length[100]',
            'apellido_materno' => 'max_length[100]',
            'run' => 'required|regex_match[/^[0-9]{7,8}-[0-9Kk]{1}$/]',
            'telefono' => 'max_length[15]',
            'direccion' => 'max_length[255]'
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $this->validator->getErrors()
            ]);
        }
        try {
            // Obtener el cliente actual para mantener el correo existente
            $clienteActual = $this->clienteModel->obtenerCliente($id);
            if (!$clienteActual) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ]);
            }
            
            $usuario_actualizacion = session()->get('username') ?? 'SISTEMA';
            $datos = [
                'run' => $this->request->getPost('run'),
                'nombres' => $this->request->getPost('nombres'),
                'apellido_paterno' => $this->request->getPost('apellido_paterno'),
                'apellido_materno' => $this->request->getPost('apellido_materno'),
                'telefono' => $this->request->getPost('telefono'),
                'correo' => $clienteActual['correo'], // Mantener el correo existente
                'direccion' => $this->request->getPost('direccion'),
                'usuario_actualizacion' => $usuario_actualizacion
            ];
            $this->clienteModel->actualizarCliente($id, $datos);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar cliente: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminar($id = null)
    {
        if (!session()->get('id_usuario')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Acceso denegado']);
        }
        try {
            $usuario = session()->get('username') ?? 'SISTEMA';
            $this->clienteModel->eliminarCliente($id, $usuario);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar cliente: ' . $e->getMessage()
            ]);
        }
    }

    private function generarBotonesAccion($idCliente)
    {
        return '<a href="' . base_url('clientes/editar/' . $idCliente) . '" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>' .
            '<button onclick="eliminarCliente(\'' . $idCliente . '\')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>';
    }
} 