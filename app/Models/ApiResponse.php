<?php namespace App\Models;

/**
 * @SWG\Definition(
 *   definition="apiResponse",
 *   @SWG\Property(property="status", type="boolean"),
 *   @SWG\Property(property="message", type="string")
 * )
 */
class ApiResponse {

    public $status;

    public $message;

}
