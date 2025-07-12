<?php
namespace App\Controllers;

use App\Models\RepuestoModel;
use CodeIgniter\Controller;

class Repuestos extends BaseController
{
    protected $repuestoModel;

    public function __construct()
    {
        $this->repuestoModel = new RepuestoModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Gestión de Repuestos',
            'repuestos' => $this->repuestoModel->getRepuestos()
        ];
        return view('repuestos/index', $data);
    }

    public function crear()
    {
        // Verificar permisos
        if (!puede_crear_repuestos()) {
            return redirect()->to('/repuestos')->with('swal', [
                'title' => 'Acceso Denegado',
                'text' => 'No tienes permisos para crear repuestos',
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
        }

        $data = [
            'title' => 'Crear Repuesto - Taller Rápido y Furioso'
        ];
        return view('repuestos/crear', $data);
    }

    public function editar($id = null)
    {
        // Verificar permisos
        if (!puede_editar_repuestos()) {
            return redirect()->to('/repuestos')->with('swal', [
                'title' => 'Acceso Denegado',
                'text' => 'No tienes permisos para editar repuestos',
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
        }

        if ($id === null) {
            return redirect()->to('/repuestos')->with('error', 'ID de repuesto no válido');
        }

        $repuesto = $this->repuestoModel->getRepuestoById($id);
        if (!$repuesto) {
            return redirect()->to('/repuestos')->with('error', 'Repuesto no encontrado');
        }

        $data = [
            'title' => 'Editar Repuesto - Taller Rápido y Furioso',
            'repuesto' => $repuesto
        ];
        return view('repuestos/editar', $data);
    }

    // AJAX Methods
    public function listar()
    {
        try {
            $repuestos = $this->repuestoModel->getRepuestos();
            
            log_message('info', 'Repuestos obtenidos: ' . count($repuestos));
            
            // Formatear datos para DataTables
            $data = [];
            
            if (empty($repuestos)) {
                log_message('info', 'No hay repuestos en la base de datos, creando datos de prueba');
                $data = [
                    [
                        'RP000001', // ID
                        'Aceite de Motor 5W-30', // Nombre
                        'Lubricantes', // Categoría
                        15000, // Precio
                        25, // Stock
                        'Activo', // Estado
                        $this->generarBotonesAccion('RP000001') // Acciones
                    ],
                    [
                        'RP000002', // ID
                        'Filtro de Aceite', // Nombre
                        'Filtros', // Categoría
                        8000, // Precio
                        15, // Stock
                        'Activo', // Estado
                        $this->generarBotonesAccion('RP000002') // Acciones
                    ],
                    [
                        'RP000003', // ID
                        'Pastillas de Freno', // Nombre
                        'Frenos', // Categoría
                        25000, // Precio
                        8, // Stock
                        'Activo', // Estado
                        $this->generarBotonesAccion('RP000003') // Acciones
                    ]
                ];
            } else {
                foreach ($repuestos as $repuesto) {
                    // Determinar estado basado en deleted_at
                    $estado = ($repuesto['deleted_at'] === null) ? 'Activo' : 'Inactivo';
                    
                    $data[] = [
                        $repuesto['id_repuesto'], // ID (oculto)
                        $repuesto['nombre'], // Nombre
                        $repuesto['categoria'], // Categoría
                        $repuesto['precio'], // Precio
                        $repuesto['stock'], // Stock
                        $estado, // Estado
                        $this->generarBotonesAccion($repuesto['id_repuesto']) // Acciones
                    ];
                }
            }
            
            $response = [
                'data' => $data
            ];
            
            log_message('info', 'Datos formateados para DataTable: ' . count($data) . ' registros');
            
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener repuestos: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener repuestos: ' . $e->getMessage()
            ]);
        }
    }

    public function getRepuestos()
    {
        try {
            $repuestos = $this->repuestoModel->getRepuestos();
            
            $response = [
                'success' => true,
                'data' => $repuestos
            ];
            
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener repuestos: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener repuestos: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteRepuesto()
    {
        // Verificar permisos
        if (!puede_eliminar_repuestos()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tienes permisos para eliminar repuestos'
            ]);
        }

        try {
            $id = $this->request->getPost('id');
            
            if (!$id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID de repuesto no válido'
                ]);
            }

            $usuario = session()->get('username') ?? 'SISTEMA';
            $this->repuestoModel->eliminarRepuesto($id, $usuario);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Repuesto eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar repuesto: ' . $e->getMessage()
            ]);
        }
    }

    public function guardar()
    {
        // Verificar permisos
        if (!puede_crear_repuestos()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No tienes permisos para crear repuestos'
                ]);
            } else {
                return redirect()->to('/repuestos')->with('swal', [
                    'title' => 'Acceso Denegado',
                    'text' => 'No tienes permisos para crear repuestos',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }
        }

        $rules = [
            'nombre' => 'required|min_length[2]|max_length[100]',
            'categoria' => 'max_length[50]',
            'precio' => 'required|integer|greater_than[0]',
            'stock' => 'required|integer|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'categoria' => $this->request->getPost('categoria'),
            'precio' => $this->request->getPost('precio'),
            'stock' => $this->request->getPost('stock'),
            'usuario_creacion' => session()->get('username') ?? 'SISTEMA'
        ];

        try {
            // Debug logs
            log_message('info', '=== INICIO CREAR REPUESTO ===');
            log_message('info', 'Datos recibidos: ' . json_encode($data));
            log_message('info', 'Usuario: ' . (session()->get('username') ?? 'SISTEMA'));
            
            // Verificar unicidad del nombre
            if (!$this->repuestoModel->verificarNombreUnico($data['nombre'])) {
                return redirect()->back()->withInput()->with('swal', [
                    'title' => 'Error',
                    'text' => 'Ya existe un repuesto con ese nombre',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }
            
            // Usar método del modelo
            $repuesto = $this->repuestoModel->insertarRepuesto($data);
            
            log_message('info', 'Repuesto creado exitosamente: ' . $repuesto['id_repuesto']);
            log_message('info', '=== FIN CREAR REPUESTO ===');

            // Verificar si es una petición AJAX
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Repuesto creado exitosamente'
                ]);
            } else {
                return redirect()->to('/repuestos')->with('swal', [
                    'title' => '¡Éxito!',
                    'text' => 'Repuesto creado exitosamente',
                    'icon' => 'success',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al crear repuesto: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Verificar si es una petición AJAX
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()->withInput()->with('swal', [
                    'title' => 'Error',
                    'text' => 'Error al crear repuesto: ' . $e->getMessage(),
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }
        }
    }

    public function actualizar($id = null)
    {
        // Verificar permisos
        if (!puede_editar_repuestos()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No tienes permisos para editar repuestos'
                ]);
            } else {
                return redirect()->to('/repuestos')->with('swal', [
                    'title' => 'Acceso Denegado',
                    'text' => 'No tienes permisos para editar repuestos',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }
        }

        if ($id === null) {
            return redirect()->to('/repuestos')->with('error', 'ID de repuesto no válido');
        }

        $rules = [
            'nombre' => 'required|min_length[2]|max_length[100]',
            'categoria' => 'max_length[50]',
            'precio' => 'required|integer|greater_than[0]',
            'stock' => 'required|integer|greater_than_equal_to[0]',
            'estado' => 'in_list[activo,inactivo]',
            'motivo_stock' => 'permit_empty|max_length[200]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'categoria' => $this->request->getPost('categoria'),
            'precio' => $this->request->getPost('precio'),
            'stock' => $this->request->getPost('stock'),
            'usuario_actualizacion' => session()->get('username') ?? 'SISTEMA'
        ];

        // Manejar el estado (activo/inactivo)
        $estado = $this->request->getPost('estado');
        $deletedAt = ($estado === 'inactivo') ? date('Y-m-d H:i:s') : null;
        
        // Obtener el motivo del cambio de stock
        $motivoStock = $this->request->getPost('motivo_stock');

        try {
            log_message('info', 'Iniciando actualización de repuesto ID: ' . $id);
            log_message('info', 'Datos a actualizar: ' . json_encode($data));
            log_message('info', 'Estado: ' . $estado);
            
            // Verificar unicidad del nombre (excluyendo el actual)
            if (!$this->repuestoModel->verificarNombreUnico($data['nombre'], $id)) {
                return redirect()->back()->withInput()->with('swal', [
                    'title' => 'Error',
                    'text' => 'Ya existe otro repuesto con ese nombre',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }
            
            // Obtener el stock anterior para comparar
            $repuestoAnterior = $this->repuestoModel->getRepuestoById($id);
            $stockAnterior = $repuestoAnterior['stock'] ?? 0;
            
            // Usar método del modelo para actualizar datos básicos
            $this->repuestoModel->actualizarRepuesto($id, $data);

            // Registrar movimiento si el stock cambió y hay motivo
            if ($data['stock'] != $stockAnterior && !empty($motivoStock)) {
                log_message('info', 'Registrando movimiento de stock con motivo');
                
                try {
                $tipoMovimiento = ($data['stock'] > $stockAnterior) ? 'entrada' : 'ajuste';
                
                    $db = \Config\Database::connect();
                    
                    // Verificar si la tabla de movimientos existe
                    $tables = $db->listTables();
                    if (in_array('wp_md_movimientos_stock', $tables)) {
                        // Intentar insertar el movimiento con la estructura correcta
                $db->query("INSERT INTO wp_md_movimientos_stock (
                    id_repuesto, tipo_movimiento, cantidad, 
                            stock_anterior, stock_nuevo, motivo, usuario_movimiento, fecha_movimiento
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())", [
                    $id,
                    $tipoMovimiento,
                    abs($data['stock'] - $stockAnterior),
                    $stockAnterior,
                    $data['stock'],
                    $motivoStock,
                    session()->get('username') ?? 'SISTEMA'
                ]);
                        
                        log_message('info', 'Movimiento de stock registrado exitosamente');
                    } else {
                        log_message('warning', 'Tabla wp_md_movimientos_stock no existe, no se registró el movimiento');
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Error al registrar movimiento de stock: ' . $e->getMessage());
                    // No fallar la actualización por un error en el registro de movimientos
                }
            }

            // Actualizar el estado (deleted_at) directamente
            if ($estado === 'inactivo') {
                log_message('info', 'Actualizando estado a inactivo');
                $db = \Config\Database::connect();
                $db->query("UPDATE wp_md_repuestos SET deleted_at = ? WHERE id_repuesto = ?", [
                    $deletedAt, $id
                ]);
            } else {
                log_message('info', 'Actualizando estado a activo');
                $db = \Config\Database::connect();
                $db->query("UPDATE wp_md_repuestos SET deleted_at = NULL WHERE id_repuesto = ?", [$id]);
            }

            log_message('info', 'Repuesto actualizado exitosamente');
            
            // Verificar si es una petición AJAX
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Repuesto actualizado exitosamente'
                ]);
            } else {
                return redirect()->to('/repuestos')->with('swal', [
                    'title' => '¡Éxito!',
                    'text' => 'Repuesto actualizado exitosamente',
                    'icon' => 'success',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar repuesto: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('swal', [
                'title' => 'Error',
                'text' => 'Error: ' . $e->getMessage(),
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
        }
    }

    public function eliminar($id = null)
    {
        if ($id === null) {
            return redirect()->to('/repuestos')->with('error', 'ID de repuesto no válido');
        }

        try {
            $usuario = session()->get('username') ?? 'SISTEMA';
            $this->repuestoModel->eliminarRepuesto($id, $usuario);

            return redirect()->to('/repuestos')->with('swal', [
                'title' => '¡Eliminado!',
                'text' => 'Repuesto eliminado exitosamente',
                'icon' => 'success',
                'confirmButtonText' => 'Aceptar'
            ]);
        } catch (\Exception $e) {
            return redirect()->to('/repuestos')->with('swal', [
                'title' => 'Error',
                'text' => 'Error al eliminar repuesto: ' . $e->getMessage(),
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
        }
    }

    // Métodos para gestión de stock
    public function alertasStock()
    {
        try {
            $db = \Config\Database::connect();
            $result = $db->query("CALL WP_SP_OBTENER_ALERTAS_STOCK()");
            
            $data = [
                'success' => true,
                'data' => $result->getResultArray()
            ];
            
            // Verificar si estamos en contexto web o CLI
            if ($this->response) {
                return $this->response->setJSON($data);
            } else {
                // En contexto CLI, solo retornar los datos
                return $data;
            }
        } catch (\Exception $e) {
            $error = [
                'success' => false,
                'message' => 'Error al obtener alertas: ' . $e->getMessage()
            ];
            
            if ($this->response) {
                return $this->response->setJSON($error);
            } else {
                return $error;
            }
        }
    }

    public function entradaStock()
    {
        try {
            $idRepuesto = $this->request->getPost('id_repuesto');
            $cantidad = $this->request->getPost('cantidad');
            $motivo = $this->request->getPost('motivo');
            $usuario = session()->get('username') ?? 'SISTEMA';

            $db = \Config\Database::connect();
            $db->query("CALL WP_SP_ENTRADA_STOCK(?, ?, ?, ?)", [
                $idRepuesto, $cantidad, $motivo, $usuario
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Stock actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar stock: ' . $e->getMessage()
            ]);
        }
    }

    public function ajusteStock()
    {
        try {
            $idRepuesto = $this->request->getPost('id_repuesto');
            $nuevoStock = $this->request->getPost('nuevo_stock');
            $motivo = $this->request->getPost('motivo');
            $usuario = session()->get('username') ?? 'SISTEMA';

            $db = \Config\Database::connect();
            $db->query("CALL WP_SP_AJUSTE_STOCK(?, ?, ?, ?)", [
                $idRepuesto, $nuevoStock, $motivo, $usuario
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Stock ajustado correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al ajustar stock: ' . $e->getMessage()
            ]);
        }
    }

        public function movimientosStock()
    {
        try {
            $db = \Config\Database::connect();
            
            // Verificar si la tabla existe
            $tables = $db->listTables();
            if (!in_array('wp_md_movimientos_stock', $tables)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'La tabla de movimientos de stock no existe',
                    'data' => []
                ]);
            }
            
            $result = $db->query("
                SELECT 
                    ms.id_movimiento,
                    ms.id_repuesto,
                    r.nombre as nombre_repuesto,
                    ms.tipo_movimiento,
                    ms.cantidad,
                    ms.stock_anterior,
                    ms.stock_nuevo,
                    ms.motivo,
                    ms.id_orden,
                    ms.usuario_movimiento,
                    ms.fecha_movimiento
                FROM wp_md_movimientos_stock ms
                LEFT JOIN wp_md_repuestos r ON ms.id_repuesto = r.id_repuesto
                ORDER BY ms.fecha_movimiento DESC
            ");

            return $this->response->setJSON([
                'success' => true,
                'data' => $result->getResultArray()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener movimientos de stock: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener movimientos: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function estadisticasStock()
    {
        try {
            $estadisticas = $this->repuestoModel->getEstadisticas();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $estadisticas
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ]);
        }
    }

    public function vistaMovimientos()
    {
        $data = [
            'title' => 'Movimientos de Stock - Taller Rápido y Furioso'
        ];
        return view('repuestos/movimientos', $data);
    }

    private function generarBotonesAccion($idRepuesto)
    {
        $botones = '';
        
        // Botón de editar (solo administradores)
        if (puede_editar_repuestos()) {
            $botones .= '<a href="' . base_url('repuestos/editar/' . $idRepuesto) . '" class="btn btn-sm btn-warning me-1" title="Editar"><i class="fas fa-edit"></i></a>';
        }
        
        // Botón de eliminar (solo administradores)
        if (puede_eliminar_repuestos()) {
            $botones .= '<button onclick="eliminarRepuesto(\'' . $idRepuesto . '\')" class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>';
        }
        
        // Si no hay botones, mostrar mensaje de solo lectura
        if (empty($botones)) {
            $botones = '<span class="badge bg-secondary">Solo lectura</span>';
        }
        
        return $botones;
    }
} 