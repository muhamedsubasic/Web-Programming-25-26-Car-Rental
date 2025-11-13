<?php

/**
 * @OA\Get(
 *     path="/bookings",
 *     tags={"bookings"},
 *     summary="Get bookings (optionally filter by user)",
 *     @OA\Parameter(name="user_id", in="query", required=false, @OA\Schema(type="integer"), description="Filter bookings by user_id"),
 *     @OA\Response(response=200, description="List of bookings")
 * )
 */
Flight::route('GET /bookings', function(){
    try {
        $q = Flight::request()->query;
        if (isset($q['user_id'])) {
            $res = Flight::bookingService()->getByUser((int)$q['user_id']);
        } else {
            $res = Flight::bookingService()->getActiveBookings();
        }
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Get(
 *     path="/bookings/{id}",
 *     tags={"bookings"},
 *     summary="Get booking by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Booking object")
 * )
 */
Flight::route('GET /bookings/@id', function($id){
    try {
        $res = Flight::bookingService()->getById($id);
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Post(
 *     path="/bookings",
 *     tags={"bookings"},
 *     summary="Create a new booking",
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"user_id","car_id","rented_at","return_time","price"},
 *         @OA\Property(property="user_id", type="integer"),
 *         @OA\Property(property="car_id", type="integer"),
 *         @OA\Property(property="rented_at", type="string", format="date-time"),
 *         @OA\Property(property="return_time", type="string", format="date-time"),
 *         @OA\Property(property="price", type="number", format="float")
 *     )),
 *     @OA\Response(response=200, description="Booking created")
 * )
 */
Flight::route('POST /bookings', function(){
    $data = Flight::request()->data->getData();
    try {
        $res = Flight::bookingService()->createBooking($data);
        Flight::json(['created' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Put(
 *     path="/bookings/{id}/complete",
 *     tags={"bookings"},
 *     summary="Mark a booking as completed",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Booking completed")
 * )
 */
Flight::route('PUT /bookings/@id/complete', function($id){
    try {
        $res = Flight::bookingService()->completeBooking($id);
        Flight::json(['completed' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Delete(
 *     path="/bookings/{id}",
 *     tags={"bookings"},
 *     summary="Delete a booking",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Booking deleted")
 * )
 */
Flight::route('DELETE /bookings/@id', function($id){
    try {
        $res = Flight::bookingService()->delete($id);
        Flight::json(['deleted' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

?>
