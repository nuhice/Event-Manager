<?php
require_once __DIR__ . '/../../services/ContactService.php';

class ContactRoutes {
    private $contactService;

    public function __construct() {
        $this->contactService = new ContactService();
    }

    public function registerRoutes() {
        $contactService = $this->contactService;

        /**
         * @OA\Get(
         *   path="/contacts",
         *   tags={"contacts"},
         *   summary="Get all contacts",
         *   @OA\Response(response=200, description="List of contact messages")
         * )
         */
        Flight::route('GET /contacts', function() use ($contactService) {
            try {
                $contacts = $contactService->getAllContacts();
                Flight::json([
                    'success' => true,
                    'data' => $contacts
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
         *   path="/contacts/{id}",
         *   tags={"contacts"},
         *   summary="Get contact by ID",
         *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
         *   @OA\Response(response=200, description="Contact found"),
         *   @OA\Response(response=404, description="Not found")
         * )
         */
        Flight::route('GET /contacts/@id', function($id) use ($contactService) {
            try {
                $contact = $contactService->getContactById($id);
                Flight::json([
                    'success' => true,
                    'data' => $contact
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
         *   path="/contacts",
         *   tags={"contacts"},
         *   summary="Create a contact message",
         *   @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/json",
         *     @OA\Schema(
         *       required={"name","email","message"},
         *       @OA\Property(property="name", type="string", example="John Doe"),
         *       @OA\Property(property="email", type="string", format="email", example="john@example.com"),
         *       @OA\Property(property="message", type="string", example="I'd like more info about your events.")
         *     )
         *   )),
         *   @OA\Response(response=201, description="Created"),
         *   @OA\Response(response=400, description="Validation error")
         * )
         */
        Flight::route('POST /contacts', function() use ($contactService) {
            try {
                $data = Flight::request()->data->getData();
                $contactService->createContact($data);
                Flight::json([
                    'success' => true,
                    'message' => 'Contact created successfully'
                ], 201);
            } catch (Exception $e) {
                Flight::json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        });

        /**
         * @OA\Put(
         *   path="/contacts/{id}",
         *   tags={"contacts"},
         *   summary="Update a contact message",
         *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
         *   @OA\RequestBody(required=true, @OA\MediaType(mediaType="application/json",
         *     @OA\Schema(
         *       @OA\Property(property="name", type="string", example="John Doe"),
         *       @OA\Property(property="email", type="string", format="email", example="john@example.com"),
         *       @OA\Property(property="message", type="string", example="Updated message")
         *     )
         *   )),
         *   @OA\Response(response=200, description="Updated"),
         *   @OA\Response(response=404, description="Not found")
         * )
         */
        Flight::route('PUT /contacts/@id', function($id) use ($contactService) {
            try {
                $data = Flight::request()->data->getData();
                $contactService->updateContact($id, $data);
                Flight::json([
                    'success' => true,
                    'message' => 'Contact updated successfully'
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
         *   path="/contacts/{id}",
         *   tags={"contacts"},
         *   summary="Delete a contact message",
         *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
         *   @OA\Response(response=200, description="Deleted"),
         *   @OA\Response(response=404, description="Not found")
         * )
         */
        Flight::route('DELETE /contacts/@id', function($id) use ($contactService) {
            try {
                $contactService->deleteContact($id);
                Flight::json([
                    'success' => true,
                    'message' => 'Contact deleted successfully'
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