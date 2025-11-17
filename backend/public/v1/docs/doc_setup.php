<?php

/**
 * @OA\Info(
 *     title="Event Manager API",
 *     description="Event Manager API Documentation",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="tvoj@email.com",
 *         name="Your Name"
 *     )
 * )
 */

/**
 * @OA\Server(
 *     url="http://localhost/eventmanager/backend",
 *     description="Local API server"
 * )
 */

/**
 * @OA\Server(
 *     url="https://lobster-app-czvm2.ondigitalocean.app/backend",
 *     description="Production API server"
 * )
 */

/**
 * @OA\SecurityScheme(
 *     securityScheme="ApiKey",
 *     type="apiKey",
 *     in="header",
 *     name="Authentication"
 * )
 */