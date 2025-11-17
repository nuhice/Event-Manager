<?php
require_once __DIR__ . '/../../services/EventService.php';
class EventRoutes {
    private $eventService;
    public function __construct() {
        $this->eventService = new EventService();
    }
    public function registerRoutes() {
    /**
     * @OA\Get(
     *   path="/events",
     *   tags={"events"},
     *   summary="Get all events",
     *   @OA\Response(response=200, description="List of events")
     * )
     */
    Flight::route('GET /events', function() {
            try {
                $events = $this->eventService->getAllEvents();
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
    Flight::route('GET /events/@id', function($id) {
            try {
                $event = $this->eventService->getEventById($id);
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
    Flight::route('POST /events', function() {
            try {
                $data = Flight::request()->data->getData();
                $this->eventService->createEvent($data);
                Flight::json([
                    'success' => true,
                    'message' => 'Event created successfully'
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
    Flight::route('PUT /events/@id', function($id) {
            try {
                $data = Flight::request()->data->getData();
                $this->eventService->updateEvent($id, $data);
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
    Flight::route('DELETE /events/@id', function($id) {
            try {
                $this->eventService->deleteEvent($id);
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
