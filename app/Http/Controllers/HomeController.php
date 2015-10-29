<?php namespace App\Http\Controllers;

use Swagger\Annotations\Swagger;

/**
 * Class HomeController
 *
 * @package App\Http\Controllers
 *
 * @SWG\Swagger(
 *     host="http://cityrus.projects.development1.scify.org/www/city-r-us-service/public/api",
 *     basePath="/v1",
 *     schemes={"http"},
 *     @SWG\Info(
 *         version="1.0",
 *         title="City-R-US API",
 *         description = "API, API on the wall, who is the RESTiest of them all?",
 *         @SWG\Contact(name="SciFY"),
 *     ),
 *
 *     @SWG\Definition(
 *         definition="Error",
 *         required={"code", "message"},
 *         @SWG\Property(
 *             property="code",
 *             type="integer",
 *             format="int32"
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *         )
 *     )
 * )
 */
class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('home');
	}

}
