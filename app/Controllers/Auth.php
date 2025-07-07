<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Auth extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        // Si ya está logueado, redirigir al dashboard
        if (session()->get('id_usuario')) {
            return redirect()->to('dashboard');
        }

        return view('auth/login', ['title' => 'Iniciar Sesión']);
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            session()->setFlashdata('swal', [
                'title' => 'Error',
                'text' => 'Por favor complete todos los campos',
                'icon' => 'error'
            ]);
            return redirect()->back();
        }

        $resultado = $this->usuarioModel->autenticar($username, $password);

        if ($resultado['status'] === 'success') {
            $usuario = $resultado['usuario'];
            // Crear sesión
            session()->set([
                'id_usuario' => $usuario['id_usuario'],
                'username' => $usuario['username'],
                'nombres' => $usuario['nombres'],
                'apellido_paterno' => $usuario['apellido_paterno'],
                'apellido_materno' => $usuario['apellido_materno'],
                'correo' => $usuario['correo'],
                'telefono' => $usuario['telefono'],
                'id_rol' => $usuario['id_rol'],
                'rol_nombre' => $usuario['nombre_rol'],
                'id_estado_usuario' => $usuario['id_estado_usuario']
            ]);

            session()->setFlashdata('swal', [
                'title' => '¡Bienvenido!',
                'text' => 'Has iniciado sesión correctamente',
                'icon' => 'success'
            ]);

            return redirect()->to('dashboard');
        } elseif ($resultado['status'] === 'inactive') {
            session()->setFlashdata('swal', [
                'title' => 'Usuario inactivo',
                'text' => 'Tu usuario está inactivo. Contacta al administrador.',
                'icon' => 'warning'
            ]);
            return redirect()->back();
        } else {
            session()->setFlashdata('swal', [
                'title' => 'Error de Autenticación',
                'text' => 'Usuario o contraseña incorrectos',
                'icon' => 'error'
            ]);
            return redirect()->back();
        }
    }

    public function logout()
    {
        session()->destroy();
        
        session()->setFlashdata('swal', [
            'title' => 'Sesión Cerrada',
            'text' => 'Has cerrado sesión correctamente',
            'icon' => 'info'
        ]);

        return redirect()->to('auth');
    }
} 