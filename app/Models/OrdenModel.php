<?php
namespace App\Models;

use CodeIgniter\Model;

class OrdenModel extends Model
{
    protected $table = 'wp_md_orden_trabajo';
    protected $primaryKey = 'id_orden';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id_orden', 'id_cliente', 'id_vehiculo', 'id_estado', 
        'fecha_registro', 'fecha_inicio', 'fecha_fin', 'diagnostico', 
        'observaciones', 'total', 'subtotal', 'iva', 'descuento',
        'usuario_creacion', 'usuario_actualizacion', 'usuario_eliminacion',
        'created_at', 'updated_at', 'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'id_cliente' => 'required',
        'id_vehiculo' => 'required',
        'id_estado' => 'required',
        'diagnostico' => 'permit_empty|max_length[1000]'
    ];

    protected $validationMessages = [
        'id_cliente' => [
            'required' => 'El cliente es obligatorio'
        ],
        'id_vehiculo' => [
            'required' => 'El vehículo es obligatorio'
        ],
        'id_estado' => [
            'required' => 'El estado es obligatorio'
        ]
    ];

    // =========================
    // MÉTODOS INTEGRADOS CON PROCEDIMIENTOS ALMACENADOS
    // =========================

    /**
     * Crear orden completa usando procedimiento almacenado
     */
    public function crearOrdenCompleta($datos)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->transStart();

            // Generar correo usando la función
            $sqlCorreo = "SELECT WP_FN_GENERAR_CORREO(?, ?) AS correo_generado";
            $correoResult = $db->query($sqlCorreo, [
                $datos['nombres_cliente'],
                $datos['apellido_paterno']
            ])->getRow();
            $correo_generado = $correoResult ? $correoResult->correo_generado : '';

            // Preparar datos para el procedimiento
            $repuestos_json = json_encode($datos['repuestos'] ?? []);
            
            $sql = "CALL WP_SP_CREAR_ORDEN_COMPLETA(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @id_orden, @id_cliente, @id_vehiculo)";
            
            $db->query($sql, [
                $datos['run_cliente'],
                $datos['nombres_cliente'],
                $datos['apellido_paterno'],
                $datos['apellido_materno'],
                $datos['telefono_cliente'],
                $correo_generado,
                $datos['direccion_cliente'],
                $datos['patente_vehiculo'],
                $datos['marca_vehiculo'],
                $datos['modelo_vehiculo'],
                $datos['anio_vehiculo'],
                $datos['id_tipo_vehiculo'],
                $datos['diagnostico'],
                $datos['observaciones'],
                $repuestos_json,
                $datos['usuario']
            ]);
            
            // Obtener los IDs generados
            $result = $db->query("SELECT @id_orden as id_orden, @id_cliente as id_cliente, @id_vehiculo as id_vehiculo")->getRow();
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                $error = $db->error();
                throw new \Exception('Error al crear la orden completa: ' . ($error['message'] ?? 'Error desconocido'));
            }
            
            return [
                'success' => true,
                'id_orden' => $result->id_orden,
                'id_cliente' => $result->id_cliente,
                'id_vehiculo' => $result->id_vehiculo
            ];
            
        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar orden usando procedimiento almacenado
     */
    public function actualizarOrden($id_orden, $datos)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->transStart();
            
            $repuestos_json = json_encode($datos['nuevos_repuestos'] ?? []);
            
            $sql = "CALL WP_SP_ACTUALIZAR_ORDEN_TRABAJO(?, ?, ?, ?, ?, ?)";
            
            $db->query($sql, [
                $id_orden,
                $datos['id_estado'],
                $datos['diagnostico'],
                $datos['observaciones'],
                $repuestos_json,
                $datos['usuario']
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Error al actualizar la orden');
            }
            
            return ['success' => true];
            
        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar orden usando procedimiento almacenado
     */
    public function eliminarOrden($id_orden, $usuario)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->transStart();
            
            $sql = "CALL WP_SP_ELIMINAR_ORDEN_TRABAJO(?, ?)";
            
            $db->query($sql, [$id_orden, $usuario]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Error al eliminar la orden');
            }
            
            return ['success' => true];
            
        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Recalcula el subtotal, IVA y total de la orden sumando todos los repuestos asociados
     */
    public function recalcularTotalesOrden($id_orden)
    {
        $db = \Config\Database::connect();
        try {
            // Llamar a la función WP_FN_CALCULAR_TOTAL_ORDEN para obtener el subtotal
            $sql = "SELECT WP_FN_CALCULAR_TOTAL_ORDEN(?) AS subtotal";
            $row = $db->query($sql, [$id_orden])->getRow();
            $subtotal = (int)round($row->subtotal ?? 0);
            $iva = (int)round($subtotal * 0.19);
            // Guarda el total con IVA en 'total'
            $db->table('wp_md_orden_trabajo')->where('id_orden', $id_orden)->update([
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $subtotal + $iva // <--- total con IVA
            ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error al recalcular totales de la orden: ' . $e->getMessage());
            return false;
        }
    }

    // =========================
    // MÉTODOS DE CONSULTA EXISTENTES
    // =========================

    // Obtener órdenes con información de cliente, vehículo y estado
    public function getOrdenesCompletas()
    {
        $db = \Config\Database::connect();
        
        $sql = "
            SELECT 
                ot.id_orden,
                ot.id_cliente,
                ot.id_vehiculo,
                ot.id_estado,
                ot.fecha_registro,
                ot.fecha_inicio,
                ot.fecha_fin,
                ot.diagnostico,
                ot.observaciones,
                ot.total,
                ot.subtotal,
                ot.iva,
                ot.descuento,
                ot.usuario_creacion,
                ot.usuario_actualizacion,
                ot.created_at,
                ot.updated_at,
                c.nombres as cliente_nombres,
                c.apellido_paterno as cliente_apellido,
                c.apellido_materno as cliente_apellido_materno,
                c.telefono as cliente_telefono,
                c.correo as cliente_correo,
                v.patente as vehiculo_patente,
                v.marca as vehiculo_marca,
                v.modelo as vehiculo_modelo,
                v.anio as vehiculo_anio,
                eo.nombre_estado
            FROM wp_md_orden_trabajo ot
            INNER JOIN wp_md_clientes c ON c.id_cliente = ot.id_cliente
            INNER JOIN wp_md_vehiculos v ON v.id_vehiculo = ot.id_vehiculo
            INNER JOIN wp_md_estado_orden eo ON eo.id_estado = ot.id_estado
            WHERE ot.deleted_at IS NULL
            GROUP BY ot.id_orden
            ORDER BY ot.fecha_registro DESC
        ";
        
        return $db->query($sql)->getResultArray();
    }

    // Obtener una orden específica con toda su información
    public function getOrdenCompleta($id_orden)
    {
        $db = \Config\Database::connect();
        
        $sql = "
            SELECT 
                ot.*,
                c.nombres as cliente_nombres,
                c.apellido_paterno as cliente_apellido,
                c.apellido_materno as cliente_apellido_materno,
                c.telefono as cliente_telefono,
                c.correo as cliente_correo,
                c.direccion as cliente_direccion,
                v.patente as vehiculo_patente,
                v.marca as vehiculo_marca,
                v.modelo as vehiculo_modelo,
                v.anio as vehiculo_anio,
                tv.nombre_tipo as tipo_vehiculo,
                eo.nombre_estado
            FROM wp_md_orden_trabajo ot
            INNER JOIN wp_md_clientes c ON c.id_cliente = ot.id_cliente
            INNER JOIN wp_md_vehiculos v ON v.id_vehiculo = ot.id_vehiculo
            INNER JOIN wp_md_tipo_vehiculo tv ON tv.id_tipo = v.id_tipo
            INNER JOIN wp_md_estado_orden eo ON eo.id_estado = ot.id_estado
            WHERE ot.id_orden = ? AND ot.deleted_at IS NULL
        ";
        
        return $db->query($sql, [$id_orden])->getRowArray();
    }

    // Obtener repuestos de una orden
    public function getRepuestosOrden($id_orden)
    {
        // Usar el nuevo modelo de repuestos de órdenes
        $ordenRepuestoModel = new \App\Models\OrdenRepuestoModel();
        return $ordenRepuestoModel->getRepuestosOrden($id_orden);
    }

    // Obtener estados de orden
    public function getEstadosOrden()
    {
        $db = \Config\Database::connect();
        return $db->table('wp_md_estado_orden')
            ->orderBy('id_estado')
            ->get()
            ->getResultArray();
    }

    // Obtener tipos de vehículo
    public function getTiposVehiculo()
    {
        $db = \Config\Database::connect();
        return $db->table('wp_md_tipo_vehiculo')
            ->orderBy('nombre_tipo')
            ->get()
            ->getResultArray();
    }

    // Obtener repuestos disponibles
    public function getRepuestosDisponibles()
    {
        $db = \Config\Database::connect();
        return $db->table('wp_md_repuestos')
            ->select('id_repuesto, nombre, precio, stock')
            ->where('stock >', 0)
            ->orderBy('nombre')
            ->get()
            ->getResultArray();
    }
}