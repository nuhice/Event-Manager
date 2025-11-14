<?php
require_once __DIR__ . '/../../services/UserService.php';

class UserRoutes {
    private $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    public function registerRoutes() {
    /**
     * @OA\Get(
     *   path="/users",
     *   tags={"users"},
     *   summary="Get all users",
     *   @OA\Response(response=200, description="List of users")
     * )
     */
    Flight::route('GET /users', function() {
            try {
                $users = $this->userService->getAllUsers();
                Flight::json([
                    'success' => true,
                    'data' => $users
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
     *   path="/users/{id}",
     *   tags={"users"},
     *   summary="Get user by ID",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(response=200, description="User found"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('GET /users/@id', function($id) {
            try {
                $user = $this->userService->getUserById($id);
                Flight::json([
                    'success' => true,
                    'data' => $user
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
     *   path="/users",
     *   tags={"users"},
     *   summary="Create a new user",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         required={"name","email","password"},
     *         @OA\Property(property="name", type="string", example="Alice"),
     *         @OA\Property(property="email", type="string", format="email", example="alice@example.com"),
     *         @OA\Property(property="password", type="string", example="P@ssw0rd")
     *       )
     *     )
     *   ),
     *   @OA\Response(response=201, description="Created"),
     *   @OA\Response(response=400, description="Validation error")
     * )
     */
    Flight::route('POST /users', function() {
            try {
                $data = Flight::request()->data->getData();
                $this->userService->createUser($data);
                Flight::json([
                    'success' => true,
                    'message' => 'User created successfully'
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
     *   path="/users/{id}",
     *   tags={"users"},
     *   summary="Update an existing user",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/json",
     *     @OA\Schema(
     *       @OA\Property(property="name", type="string", example="Alice"),
     *       @OA\Property(property="email", type="string", format="email", example="alice@example.com"),
     *       @OA\Property(property="password", type="string", example="newpass")
     *     )
     *   )),
     *   @OA\Response(response=200, description="Updated"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('PUT /users/@id', function($id) {
            try {
                $data = Flight::request()->data->getData();
                $this->userService->updateUser($id, $data);
                Flight::json([
                    'success' => true,
                    'message' => 'User updated successfully'
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
     *   path="/users/{id}",
     *   tags={"users"},
     *   summary="Delete a user",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Deleted"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    Flight::route('DELETE /users/@id', function($id) {
            try {
                $this->userService->deleteUser($id);
                Flight::json([
                    'success' => true,
                    'message' => 'User deleted successfully'
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
