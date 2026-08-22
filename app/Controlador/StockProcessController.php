<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/InventoryStock.php';
require_once RUTA . '/Modelos/DAO/InventoryStockDAO.php';
require_once RUTA . '/Modelos/DTO/InventoryPackages.php';
require_once RUTA . '/Modelos/DAO/InventoryPackagesDAO.php';
require_once RUTA . '/Modelos/DTO/PurchaseDetails.php';
require_once RUTA . '/Modelos/DAO/PurchaseDetailsDAO.php';
require_once RUTA . '/Modelos/DTO/Batches.php';
require_once RUTA . '/Modelos/DAO/BatchesDAO.php';
require_once RUTA . '/Modelos/DTO/Production.php';
require_once RUTA . '/Modelos/DAO/ProductionDAO.php';
require_once RUTA . '/Controlador/MovementsController.php';


class StockProcessController
{
    private $inventoryStockDAO = null;
    private $inventoryPackagesDAO = null;
    private $purchaseDetailsDAO = null;
    private $batchesDAO = null;
    private $productionDAO = null;
    private $movementsController = null;

    public function __construct()
    {
        $this->inventoryStockDAO = new InventoryStockDAO();
        $this->inventoryPackagesDAO = new InventoryPackagesDAO();
        $this->purchaseDetailsDAO = new PurchaseDetailsDAO();
        $this->batchesDAO = new BatchesDAO();
        $this->productionDAO = new ProductionDAO();
        $this->movementsController = new MovementsController();
    }
    public function reception($data)
    {
        $originId = $data['detail_id'];
        $purchaseDetail = $this->purchaseDetailsDAO->consultId($originId);
        if (sizeof($purchaseDetail) > 0) {
            foreach ($purchaseDetail as $purchase) {
                $units_per_package = $purchase->getUnits_per_package();
                if (isset($data['locations']) && is_array($data['locations']) && !empty($data['locations']) && $data['process'] == 2) {
                    $locations = $data['locations'];
                    foreach ($locations as $location) {
                        $this->movementsController->create(array(
                            "product_id" => $purchase->getProduct_id(),
                            "subproduct_id" => $purchase->getSubproduct_id(),
                            "batch_id" => $purchase->getBatch_id(),
                            "location_id" => $location['location_id'],
                            "presentation_id" => $purchase->getPresentation_id(),
                            "movement_type" => 'entrada',
                            "quantity" => $location['quantity'],
                            "package_count" => $location['package_count'],
                            "product_condition" => $data['product_condition'],
                            "origin_type" => 'purchase',
                            "origin_id" => $originId,
                            "temperature_received" => $data['entry_temperature'],
                            "expiration_date" => $data['expiration_date'],
                            "notes" => null
                        ));
                        $this->packageStock(array(
                            "batch_id" => $data['id'],
                            "product_id" => $purchase->getProduct_id(),
                            "subproduct_id" => $purchase->getSubproduct_id(),
                            "location_id" => $location['location_id'],
                            "presentation_id" => $purchase->getPresentation_id(),
                            "package_weight_lb" => $data['package_weight_lb'],
                            "package_count" => $location['package_count'],
                            "quantity" => $location['quantity'],
                            "units_per_package" => $units_per_package
                        ));
                    }
                } else {
                    $this->movementsController->create(array(
                        "product_id" => $purchase->getProduct_id(),
                        "subproduct_id" => $purchase->getSubproduct_id(),
                        "batch_id" => $purchase->getBatch_id(),
                        "location_id" => $data['location_id'],
                        "presentation_id" => $purchase->getPresentation_id(),
                        "movement_type" => 'entrada',
                        "quantity" => $data['quantity'],
                        "package_count" => $data['package_count'],
                        "product_condition" => $data['product_condition'],
                        "origin_type" => 'purchase',
                        "origin_id" => $originId,
                        "temperature_received" => $data['entry_temperature'],
                        "expiration_date" => $data['expiration_date'],
                        "notes" => null
                    ));
                    $this->packageStock(array(
                        "batch_id" => $data['id'],
                        "product_id" => $purchase->getProduct_id(),
                        "subproduct_id" => $purchase->getSubproduct_id(),
                        "location_id" => $data['location_id'],
                        "presentation_id" => $purchase->getPresentation_id(),
                        "package_weight_lb" => $data['package_weight_lb'],
                        "package_count" => $data['package_count'],
                        "quantity" => $data['quantity'],
                        "units_per_package" => $units_per_package
                    ));
                }
            }
        }
    }
    public function movePackage(int $packageId, int $oldLocationId, int $newLocationId, float $packagesToMove, string $condition, $temperature, $expiration_date)
    {
        $package = $this->inventoryPackagesDAO->consultId($packageId);
        if (empty($package)) {
            throw new Exception('Paquete no encontrado');
        }
        $package = $package[0];
        if ($oldLocationId == $newLocationId) {
            throw new Exception('La ubicación es la misma');
        }
        $availablePackages = $package->getPackage_count();
        if ($packagesToMove <= 0) {
            $packagesToMove = $availablePackages;
        }
        if ($packagesToMove > $availablePackages) {
            throw new Exception('No existen suficientes empaques');
        }
        $weightToMove = $package->getPackage_weight_lb() * $packagesToMove;

        $this->movePackageInternal(
            $package,
            $newLocationId,
            $packagesToMove,
            $weightToMove
        );

        $this->syncStock([
            'product_id' => $package->getProduct_id(),
            'subproduct_id' => $package->getSubproduct_id(),
            'location_id' => $oldLocationId,
            'presentation_id' => $package->getPresentation_id()
        ]);

        $this->syncStock([
            'product_id' => $package->getProduct_id(),
            'subproduct_id' => $package->getSubproduct_id(),
            'location_id' => $newLocationId,
            'presentation_id' => $package->getPresentation_id()
        ]);

        $this->movementsController->create([
            'product_id' => $package->getProduct_id(),
            'subproduct_id' => $package->getSubproduct_id(),
            'batch_id' => $package->getBatch_id(),
            'location_id' => $newLocationId,
            'presentation_id' => $package->getPresentation_id(),
            'movement_type' => 'traslado',
            'quantity' => $weightToMove,
            'package_count' => $packagesToMove,
            'product_condition' => $condition,
            'origin_type' => 'stock',
            'origin_id' => null,
            'temperature_received' => $temperature,
            'expiration_date' => $expiration_date,
            'notes' => null

        ]);
    }
    public function packageStock($data)
    {
        $packageWeight = !empty($data['package_weight_lb'])
            ? $data['package_weight_lb']
            : ($data['quantity'] / $data['package_count']);

        $existingPackage = $this->inventoryPackagesDAO->validatedId([
            $data['batch_id'],
            $data['product_id'],
            $data['subproduct_id'],
            $data['location_id'],
            $data['presentation_id'],
            $packageWeight
        ]);
        if ($existingPackage != null) {
            $package = new InventoryPackages();
            $package->setId($existingPackage[0]['id']);
            $package->setPackage_count($data['package_count']);
            $package->setQuantity_lb($data['quantity']);
            $this->inventoryPackagesDAO->increaseStock($package);
        } else {
            $package = new InventoryPackages();
            $package->setBatch_id($data['batch_id']);
            $package->setProduct_id($data['product_id']);
            $package->setSubproduct_id($data['subproduct_id']);
            $package->setLocation_id($data['location_id']);
            $package->setPresentation_id($data['presentation_id']);
            $package->setPackage_weight_lb($packageWeight);
            $package->setPackage_count($data['package_count']);
            $package->setQuantity_lb($data['quantity']);
            $package->setUnits_per_package($data['units_per_package']);
            $package->setStatus('available');
            $this->inventoryPackagesDAO->create($package);
        }

        $this->syncStock([
            'product_id' => $data['product_id'],
            'subproduct_id' => $data['subproduct_id'],
            'location_id' => $data['location_id'],
            'presentation_id' => $data['presentation_id']
        ]);
    }
    public function syncStock($data)
    {
        $result = $this->inventoryPackagesDAO->syncStock([
            $data['product_id'],
            $data['subproduct_id'],
            $data['location_id'],
            $data['presentation_id']
        ]);
        $quantity = 0;
        $packageCount = 0;
        if (!empty($result)) {
            $quantity = $result[0]['quantity'];
            $packageCount = $result[0]['package_count'];
        }
        $existingStock = $this->inventoryStockDAO->validatedId([
            $data['product_id'],
            $data['subproduct_id'],
            $data['location_id'],
            $data['presentation_id']
        ]);
        $stock = new InventoryStock();
        $stock->setProduct_id($data['product_id']);
        $stock->setSubproduct_id($data['subproduct_id']);
        $stock->setLocation_id($data['location_id']);
        $stock->setPresentation_id($data['presentation_id']);
        $stock->setQuantity($quantity);
        $stock->setPackage_count($packageCount);
        if ($existingStock != null) {
            if (($quantity == 0  || $quantity == null) && ($packageCount == 0 || $packageCount == null)) {
                $this->inventoryStockDAO->delete($existingStock[0]['id']);
            } else {
                $stock->setId($existingStock[0]['id']);
                $this->inventoryStockDAO->syncStock($stock);
            }
        } else {
            $this->inventoryStockDAO->create($stock);
        }
    }
    private function movePackageInternal(
        InventoryPackages $sourcePackage,
        int $newLocationId,
        float $packagesToMove,
        float $weightToMove
    ) {
        $destinationPackage = $this->inventoryPackagesDAO->validatedIdUnit([
            $sourcePackage->getBatch_id(),
            $sourcePackage->getProduct_id(),
            $sourcePackage->getSubproduct_id(),
            $newLocationId,
            $sourcePackage->getPresentation_id(),
            $sourcePackage->getPackage_weight_lb(),
            $sourcePackage->getUnits_per_package()
        ]);
        if ($destinationPackage != null) {
            $package = new InventoryPackages();
            $package->setId($destinationPackage[0]['id']);
            $package->setPackage_count($packagesToMove);
            $package->setQuantity_lb($weightToMove);
            $this->inventoryPackagesDAO->increaseStock($package);
        } else {
            $newPackage = new InventoryPackages();
            $newPackage->setBatch_id($sourcePackage->getBatch_id());
            $newPackage->setProduct_id($sourcePackage->getProduct_id());
            $newPackage->setSubproduct_id($sourcePackage->getSubproduct_id());
            $newPackage->setLocation_id($newLocationId);
            $newPackage->setPresentation_id($sourcePackage->getPresentation_id());
            $newPackage->setPackage_weight_lb($sourcePackage->getPackage_weight_lb());
            $newPackage->setPackage_count($packagesToMove);
            $newPackage->setQuantity_lb($weightToMove);
            $newPackage->setUnits_per_package($sourcePackage->getUnits_per_package());
            $newPackage->setStatus($sourcePackage->getStatus());
            $this->inventoryPackagesDAO->create($newPackage);
        }
        $remainingPackages = $sourcePackage->getPackage_count() - $packagesToMove;
        $remainingWeight = $sourcePackage->getQuantity_lb() - $weightToMove;
        if ($remainingPackages <= 0) {
            $this->inventoryPackagesDAO->delete($sourcePackage->getId());
        } else {
            $package = new InventoryPackages();
            $package->setId($sourcePackage->getId());
            $package->setPackage_count($remainingPackages);
            $package->setQuantity_lb($remainingWeight);
            $this->inventoryPackagesDAO->decreaseStock($package);
        }
    }
    public function decreaseProcess($decrease, $movement_type, $product_condition, $origin_type, $origin_id)
    {
        foreach ($decrease as $data) {
            $id = $data['id'];
            $packageResult = $this->inventoryPackagesDAO->consultId($id);
            if (empty($packageResult) || !is_array($packageResult)) {
                continue;
            }
            $package = $packageResult[0];
            $packageCount = (float)($data['package_count'] ?? 0);
            $notes = $data['notes'] ?? '';
            $weights = $data['weights'] ?? [];
            $totalAffected = $packageCount;
            foreach ($weights as $weight) {
                $totalAffected += 1;
            }
            if ($totalAffected > $package->getPackage_count()) {
                throw new Exception(
                    'La cantidad de empaques afectados supera la existencia disponible.'
                );
            }
            if ($packageCount > 0) {
                $this->processTotalLoss($package, $packageCount, $notes, $movement_type, $product_condition, $origin_type, $origin_id);
            }
            if (!empty($weights) && is_array($weights)) {
                $this->processPartialLoss($id, $weights, $notes, $movement_type, $product_condition, $origin_type, $origin_id);
            }
            $this->syncStock([
                'product_id' => $package->getProduct_id(),
                'subproduct_id' => $package->getSubproduct_id(),
                'location_id' => $package->getLocation_id(),
                'presentation_id' => $package->getPresentation_id()
            ]);
        }
    }
    public function processTotalLoss(InventoryPackages $sourcePackage, $package_count, $notes, $movement_type, $product_condition, $origin_type, $origin_id)
    {
        $id = $sourcePackage->getId();
        if ($origin_id != null) {
            $id = $origin_id;
        }
        $weightDecrease = $sourcePackage->getPackage_weight_lb() * $package_count;
        $remainingPackages = $sourcePackage->getPackage_count() - $package_count;
        $remainingWeight = $sourcePackage->getQuantity_lb() - $weightDecrease;
        if ($remainingPackages <= 0) {
            $this->inventoryPackagesDAO->delete($sourcePackage->getId());
        } else {
            $package = new InventoryPackages();
            $package->setId($sourcePackage->getId());
            $package->setPackage_count($remainingPackages);
            $package->setQuantity_lb($remainingWeight);
            $this->inventoryPackagesDAO->decreaseStock($package);
        }
        $this->movementsController->create([
            'product_id' => $sourcePackage->getProduct_id(),
            'subproduct_id' => $sourcePackage->getSubproduct_id(),
            'batch_id' => $sourcePackage->getBatch_id(),
            'location_id' => $sourcePackage->getLocation_id(),
            'presentation_id' => $sourcePackage->getPresentation_id(),
            'movement_type' => $movement_type,
            'quantity' => $weightDecrease,
            'package_count' => $package_count,
            'product_condition' => $product_condition,
            'origin_type' => $origin_type,
            'origin_id' => $id,
            'temperature_received' => null,
            'expiration_date' => null,
            'notes' => !empty($notes) ? $notes : null
        ]);
    }
    public function processPartialLoss($packageId, $weights, $notes, $movement_type, $product_condition, $origin_type, $origin_id)
    {
        foreach ($weights as $data) {
            $packageResult = $this->inventoryPackagesDAO->consultId($packageId);
            if (!empty($packageResult) && is_array($packageResult)) {
                $sourcePackage = $packageResult[0];
                $id = $sourcePackage->getId();
                if ($origin_id != null) {
                    $id = $origin_id;
                }
                $packageCount = 1;
                $quantity = (float) $data['quantity'];
                $unitsPerPackage = !empty($data['units_per_package']) ? $data['units_per_package'] : null;
                if ($packageCount <= 0 || $quantity <= 0) {
                    continue;
                }
                $packageWeight = $quantity / $packageCount;
                $this->addPartialLossStock(
                    $sourcePackage,
                    $packageCount,
                    $quantity,
                    $packageWeight,
                    $unitsPerPackage
                );
                $this->decreaseSourceStock($sourcePackage, $packageCount);
                $lossWeight = ($sourcePackage->getPackage_weight_lb() * $packageCount) - $quantity;
                $this->movementsController->create([
                    'product_id' => $sourcePackage->getProduct_id(),
                    'subproduct_id' => $sourcePackage->getSubproduct_id(),
                    'batch_id' => $sourcePackage->getBatch_id(),
                    'location_id' => $sourcePackage->getLocation_id(),
                    'presentation_id' => $sourcePackage->getPresentation_id(),
                    'movement_type' => $movement_type,
                    'quantity' => $lossWeight,
                    'package_count' => $packageCount,
                    'product_condition' => $product_condition,
                    'origin_type' => $origin_type,
                    'origin_id' => $id,
                    'temperature_received' => null,
                    'expiration_date' => null,
                    'notes' => !empty($notes) ? $notes : null
                ]);
            }
        }
    }
    private function addPartialLossStock(InventoryPackages $sourcePackage, float $packageCount, float $quantity, float $packageWeight, $unitsPerPackage)
    {
        $destinationPackage = $this->inventoryPackagesDAO->validatedIdUnit([
            $sourcePackage->getBatch_id(),
            $sourcePackage->getProduct_id(),
            $sourcePackage->getSubproduct_id(),
            $sourcePackage->getLocation_id(),
            $sourcePackage->getPresentation_id(),
            $packageWeight,
            $unitsPerPackage
        ]);

        if ($destinationPackage != null) {
            $package = new InventoryPackages();
            $package->setId($destinationPackage[0]['id']);
            $package->setPackage_count($packageCount);
            $package->setQuantity_lb($quantity);
            $this->inventoryPackagesDAO->increaseStock($package);
        } else {
            $newPackage = new InventoryPackages();
            $newPackage->setBatch_id($sourcePackage->getBatch_id());
            $newPackage->setProduct_id($sourcePackage->getProduct_id());
            $newPackage->setSubproduct_id($sourcePackage->getSubproduct_id());
            $newPackage->setLocation_id($sourcePackage->getLocation_id());
            $newPackage->setPresentation_id($sourcePackage->getPresentation_id());
            $newPackage->setPackage_weight_lb($packageWeight);
            $newPackage->setPackage_count($packageCount);
            $newPackage->setQuantity_lb($quantity);
            $newPackage->setUnits_per_package($unitsPerPackage);
            $newPackage->setStatus($sourcePackage->getStatus());
            $this->inventoryPackagesDAO->create($newPackage);
        }
    }
    private function decreaseSourceStock(InventoryPackages $sourcePackage, float $packageCount)
    {
        $remainingPackages = $sourcePackage->getPackage_count() - $packageCount;
        $remainingWeight = $sourcePackage->getPackage_weight_lb() * $remainingPackages;
        if ($remainingPackages <= 0 || $remainingWeight <= 0) {
            $this->inventoryPackagesDAO->delete($sourcePackage->getId());
        } else {
            $package = new InventoryPackages();
            $package->setId($sourcePackage->getId());
            $package->setPackage_count($remainingPackages);
            $package->setQuantity_lb($remainingWeight);
            $this->inventoryPackagesDAO->decreaseStock($package);
        }
    }
    public function productionProcess($data)
    {
        $productsInput = $data['input'];
        $productsOutput = $data['output'];
        if (!empty($productsInput) && !empty($productsOutput)) {
            $production = new Production();
            $production->setUser_id($_SESSION['ctid']);
            $production->setNotes(null);
            $idproducction = $this->productionDAO->create($production);
            if ($idproducction != null) {
                foreach ($productsOutput as $product) {
                    $idbatch = $this->createBatches($product);
                    $this->packageStock(array(
                        "batch_id" => $idbatch,
                        "product_id" => $product['productId'],
                        "subproduct_id" => $product['subproductId'],
                        "location_id" => $product['locationId'],
                        "presentation_id" => $product['presentationId'],
                        "package_weight_lb" => $product['package_weight_lb'],
                        "package_count" => $product['package_count'],
                        "quantity" => $product['quantity_lb'],
                        "units_per_package" => (trim($product['units_per_package']) !== '' ? $data['units_per_package'] : null)
                    ));
                    $this->movementsController->create(array(
                        "product_id" => $product['productId'],
                        "subproduct_id" => $product['subproductId'],
                        "batch_id" => $idbatch,
                        "location_id" => $product['locationId'],
                        "presentation_id" => $product['presentationId'],
                        "movement_type" => 'entrada',
                        "quantity" => $product['quantity_lb'],
                        "package_count" => $product['package_count'],
                        "product_condition" => $product['product_condition'],
                        "origin_type" => 'production',
                        "origin_id" => $idproducction,
                        "temperature_received" => $product['entry_temperature'],
                        "expiration_date" => $product['expiration_date'],
                        "notes" => null
                    ));
                }
                foreach ($productsInput as $product) {
                    $this->decreaseProcess(array(array(
                        "id" => $product['id'],
                        "package_count" => $product['inputPackage'],
                        "notes" => null,
                        "weights" => []
                    )), 'produccion', null, 'production', $idproducction);
                    if ($product['loss'] > 0) {
                        $packageResult = $this->inventoryPackagesDAO->consultId($product['id']);
                        if (!empty($packageResult) && is_array($packageResult)) {
                            $sourcePackage = $packageResult[0];
                            $this->movementsController->create([
                                'product_id' => $sourcePackage->getProduct_id(),
                                'subproduct_id' => $sourcePackage->getSubproduct_id(),
                                'batch_id' => $sourcePackage->getBatch_id(),
                                'location_id' => $sourcePackage->getLocation_id(),
                                'presentation_id' => $sourcePackage->getPresentation_id(),
                                'movement_type' => 'merma',
                                'quantity' => $product['loss'],
                                'package_count' => $product['inputPackage'],
                                'product_condition' => null,
                                'origin_type' => 'production',
                                'origin_id' => $idproducction,
                                'temperature_received' => null,
                                'expiration_date' => null,
                                'notes' => null
                            ]);
                        }
                    }
                }
            } else {
                throw new Exception('Ocurrio un error insperado.');
            }
        }
    }
    public function createBatches($data)
    {
        $codebatch = trim($data['batch_code']);
        if ($codebatch == '' || empty($codebatch)) {
            $codebatch = $this->batchesDAO->codeBatch();
        }
        if ($this->batchesDAO->repeated(array($codebatch))) {
            $batch = new Batches();
            $batch->setBatch_code($codebatch);
            $batch->setBatch_type('production');
            $batch->setPurchase_id(null);
            $batch->setPurchase_detail_id(null);
            $batch->setSupplier_id(null);
            $batch->setCategory_id($data['categoryId']);
            $batch->setProduct_id($data['productId']);
            $batch->setSubproduct_id($data['subproductId']);
            $batch->setProduction_date($data['production_date']);
            $batch->setExpiration_date($data['expiration_date']);
            $batch->setEntry_temperature($data['entry_temperature']);
            $batch->setNotes(trim($data['notesbatch']) !== '' ? $data['notesbatch'] : null);
            $id = $this->batchesDAO->create($batch);
            if ($id != null) {
                return $id;
            } else {
                throw new Exception('Ocurrio un error al registrar el lote.');
            }
        } else {
            throw new Exception('El código del lote ya existe.');
        }
    }

}
