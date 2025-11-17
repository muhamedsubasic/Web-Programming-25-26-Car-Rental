<?php

/**
 * @OA\Get(
 *     path="/users",
 *     tags={"users"},
 *     summary="Get all users",
 *     @OA\Parameter(
 *         name="role",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string"),
 *         description="Optional role filter (e.g., 'customer' or 'admin')"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of users"
 *     )
 * )
 */
Flight::route('GET /users', function(){
    try {
        $res = Flight::usersService()->getAll();
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Get(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Get user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="User ID"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User object"
 *     )
 * )
 */
Flight::route('GET /users/@id', function($id){
    try {
        $res = Flight::usersService()->getById($id);
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Post(
 *     path="/users",
 *     tags={"users"},
 *     summary="Create/register a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","surname","email","password"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="surname", type="string"),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string"),
 *             @OA\Property(property="phone", type="string"),
 *             @OA\Property(property="city", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="User created")
 * )
 */
Flight::route('POST /users', function(){
    $data = Flight::request()->data->getData();
    try {
        $res = Flight::usersService()->register($data);
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Post(
 *     path="/login",
 *     tags={"users"},
 *     summary="Authenticate user and return user object",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Authenticated user object"),
 *     @OA\Response(response=401, description="Invalid credentials")
 * )
 */
Flight::route('POST /login', function(){
    $data = Flight::request()->data->getData();
    try {
        $user = Flight::usersService()->login($data['email'] ?? '', $data['password'] ?? '');
        if ($user === false) Flight::halt(401, 'Invalid credentials');
        Flight::json($user);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Put(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Update a user's profile",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="surname", type="string"),
 *             @OA\Property(property="phone", type="string"),
 *             @OA\Property(property="city", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="User updated")
 * )
 */
Flight::route('PUT /users/@id', function($id){
    $data = Flight::request()->data->getData();
    try {
        $res = Flight::usersService()->updateProfile($id, $data);
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Delete(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Delete a user",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="User deleted")
 * )
 */
Flight::route('DELETE /users/@id', function($id){
    try {
        $res = Flight::usersService()->delete($id);
        Flight::json(['deleted' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

?>
