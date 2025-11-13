<?php

/**
 * @OA\Get(
 *     path="/reviews",
 *     tags={"reviews"},
 *     summary="Get all reviews or filter by car",
 *     @OA\Parameter(name="car_id", in="query", required=false, @OA\Schema(type="integer"), description="Filter reviews by car_id"),
 *     @OA\Response(response=200, description="List of reviews")
 * )
 */
Flight::route('GET /reviews', function(){
    try {
        $q = Flight::request()->query;
        if (isset($q['car_id'])) {
            $res = Flight::reviewService()->getByCar((int)$q['car_id']);
        } else {
            $res = Flight::reviewService()->getAll();
        }
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Get(
 *     path="/reviews/{id}",
 *     tags={"reviews"},
 *     summary="Get review by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Review object")
 * )
 */
Flight::route('GET /reviews/@id', function($id){
    try {
        $res = Flight::reviewService()->getById($id);
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Post(
 *     path="/reviews",
 *     tags={"reviews"},
 *     summary="Create a review for a car",
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"user_id","car_id","rating"},
 *         @OA\Property(property="user_id", type="integer"),
 *         @OA\Property(property="car_id", type="integer"),
 *         @OA\Property(property="rating", type="integer", example=5),
 *         @OA\Property(property="comment", type="string")
 *     )),
 *     @OA\Response(response=200, description="Review created")
 * )
 */
Flight::route('POST /reviews', function(){
    $data = Flight::request()->data->getData();
    try {
        $res = Flight::reviewService()->createReview($data['user_id'], $data['car_id'], $data['rating'], $data['comment'] ?? null);
        Flight::json(['created' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Get(
 *     path="/reviews/average/{car_id}",
 *     tags={"reviews"},
 *     summary="Get average rating for a car",
 *     @OA\Parameter(name="car_id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Average rating")
 * )
 */
Flight::route('GET /reviews/average/@car_id', function($car_id){
    try {
        $res = Flight::reviewService()->getAverageRating($car_id);
        Flight::json(['average' => $res]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Delete(
 *     path="/reviews/{id}",
 *     tags={"reviews"},
 *     summary="Delete a review",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Review deleted")
 * )
 */
Flight::route('DELETE /reviews/@id', function($id){
    try {
        $res = Flight::reviewService()->delete($id);
        Flight::json(['deleted' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

?>
