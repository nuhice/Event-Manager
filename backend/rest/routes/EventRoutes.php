<?php
require_once __DIR__ . '/../../services/EventService.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
class EventRoutes {
    private $eventService;
    public function __construct() {
        $this->eventService = new EventService();
    }
    public function registerRoutes() {
        $eventService = $this->eventService;
        
    /**
     * @OA\Get(
     *   path="/events",
     *   tags={"events"},
     *   summary="Get all events",
     *   @OA\Response(response=200, description="List of events")
     * )
     */
    Flight::route('GET /events', function() use ($eventService) {
            try {
                $events = $eventService->getAllEvents();
                Flight::json([
                    'success' => true,
                    'data' => $events
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
     *   path="/events/{id}",
     *   tags={"events"},
     *   summary="Get event by ID",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Event found"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('GET /events/@id', function($id) use ($eventService) {
            try {
                $event = $eventService->getEventById($id);
                Flight::json([
                    'success' => true,
                    'data' => $event
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
     *   path="/events",
     *   tags={"events"},
     *   summary="Create a new event",
     *   @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/json",
     *     @OA\Schema(
     *       required={"title","start_date"},
     *       @OA\Property(property="title", type="string", example="Music Concert"),
     *       @OA\Property(property="start_date", type="string", format="date-time", example="2025-12-01T19:00:00Z"),
     *       @OA\Property(property="end_date", type="string", format="date-time", example="2025-12-01T22:00:00Z"),
     *       @OA\Property(property="capacity", type="integer", example=150),
     *       @OA\Property(property="location_id", type="integer", example=1),
     *       @OA\Property(property="description", type="string", example="An evening of live music")
     *     )
     *   )),
     *   @OA\Response(response=201, description="Created"),
     *   @OA\Response(response=400, description="Validation error")
     * )
     */
    Flight::route('POST /events', function() use ($eventService) {
            try {
                (new AuthMiddleware())->validate();
                $data = Flight::request()->data->getData();
                $data['organizer_id'] = Flight::get('user')->id;
                $eventService->createEvent($data);
                
                http_response_code(201);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Event created successfully'
                ]);
            } catch (Exception $e) {
                $statusCode = strpos($e->getMessage(), 'already exists') !== false ? 409 : 400;
                http_response_code($statusCode);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            }
        });
    /**
     * @OA\Put(
     *   path="/events/{id}",
     *   tags={"events"},
     *   summary="Update an event",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/json",
     *     @OA\Schema(
     *       @OA\Property(property="title", type="string", example="Updated Title"),
     *       @OA\Property(property="start_date", type="string", format="date-time", example="2025-12-01T19:00:00Z"),
     *       @OA\Property(property="end_date", type="string", format="date-time", example="2025-12-01T22:00:00Z"),
     *       @OA\Property(property="capacity", type="integer", example=200)
     *     )
     *   )),
     *   @OA\Response(response=200, description="Updated"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('PUT /events/@id', function($id) use ($eventService) {
            (new AuthMiddleware())->validate();
            try {
                $user = Flight::get('user');
                $event = $eventService->getEventById($id);
                if ($user->role != 'admin' && $event['organizer_id'] != $user->id) {
                    Flight::json(['error' => 'Forbidden'], 403);
                    return;
                }

                $data = Flight::request()->data->getData();
                $eventService->updateEvent($id, $data);
                Flight::json([
                    'success' => true,
                    'message' => 'Event updated successfully'
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
     *   path="/events/{id}",
     *   tags={"events"},
     *   summary="Delete an event",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Deleted"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('DELETE /events/@id', function($id) use ($eventService) {
            (new AuthMiddleware())->validate();
            try {
                $user = Flight::get('user');
                $event = $eventService->getEventById($id);
                if ($user->role != 'admin' && $event['organizer_id'] != $user->id) {
                    Flight::json(['error' => 'Forbidden'], 403);
                    return;
                }

                $eventService->deleteEvent($id);
                Flight::json([
                    'success' => true,
                    'message' => 'Event deleted successfully'
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
