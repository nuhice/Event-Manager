<?php
require_once __DIR__ . '/../../services/LocationService.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
class LocationRoutes {
    private $locationService;
    public function __construct() {
        $this->locationService = new LocationService();
    }
    public function registerRoutes() {
        $locationService = $this->locationService;
        
    /**
     * @OA\Get(
     *   path="/locations",
     *   tags={"locations"},
     *   summary="Get all locations",
     *   @OA\Response(response=200, description="List of locations")
     * )
     */
    Flight::route('GET /locations', function() use ($locationService) {
            try {
                $locations = $locationService->getAllLocations();
                Flight::json([
                    'success' => true,
                    'data' => $locations
                ], 200);
            } catch (Exception $e) {
                Flight::json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    /**
     * @OA\Get(
     *   path="/locations/{id}",
     *   tags={"locations"},
     *   summary="Get location by ID",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Location found"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('GET /locations/@id', function($id) use ($locationService) {
            try {
                $location = $locationService->getLocationById($id);
                Flight::json([
                    'success' => true,
                    'data' => $location
                ], 200);
            } catch (Exception $e) {
                $statusCode = strpos($e->getMessage(), 'not found') !== false ? 404 : 400;
                Flight::json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], $statusCode);
            }
        });
    /**
     * @OA\Post(
     *   path="/locations",
     *   tags={"locations"},
     *   summary="Create a location",
     *   @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/json",
     *     @OA\Schema(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", example="Main Hall"),
     *       @OA\Property(property="address", type="string", example="123 Main St"),
     *       @OA\Property(property="capacity", type="integer", example=300)
     *     )
     *   )),
     *   @OA\Response(response=201, description="Created"),
     *   @OA\Response(response=400, description="Validation error")
     * )
     */
    Flight::route('POST /locations', function() use ($locationService) {
            (new AuthMiddleware())->validate('admin');
            try {
                $data = Flight::request()->data->getData();
                $locationService->createLocation($data);
                Flight::json([
                    'success' => true,
                    'message' => 'Location created successfully'
                ], 201);
            } catch (Exception $e) {
                $statusCode = strpos($e->getMessage(), 'already exists') !== false ? 409 : 400;
                Flight::json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], $statusCode);
            }
        });
    /**
     * @OA\Put(
     *   path="/locations/{id}",
     *   tags={"locations"},
     *   summary="Update a location",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/json",
     *     @OA\Schema(
     *       @OA\Property(property="name", type="string", example="Updated Hall"),
     *       @OA\Property(property="address", type="string", example="456 Elm St"),
     *       @OA\Property(property="capacity", type="integer", example=250)
     *     )
     *   )),
     *   @OA\Response(response=200, description="Updated"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('PUT /locations/@id', function($id) use ($locationService) {
            (new AuthMiddleware())->validate('admin');
            try {
                $data = Flight::request()->data->getData();
                $locationService->updateLocation($id, $data);
                Flight::json([
                    'success' => true,
                    'message' => 'Location updated successfully'
                ], 200);
            } catch (Exception $e) {
                $statusCode = strpos($e->getMessage(), 'not found') !== false ? 404 : 400;
                Flight::json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], $statusCode);
            }
        });
    /**
     * @OA\Delete(
     *   path="/locations/{id}",
     *   tags={"locations"},
     *   summary="Delete a location",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Deleted"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('DELETE /locations/@id', function($id) use ($locationService) {
            (new AuthMiddleware())->validate('admin');
            try {
                $locationService->deleteLocation($id);
                Flight::json([
                    'success' => true,
                    'message' => 'Location deleted successfully'
                ], 200);
            } catch (Exception $e) {
                $statusCode = strpos($e->getMessage(), 'not found') !== false ? 404 : 400;
                Flight::json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], $statusCode);
            }
        });
    }
}
?>
