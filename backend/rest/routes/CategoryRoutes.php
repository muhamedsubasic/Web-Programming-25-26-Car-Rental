<?php

/**
 * @OA\Get(
 *     path="/categories",
 *     tags={"categories"},
 *     summary="Get all categories",
 *     @OA\Response(response=200, description="List of categories")
 * )
 */
Flight::route('GET /categories', function(){
    try {
        $res = Flight::categoryService()->getAllCategories();
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Get(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Get category by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Category object")
 * )
 */
Flight::route('GET /categories/@id', function($id){
    try {
        $res = Flight::categoryService()->getCategoryById($id);
        Flight::json($res);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Post(
 *     path="/categories",
 *     tags={"categories"},
 *     summary="Create a category",
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"name"},
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="description", type="string")
 *     )),
 *     @OA\Response(response=200, description="Category created")
 * )
 */
Flight::route('POST /categories', function(){
    $data = Flight::request()->data->getData();
    try {
        $res = Flight::categoryService()->createCategory($data);
        Flight::json(['created' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Put(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Update a category",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="description", type="string")
 *     )),
 *     @OA\Response(response=200, description="Category updated")
 * )
 */
Flight::route('PUT /categories/@id', function($id){
    $data = Flight::request()->data->getData();
    try {
        $res = Flight::categoryService()->updateCategory($id, $data);
        Flight::json(['updated' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Delete(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Delete a category",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Category deleted")
 * )
 */
Flight::route('DELETE /categories/@id', function($id){
    try {
        $res = Flight::categoryService()->delete($id);
        Flight::json(['deleted' => (bool)$res]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

?>
