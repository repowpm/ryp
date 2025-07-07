<?php
namespace App\Controllers;

use App\Models\ClienteModel;
use CodeIgniter\Controller;

class Clientes extends BaseController
{
    public function index()
    {
        // Aquí irá la lógica para mostrar la lista de clientes
        return view('clientes/index');
    }
} 