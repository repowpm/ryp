<?php
namespace App\Controllers;

use App\Models\VehiculoModel;
use App\Models\ClienteModel;
use CodeIgniter\Controller;

class Vehiculos extends BaseController
{
    protected $vehiculoModel;
    protected $clienteModel;
    protected $marcaModeloModel;
    protected $tipoVehiculoModel;

    public function __construct()
    {
        $this->vehiculoModel = new VehiculoModel();
        $this->clienteModel = new ClienteModel();
        $this->marcaModeloModel = new \App\Models\MarcaModeloModel();
        $this->tipoVehiculoModel = new \App\Models\TipoVehiculoModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Gestión de Vehículos',
            'vehiculos' => $this->vehiculoModel->getVehiculosWithClientes()
        ];
        return view('vehiculos/index', $data);
    }

    public function crear()
    {
        $data = [
            'marcas' => $this->marcaModeloModel->getMarcas(),
            'clientes' => $this->clienteModel->getClientesActivos(),
            'tipos' => $this->tipoVehiculoModel->getTiposActivos()
        ];
        return view('vehiculos/crear', $data);
    }

    public function editar($id = null)
    {
        log_message('info', '=== INICIO editar vehículo ===');
        log_message('info', 'ID recibido: ' . $id);
        
        if ($id === null) {
            log_message('error', 'ID de vehículo es null');
            return redirect()->to('/vehiculos')->with('error', 'ID de vehículo no válido');
        }

        $vehiculo = $this->vehiculoModel->getVehiculoWithCliente($id);
        log_message('info', 'Vehículo encontrado: ' . ($vehiculo ? 'SÍ' : 'NO'));
        
        if (!$vehiculo) {
            log_message('error', 'Vehículo no encontrado para ID: ' . $id);
            return redirect()->to('/vehiculos')->with('error', 'Vehículo no encontrado');
        }

        log_message('info', 'Vehículo encontrado: ' . json_encode($vehiculo));
        log_message('info', '=== FIN editar vehículo ===');

        $data = [
            'vehiculo' => $vehiculo,
            'marcas' => $this->marcaModeloModel->getMarcas(),
            'clientes' => $this->clienteModel->getClientesActivos(),
            'tipos' => $this->tipoVehiculoModel->getTiposActivos()
        ];
        return view('vehiculos/editar', $data);
    }

    // AJAX Methods
    public function getVehiculos()
    {
        try {
            log_message('info', '=== INICIO getVehiculos ===');
            
            // Primero, verificar si hay vehículos en la tabla
            $totalVehiculos = $this->vehiculoModel->countAllResults();
            log_message('info', 'Total de vehículos en la tabla: ' . $totalVehiculos);
            
            $vehiculos = $this->vehiculoModel->getVehiculosWithClientes();
            log_message('info', 'Vehículos obtenidos del modelo: ' . count($vehiculos));
            
            // Debug: imprimir los primeros registros
            if (!empty($vehiculos)) {
                log_message('info', 'Primer registro: ' . json_encode($vehiculos[0]));
            }
            
            $response = [
                'success' => true,
                'data' => $vehiculos
            ];
            
            log_message('info', 'Respuesta JSON enviada con ' . count($vehiculos) . ' registros');
            log_message('info', '=== FIN getVehiculos ===');
            
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener vehículos: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener vehículos: ' . $e->getMessage()
            ]);
        }
    }





    public function getModelosByMarca()
    {
        try {
            $marca = $this->request->getPost('marca');
            
            log_message('info', 'Solicitud de modelos para marca: ' . $marca);
            
            if (!$marca) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Marca no especificada'
                ]);
            }

            $modelos = $this->marcaModeloModel->getModelosByMarca($marca);
            
            log_message('info', 'Modelos encontrados: ' . count($modelos));
            
            // Debug: imprimir la respuesta
            $response = [
                'success' => true,
                'data' => $modelos
            ];
            
            log_message('info', 'Respuesta JSON: ' . json_encode($response));
            
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener modelos: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener modelos: ' . $e->getMessage()
            ]);
        }
    }

    public function buscarClientes()
    {
        try {
            log_message('info', '=== INICIO buscarClientes ===');
            
            $query = $this->request->getPost('query');
            log_message('info', 'Query recibida: ' . $query);
            
            if (!$query || strlen($query) < 2) {
                log_message('info', 'Query inválida o muy corta');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Query debe tener al menos 2 caracteres'
                ]);
            }

            // Buscar clientes que coincidan con la consulta
            $clientes = $this->clienteModel->buscarClientes($query);
            log_message('info', 'Clientes encontrados: ' . count($clientes));
            
            if (!empty($clientes)) {
                log_message('info', 'Primer cliente: ' . json_encode($clientes[0]));
            }
            
            $response = [
                'success' => true,
                'data' => $clientes
            ];
            
            log_message('info', 'Respuesta enviada: ' . json_encode($response));
            log_message('info', '=== FIN buscarClientes ===');
            
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Error al buscar clientes: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al buscar clientes: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteVehiculo()
    {
        try {
            $id = $this->request->getPost('id');
            
            if (!$id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID de vehículo no válido'
                ]);
            }

            $usuario = session()->get('username') ?? 'SISTEMA';
            $this->vehiculoModel->eliminarVehiculo($id, $usuario);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Vehículo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar vehículo: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar vehículo: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminar($id = null)
    {
        if ($id === null) {
            return redirect()->to('/vehiculos')->with('error', 'ID de vehículo no válido');
        }

        try {
            $usuario = session()->get('username') ?? 'SISTEMA';
            $this->vehiculoModel->eliminarVehiculo($id, $usuario);

            return redirect()->to('/vehiculos')->with('swal', [
                'title' => '¡Eliminado!',
                'text' => 'Vehículo eliminado exitosamente',
                'icon' => 'success',
                'confirmButtonText' => 'Aceptar'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar vehículo: ' . $e->getMessage());
            return redirect()->to('/vehiculos')->with('swal', [
                'title' => 'Error',
                'text' => 'Error al eliminar vehículo: ' . $e->getMessage(),
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
        }
    }


    public function guardar()
    {
        $rules = [
            'patente' => 'required|max_length[10]|regex_match[/^[A-Za-z0-9]{4,10}$/]',
            'marca' => 'required|max_length[50]|min_length[2]',
            'modelo' => 'required|max_length[50]|min_length[2]',
            'anio' => 'required|integer|greater_than[1900]|less_than[2031]',
            'id_tipo' => 'required|max_length[5]|min_length[5]',
            'id_cliente' => 'required|max_length[10]|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patente' => strtoupper($this->request->getPost('patente')),
            'marca' => $this->request->getPost('marca'),
            'modelo' => $this->request->getPost('modelo'),
            'anio' => $this->request->getPost('anio'),
            'id_tipo' => $this->request->getPost('id_tipo'),
            'id_cliente' => $this->request->getPost('id_cliente'),
            'usuario_creacion' => session()->get('username') ?? 'SISTEMA'
        ];

        try {
            // Verificar si la patente ya existe (excluyendo eliminados lógicos)
            if (!$this->vehiculoModel->verificarPatenteUnica($data['patente'])) {
                return redirect()->back()->withInput()->with('swal', [
                    'title' => 'Error',
                    'text' => 'Ya existe un vehículo con esta patente',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }

            // Verificar que el cliente existe
            $cliente = $this->clienteModel->obtenerCliente($data['id_cliente']);
            if (!$cliente) {
                return redirect()->back()->withInput()->with('swal', [
                    'title' => 'Error',
                    'text' => 'El cliente seleccionado no existe',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }

            $vehiculo = $this->vehiculoModel->insertarVehiculo($data);

            return redirect()->to('/vehiculos')->with('swal', [
                'title' => '¡Éxito!',
                'text' => 'Vehículo creado exitosamente',
                'icon' => 'success',
                'confirmButtonText' => 'Aceptar'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al crear vehículo: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('swal', [
                'title' => 'Error',
                'text' => 'No se pudo crear el vehículo. Por favor, verifica los datos.',
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
        }
    }

    public function actualizar($id = null)
    {
        if ($id === null) {
            return redirect()->to('/vehiculos')->with('error', 'ID de vehículo no válido');
        }

        $rules = [
            'patente' => 'required|max_length[10]|regex_match[/^[A-Za-z0-9]{4,10}$/]',
            'marca' => 'required|max_length[50]|min_length[2]',
            'modelo' => 'required|max_length[50]|min_length[2]',
            'anio' => 'required|integer|greater_than[1900]|less_than[2031]',
            'id_tipo' => 'required|max_length[5]|min_length[5]',
            'id_cliente' => 'required|max_length[10]|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patente' => strtoupper($this->request->getPost('patente')),
            'marca' => $this->request->getPost('marca'),
            'modelo' => $this->request->getPost('modelo'),
            'anio' => $this->request->getPost('anio'),
            'id_tipo' => $this->request->getPost('id_tipo'),
            'id_cliente' => $this->request->getPost('id_cliente'),
            'usuario_actualizacion' => session()->get('username') ?? 'SISTEMA'
        ];

        try {
            // Verificar que el vehículo existe
            $vehiculoActual = $this->vehiculoModel->obtenerVehiculo($id);
            if (!$vehiculoActual) {
                return redirect()->back()->withInput()->with('swal', [
                    'title' => 'Error',
                    'text' => 'El vehículo no existe',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }

            // Verificar si la patente ya existe en otro vehículo (excluyendo el actual)
            if (!$this->vehiculoModel->verificarPatenteUnica($data['patente'], $id)) {
                return redirect()->back()->withInput()->with('swal', [
                    'title' => 'Error',
                    'text' => 'Ya existe otro vehículo con esta patente',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }

            // Verificar que el cliente existe
            $cliente = $this->clienteModel->obtenerCliente($data['id_cliente']);
            if (!$cliente) {
                return redirect()->back()->withInput()->with('swal', [
                    'title' => 'Error',
                    'text' => 'El cliente seleccionado no existe',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }

            $this->vehiculoModel->actualizarVehiculo($id, $data);

            return redirect()->to('/vehiculos')->with('swal', [
                'title' => '¡Éxito!',
                'text' => 'Vehículo actualizado exitosamente',
                'icon' => 'success',
                'confirmButtonText' => 'Aceptar'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar vehículo: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('swal', [
                'title' => 'Error',
                'text' => 'No se pudo actualizar el vehículo. Por favor, verifica los datos.',
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
        }
    }
} 