<?php

/**
 * @OA\Get(
 *     path="/cars",
 *     tags={"cars"},
 *     summary="Get all cars or filter by category",
 *     @OA\Parameter(name="category", in="query", required=false, @OA\Schema(type="integer"), description="Category ID to filter cars"),
 *     @OA\Response(response=200, description="List of cars")
 * )
 */
Flight::route('GET /cars', function(){
    try {
        $q = Flight::request()->query;
        if (isset($q['category'])) {
            $res = Flight::carService()->getByCategory((int)$q['category']);
        } else {
            $res = Flight::carService()->getAll();
        }
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Get(
 *     path="/cars/available",
 *     tags={"cars"},
 *     summary="Get available cars",
 *     @OA\Response(response=200, description="List of available cars")
 * )
 */
Flight::route('GET /cars/available', function(){
    try {
        $res = Flight::carService()->getAvailable();
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Get(
 *     path="/cars/{id}",
 *     tags={"cars"},
 *     summary="Get car by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Car object")
 * )
 */
Flight::route('GET /cars/@id', function($id){
    try {
        $res = Flight::carService()->getById($id);
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Post(
 *     path="/cars",
 *     tags={"cars"},
 *     summary="Create a new car",
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"category_id","user_id","model","brand","daily_rate"},
 *         @OA\Property(property="category_id", type="integer"),
 *         @OA\Property(property="user_id", type="integer"),
 *         @OA\Property(property="model", type="string"),
 *         @OA\Property(property="brand", type="string"),
 *         @OA\Property(property="daily_rate", type="number", format="float")
 *     )),
 *     @OA\Response(response=200, description="Car created")
 * )
 */
Flight::route('POST /cars', function(){
    $data = Flight::request()->data->getData();
    try {
        $res = Flight::carService()->createCar($data);
        Flight::json(['created' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Put(
 *     path="/cars/{id}",
 *     tags={"cars"},
 *     summary="Update a car",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         @OA\Property(property="model", type="string"),
 *         @OA\Property(property="brand", type="string"),
 *         @OA\Property(property="availability", type="boolean"),
 *         @OA\Property(property="daily_rate", type="number", format="float")
 *     )),
 *     @OA\Response(response=200, description="Car updated")
 * )
 */
Flight::route('PUT /cars/@id', function($id){
    $data = Flight::request()->data->getData();
    try {
        $res = Flight::carService()->updateCar($id, $data);
        Flight::json(['updated' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Delete(
 *     path="/cars/{id}",
 *     tags={"cars"},
 *     summary="Delete a car",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Car deleted")
 * )
 */
Flight::route('DELETE /cars/@id', function($id){
    try {
        $res = Flight::carService()->delete($id);
        Flight::json(['deleted' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

?>
