<?php
require_once __DIR__ . '/../../services/BookingService.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
class BookingRoutes {
    private $bookingService;
    public function __construct() {
        $this->bookingService = new BookingService();
    }
    public function registerRoutes() {
        $bookingService = $this->bookingService;
        
    /**
     * @OA\Get(
     *   path="/bookings",
     *   tags={"bookings"},
     *   summary="Get all bookings",
     *   @OA\Response(response=200, description="List of bookings")
     * )
     */
    Flight::route('GET /bookings', function() use ($bookingService) {
            try {
                (new AuthMiddleware())->validate();
                
                $user = Flight::get('user');
                
                if ($user->role == 'admin') {
                    $bookings = $bookingService->getAllBookings();
                } else {
                    $bookings = $bookingService->getBookingsByUserId($user->id);
                }
                
                Flight::json([
                    'success' => true,
                    'data' => $bookings
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
     *   path="/bookings/{id}",
     *   tags={"bookings"},
     *   summary="Get booking by ID",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Booking found"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('GET /bookings/@id', function($id) use ($bookingService) {
            (new AuthMiddleware())->validate();
            try {
                $user = Flight::get('user');
                $booking = $bookingService->getBookingById($id);
                if ($user->role != 'admin' && $booking['user_id'] != $user->id) {
                    Flight::json(['error' => 'Forbidden'], 403);
                    return;
                }
                Flight::json([
                    'success' => true,
                    'data' => $booking
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
     *   path="/bookings",
     *   tags={"bookings"},
     *   summary="Create a booking",
     *   @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/json",
     *     @OA\Schema(
     *       required={"user_id","event_id"},
     *       @OA\Property(property="user_id", type="integer", example=1),
     *       @OA\Property(property="event_id", type="integer", example=2),
     *       @OA\Property(property="status", type="string", example="pending")
     *     )
     *   )),
     *   @OA\Response(response=201, description="Created"),
     *   @OA\Response(response=400, description="Validation error")
     * )
     */
    Flight::route('POST /bookings', function() use ($bookingService) {
            (new AuthMiddleware())->validate();
            try {
                $data = Flight::request()->data->getData();
                $data['user_id'] = Flight::get('user')->id;
                $bookingService->createBooking($data);
                Flight::json([
                    'success' => true,
                    'message' => 'Booking created successfully'
                ], 201);
            } catch (Exception $e) {
                $statusCode = strpos($e->getMessage(), 'already booked') !== false ? 409 : 400;
                Flight::json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], $statusCode);
            }
        });
    /**
     * @OA\Put(
     *   path="/bookings/{id}",
     *   tags={"bookings"},
     *   summary="Update a booking",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/json",
     *     @OA\Schema(
     *       @OA\Property(property="user_id", type="integer", example=1),
     *       @OA\Property(property="event_id", type="integer", example=2),
     *       @OA\Property(property="status", type="string", example="confirmed")
     *     )
     *   )),
     *   @OA\Response(response=200, description="Updated"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('PUT /bookings/@id', function($id) use ($bookingService) {
            (new AuthMiddleware())->validate();
            try {
                $user = Flight::get('user');
                $booking = $bookingService->getBookingById($id);
                if ($user->role != 'admin' && $booking['user_id'] != $user->id) {
                    Flight::json(['error' => 'Forbidden'], 403);
                    return;
                }

                $data = Flight::request()->data->getData();
                $bookingService->updateBooking($id, $data);
                Flight::json([
                    'success' => true,
                    'message' => 'Booking updated successfully'
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
     *   path="/bookings/{id}",
     *   tags={"bookings"},
     *   summary="Delete a booking",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Deleted"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('DELETE /bookings/@id', function($id) use ($bookingService) {
            (new AuthMiddleware())->validate();
            try {
                $user = Flight::get('user');
                $booking = $bookingService->getBookingById($id);
                if ($user->role != 'admin' && $booking['user_id'] != $user->id) {
                    Flight::json(['error' => 'Forbidden'], 403);
                    return;
                }

                $bookingService->deleteBooking($id);
                Flight::json([
                    'success' => true,
                    'message' => 'Booking deleted successfully'
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
