<?php namespace App\Models;

/**
 * @SWG\Definition(
 *   definition="apiResponse",
 *   @SWG\Property(property="success", type="boolean"),
 *   @SWG\Property(property="data", type="string"),
 *   @SWG\Property(property="errors", type="string")
 * )
 */
class ApiResponse {

    public $success;

    public $data;

    public $errors;
}
