<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\ApiResponse;
use App\Models\Invite;
use App\Models\User;
use App\Services\PointService;

class InviteController extends Controller {

    private $pointService;
    private $googlePlayUrl = "https://play.google.com/store/apps/details?id=gr.scify.cityrus";
    private $appStoreUrl = "";

    public function __construct() {
        $this->middleware('jwt.auth', ['only' => ['invite']]);
        $this->pointService = new PointService();
    }

    /**
     * Allow a user to invite a friend to the application,
     * by sending them an email.
     * The click is tracked, so when the friends opens his/hers email and
     * clicks the link, the user is rewarded with points.
     *
     * @return mixed
     */
    public function invite() {

        $email = \Request::get('email');
        $msg = \Request::get('msg');
        $user = User::find(\Auth::user()->id);

        $token = str_random(20);
        Invite::create([
            'message' => $msg,
            'token' => $token,
            'user_id' => $user->id,
            'email' => $email
        ]);

        //send the user's friend an email to invite them to the app
        \Mail::send('emails.invite_friends', ['email' => $email, 'msg' => $msg, 'user' => $user, 'token' => $token], function ($message) use ($email) {
            $message->to($email)->subject('Πρόσκληση στην εφαρμογή City-R-US!');
        });

        $response = new ApiResponse();

        $response->status = 'success';
        $response->message = [
            'description' => 'Email sent'];

        return \Response::json($response);
    }

    /**
     * When the friend clicks the link in their email,
     * the user that invited them is rewarded
     *
     * @return mixed
     */
    public function inviteClicked() {

        //find invite based on the token
        $invite = Invite::where('token', \Request::get('token'))->first();

        if ($invite != null && !$invite->clicked) {
            //reward user when the link is clicked
            $this->pointService->inviteReward(\Request::get('user_id'), $invite->id);

            $invite->update(['clicked' => true]);
        }

        //determine the platform and set the url accordingly
        if (\Request::get('platform') == 'android')
            $url = $this->googlePlayUrl;
        else
            $url = $this->appStoreUrl;

        //redirect to googlePlay/appStore
        return \Redirect::to($url);
    }
}
