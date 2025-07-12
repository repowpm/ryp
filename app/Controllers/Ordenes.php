<?php
namespace App\Controllers;

use App\Models\OrdenModel;
use App\Models\OrdenRepuestoModel;
use App\Models\ClienteModel;
use App\Models\VehiculoModel;
use App\Models\RepuestoModel;
use App\Models\MarcaModeloModel;
use App\Models\TipoVehiculoModel;
use CodeIgniter\Controller;

class Ordenes extends BaseController
{
    protected $ordenModel;
    protected $ordenRepuestoModel;
    protected $clienteModel;
    protected $vehiculoModel;
    protected $repuestoModel;
    protected $marcaModeloModel;
    protected $tipoVehiculoModel;

    public function __construct()
    {
        $this->ordenModel = new OrdenModel();
        $this->ordenRepuestoModel = new OrdenRepuestoModel();
        $this->clienteModel = new ClienteModel();
        $this->vehiculoModel = new VehiculoModel();
        $this->repuestoModel = new RepuestoModel();
        $this->marcaModeloModel = new MarcaModeloModel();
        $this->tipoVehiculoModel = new TipoVehiculoModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Gestión de Órdenes de Trabajo'
        ];
        
        return view('ordenes/index', $data);
    }

    public function listar()
    {
        $ordenes = $this->ordenModel->getOrdenesCompletas();
        $data = [];
        foreach ($ordenes as $orden) {
            $data[] = [
                $orden['id_orden'],
                $orden['cliente_nombres'] . ' ' . $orden['cliente_apellido'] . ' ' . ($orden['cliente_apellido_materno'] ?? ''),
                $orden['vehiculo_patente'] . '<br><small class="text-muted">' . $orden['vehiculo_marca'] . ' ' . $orden['vehiculo_modelo'] . ' (' . $orden['vehiculo_anio'] . ')</small>',
                '<span class="badge badge-' . $this->getEstadoBadgeClass($orden['nombre_estado']) . '">' . $orden['nombre_estado'] . '</span>',
                date('d/m/Y H:i', strtotime($orden['fecha_registro'])),
                '$' . number_format($orden['total'] + $orden['iva'], 0, ',', '.'),
                $this->generarBotonesAccion($orden['id_orden'])
            ];
        }
        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function crear()
    {
        $data = [
            'title' => 'Crear Nueva Orden de Trabajo',
            'repuestos' => $this->ordenModel->getRepuestosDisponibles(),
            'marcas' => $this->marcaModeloModel->getMarcas(),
            'tiposVehiculo' => $this->ordenModel->getTiposVehiculo(),
            'estados' => $this->ordenModel->getEstadosOrden()
        ];
        
        return view('ordenes/crear', $data);
    }

    public function guardar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/ordenes');
        }

        $data = $this->request->getPost();
        
        // Validar datos básicos
        $rules = [
            'run_cliente' => 'required|min_length[8]|max_length[12]',
            'nombres_cliente' => 'required|min_length[2]|max_length[100]',
            'apellido_paterno' => 'required|min_length[2]|max_length[100]',
            'telefono_cliente' => 'required|min_length[8]|max_length[20]',
            'patente_vehiculo' => 'required|min_length[4]|max_length[10]',
            'marca_vehiculo' => 'required|min_length[2]|max_length[50]',
            'modelo_vehiculo' => 'required|min_length[2]|max_length[50]',
            'anio_vehiculo' => 'required|integer|greater_than[1900]|less_than[2030]',
            'id_tipo_vehiculo' => 'required',
            'diagnostico' => 'permit_empty|max_length[1000]',
            'observaciones' => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $usuario = session()->get('username') ?? 'SISTEMA';
            
            // Preparar datos para el procedimiento almacenado
            $datosOrden = [
                'run_cliente' => $data['run_cliente'],
                'nombres_cliente' => $data['nombres_cliente'],
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'] ?? '',
                'telefono_cliente' => $data['telefono_cliente'],
                'correo_cliente' => '', // Se generará automáticamente con WP_FN_GENERAR_CORREO
                'direccion_cliente' => $data['direccion_cliente'] ?? '',
                'patente_vehiculo' => strtoupper($data['patente_vehiculo']),
                'marca_vehiculo' => $data['marca_vehiculo'],
                'modelo_vehiculo' => $data['modelo_vehiculo'],
                'anio_vehiculo' => $data['anio_vehiculo'],
                'id_tipo_vehiculo' => $data['id_tipo_vehiculo'],
                'diagnostico' => $data['diagnostico'] ?? '',
                'observaciones' => $data['observaciones'] ?? '',
                'usuario' => $usuario
            ];

            // Preparar repuestos
            $repuestos = [];
            if (!empty($data['repuestos'])) {
                foreach ($data['repuestos'] as $repuesto) {
                    if (!empty($repuesto['id_repuesto']) && !empty($repuesto['cantidad'])) {
                        $repuestos[] = [
                            'id_repuesto' => $repuesto['id_repuesto'],
                            'cantidad' => (int)$repuesto['cantidad'],
                            'precio_unitario' => (float)$repuesto['precio_unitario'] ?? 0
                        ];
                    }
                }
            }
            $datosOrden['repuestos'] = $repuestos;

            // Usar el método del modelo que llama al procedimiento almacenado
            $resultado = $this->ordenModel->crearOrdenCompleta($datosOrden);
            
            if ($resultado['success']) {
            return $this->response->setJSON([
                'success' => true,
                    'message' => 'Orden de trabajo creada exitosamente',
                    'id_orden' => $resultado['id_orden'],
                    'id_cliente' => $resultado['id_cliente'],
                    'id_vehiculo' => $resultado['id_vehiculo']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al crear la orden: ' . $resultado['error']
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al crear orden: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al crear la orden de trabajo: ' . $e->getMessage()
            ]);
        }
    }

    public function editar($id = null)
    {
        if (!$id) {
            return redirect()->to('/ordenes');
        }

        $orden = $this->ordenModel->getOrdenCompleta($id);
        if (!$orden) {
            return redirect()->to('/ordenes')->with('error', 'Orden no encontrada');
        }

        $data = [
            'title' => 'Editar Orden de Trabajo',
            'orden' => $orden,
            'repuestos' => $this->ordenModel->getRepuestosDisponibles(),
            'marcas' => $this->marcaModeloModel->getMarcas(),
            'tiposVehiculo' => $this->ordenModel->getTiposVehiculo(),
            'estados' => $this->ordenModel->getEstadosOrden(),
            'repuestosOrden' => $this->ordenModel->getRepuestosOrden($id)
        ];
        
        return view('ordenes/editar', $data);
    }

    public function actualizar($id = null)
    {
        if (!$this->request->isAJAX() || !$id) {
            return redirect()->to('/ordenes');
        }

        $data = $this->request->getPost();
        $rules = [
            'id_estado' => 'required',
            'diagnostico' => 'permit_empty|max_length[1000]',
            'observaciones' => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $usuario = session()->get('username') ?? 'SISTEMA';
            // Procesar nuevos repuestos si existen
            $nuevosRepuestos = [];
            if (!empty($data['nuevos_repuestos'])) {
                foreach ($data['nuevos_repuestos'] as $repuesto) {
                    if (!empty($repuesto['id_repuesto']) && !empty($repuesto['cantidad'])) {
                        $nuevosRepuestos[] = [
                            'id_repuesto' => $repuesto['id_repuesto'],
                            'cantidad' => (int)$repuesto['cantidad'],
                            'precio_unitario' => (float)$repuesto['precio_unitario'] ?? 0
                        ];
                    }
                }
            }
            
            // Log para debugging
            log_message('info', 'Nuevos repuestos a agregar: ' . json_encode($nuevosRepuestos));
            $datosActualizacion = [
                'id_estado' => $data['id_estado'],
                'diagnostico' => $data['diagnostico'] ?? '',
                'observaciones' => $data['observaciones'] ?? '',
                'nuevos_repuestos' => $nuevosRepuestos,
                'usuario' => $usuario
            ];
            // Usar el método del modelo que llama al procedimiento almacenado
            $resultado = $this->ordenModel->actualizarOrden($id, $datosActualizacion);
            if ($resultado['success']) {
                // Recalcular el total de la orden (incluyendo IVA)
                log_message('info', 'Recalculando totales para orden: ' . $id);
                $this->ordenModel->recalcularTotalesOrden($id);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Orden de trabajo actualizada exitosamente',
                    'redirect' => base_url('ordenes')
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al actualizar la orden: ' . $resultado['error']
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar orden: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar la orden de trabajo: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminar($id = null)
    {
        if (!$this->request->isAJAX() || !$id) {
            return redirect()->to('/ordenes');
        }

        try {
            $usuario = session()->get('username') ?? 'SISTEMA';
            
            // Usar el método del modelo que llama al procedimiento almacenado
            $resultado = $this->ordenModel->eliminarOrden($id, $usuario);
            
            if ($resultado['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Orden de trabajo eliminada exitosamente'
            ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al eliminar la orden: ' . $resultado['error']
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar orden: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar la orden de trabajo: ' . $e->getMessage()
            ]);
        }
    }

    public function ver($id = null)
    {
        if (!$id) {
            return redirect()->to('/ordenes');
        }

        $orden = $this->ordenModel->getOrdenCompleta($id);
        if (!$orden) {
            return redirect()->to('/ordenes')->with('error', 'Orden no encontrada');
        }

        $data = [
            'title' => 'Ver Orden de Trabajo',
            'orden' => $orden,
            'repuestosOrden' => $this->ordenModel->getRepuestosOrden($id)
        ];
        
        return view('ordenes/ver', $data);
    }

    public function imprimir($id = null)
    {
        if (!$id) {
            return redirect()->to('/ordenes');
        }

        $orden = $this->ordenModel->getOrdenCompleta($id);
        if (!$orden) {
            return redirect()->to('/ordenes')->with('error', 'Orden no encontrada');
        }

        $data = [
            'title' => 'Imprimir Orden de Trabajo',
            'orden' => $orden,
            'repuestosOrden' => $this->ordenModel->getRepuestosOrden($id)
        ];
        
        return view('ordenes/imprimir', $data);
    }

    // =========================
    // MÉTODOS AJAX PARA FORMULARIOS
    // =========================

    public function getMarcas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }

        try {
            $marcas = $this->marcaModeloModel->getMarcas();
            return $this->response->setJSON([
                'success' => true,
                'data' => $marcas
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener marcas: ' . $e->getMessage()
            ]);
        }
    }

    public function getModelos($marca = null)
    {
        if (!$this->request->isAJAX() || !$marca) {
            return $this->response->setJSON(['success' => false]);
        }

        try {
            $modelos = $this->marcaModeloModel->getModelosByMarca($marca);
            return $this->response->setJSON([
                'success' => true,
                'data' => $modelos
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener modelos: ' . $e->getMessage()
            ]);
        }
    }

    public function getRepuestos()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }

        try {
            $repuestos = $this->ordenModel->getRepuestosDisponibles();
            return $this->response->setJSON([
                'success' => true,
                'data' => $repuestos
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener repuestos: ' . $e->getMessage()
            ]);
        }
    }

    public function verificarCliente($run = null)
    {
        if (!$this->request->isAJAX() || !$run) {
            return $this->response->setJSON(['success' => false]);
        }

        try {
            $cliente = $this->clienteModel->getClientePorRun($run);
            return $this->response->setJSON([
                'success' => true,
                'existe' => !empty($cliente),
                'cliente' => $cliente
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al verificar cliente: ' . $e->getMessage()
            ]);
        }
    }

    public function verificarVehiculo($patente = null)
    {
        if (!$this->request->isAJAX() || !$patente) {
            return $this->response->setJSON(['success' => false]);
        }

        try {
            $vehiculo = $this->vehiculoModel->getVehiculoPorPatente($patente);
            return $this->response->setJSON([
                'success' => true,
                'existe' => !empty($vehiculo),
                'vehiculo' => $vehiculo
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al verificar vehículo: ' . $e->getMessage()
            ]);
        }
    }

    // =========================
    // MÉTODOS PRIVADOS
    // =========================

    private function getEstadoBadgeClass($estado)
    {
        $clases = [
            'PENDIENTE' => 'warning',
            'EN PROCESO' => 'info',
            'COMPLETADA' => 'success',
            'CANCELADA' => 'danger',
            'FACTURADA' => 'primary'
        ];
        
        return $clases[$estado] ?? 'secondary';
    }

    private function generarBotonesAccion($id_orden)
    {
        $botones = '<div class="btn-group" role="group">';
        $botones .= '<a href="' . base_url('ordenes/ver/' . $id_orden) . '" class="btn btn-sm btn-info" title="Ver"><i class="fas fa-eye"></i></a>';
        $botones .= '<a href="' . base_url('ordenes/editar/' . $id_orden) . '" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>';
        $botones .= '<a href="' . base_url('ordenes/imprimir/' . $id_orden) . '" class="btn btn-sm btn-secondary" title="Imprimir" target="_blank"><i class="fas fa-print"></i></a>';
        $botones .= '<button type="button" class="btn btn-sm btn-danger btn-eliminar" data-id="' . $id_orden . '" title="Eliminar"><i class="fas fa-trash"></i></button>';
        $botones .= '</div>';
        
        return $botones;
    }

    // =========================
    // MÉTODOS PARA MANEJO DE REPUESTOS DE ÓRDENES
    // =========================

    /**
     * Agregar repuesto a una orden
     */
    public function agregarRepuesto()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud inválida']);
        }

        $data = $this->request->getPost();
        
        $rules = [
            'id_orden' => 'required',
            'id_repuesto' => 'required',
            'cantidad' => 'required|integer|greater_than[0]',
            'precio_unitario' => 'required|integer|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $usuario = session()->get('username') ?? 'SISTEMA';
            
            $datos = [
                'id_orden' => $data['id_orden'],
                'id_repuesto' => $data['id_repuesto'],
                'cantidad' => (int)$data['cantidad'],
                'precio_unitario' => (int)$data['precio_unitario'],
                'descuento' => (int)($data['descuento'] ?? 0),
                'usuario' => $usuario
            ];

            $resultado = $this->ordenRepuestoModel->agregarRepuestoOrden($datos);
            
            return $this->response->setJSON($resultado);

        } catch (\Exception $e) {
            log_message('error', 'Error al agregar repuesto: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al agregar repuesto: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Actualizar repuesto de una orden
     */
    public function actualizarRepuesto()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud inválida']);
        }

        $data = $this->request->getPost();
        
        $rules = [
            'id_orden_repuesto' => 'required',
            'cantidad' => 'required|integer|greater_than[0]',
            'precio_unitario' => 'required|integer|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $usuario = session()->get('username') ?? 'SISTEMA';
            
            $datos = [
                'cantidad' => (int)$data['cantidad'],
                'precio_unitario' => (int)$data['precio_unitario'],
                'descuento' => (int)($data['descuento'] ?? 0),
                'usuario' => $usuario
            ];

            $resultado = $this->ordenRepuestoModel->actualizarRepuestoOrden($data['id_orden_repuesto'], $datos);
            
            return $this->response->setJSON($resultado);

        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar repuesto: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar repuesto: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar repuesto de una orden
     */
    public function eliminarRepuesto()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud inválida']);
        }

        $data = $this->request->getPost();
        
        if (empty($data['id_orden_repuesto'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de repuesto de orden requerido'
            ]);
        }

        try {
            $usuario = session()->get('username') ?? 'SISTEMA';
            
            $resultado = $this->ordenRepuestoModel->eliminarRepuestoOrden($data['id_orden_repuesto'], $usuario);
            
            return $this->response->setJSON($resultado);

        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar repuesto: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar repuesto: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener repuestos de una orden
     */
    public function getRepuestosOrden($id_orden = null)
    {
        if (!$id_orden) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID de orden requerido']);
        }

        try {
            $repuestos = $this->ordenRepuestoModel->getRepuestosOrden($id_orden);
            $total = $this->ordenRepuestoModel->getTotalRepuestosOrden($id_orden);
            
            return $this->response->setJSON([
                'success' => true,
                'repuestos' => $repuestos,
                'total' => $total
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener repuestos: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener repuestos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas de repuestos
     */
    public function getEstadisticasRepuestos()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud inválida']);
        }

        $data = $this->request->getPost();
        
        try {
            $fecha_inicio = $data['fecha_inicio'] ?? date('Y-m-01');
            $fecha_fin = $data['fecha_fin'] ?? date('Y-m-t');
            
            $estadisticas = $this->ordenRepuestoModel->getEstadisticasRepuestos($fecha_inicio, $fecha_fin);
            $masUtilizados = $this->ordenRepuestoModel->getRepuestosMasUtilizados(10);
            $stockBajo = $this->ordenRepuestoModel->getRepuestosStockBajo(10);
            
            return $this->response->setJSON([
                'success' => true,
                'estadisticas' => $estadisticas,
                'mas_utilizados' => $masUtilizados,
                'stock_bajo' => $stockBajo
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener estadísticas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ]);
        }
    }
} 