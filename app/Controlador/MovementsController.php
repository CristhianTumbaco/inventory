<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/InventoryMovements.php';
require_once RUTA . '/Modelos/DAO/InventoryMovementsDAO.php';

class MovementsController
{
    private $inventoryMovementsDAO = null;
    public function __construct()
    {
        $this->inventoryMovementsDAO = new InventoryMovementsDAO();
    }

    public function create($data)
    {
        $movements = new InventoryMovements();
        $movements->setProduct_id($data['product_id']);
        $movements->setSubproduct_id($data['subproduct_id']);
        $movements->setBatch_id($data['batch_id']);
        $movements->setLocation_id($data['location_id']);
        $movements->setPresentation_id($data['presentation_id']);
        $movements->setUser_id($_SESSION['ctid']);
        $movements->setMovement_type($data['movement_type']);
        $movements->setQuantity($data['quantity']);
        $movements->setPackage_count($data['package_count']);
        $movements->setProduct_condition($data['product_condition']);
        $movements->setOrigin_type($data['origin_type']);
        $movements->setOrigin_id($data['origin_id']);
        $movements->setTemperature_received($data['temperature_received']);
        $movements->setExpiration_date($data['expiration_date']);
        $movements->setNotes($data['notes']);
        $id = $this->inventoryMovementsDAO->create($movements);
        return $id;
    }
}
