<?php

namespace App\Http\Controllers\Auth;

//use App\Http\Requests\Request;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\Response;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/mydrops';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
    
    public function redirectToProvider($provider){
        return Socialite::driver($provider)->redirect();
    }
    
    public function handleProviderCallback($provider){

        $user = Socialite::driver($provider)->user();
         
        // stroing data to our use table and logging them in
        $data = [
            'email' => $user->getEmail()
        ];
        
        $new_user = (User::firstOrCreate($data));
        $new_user->name = $user->getName();
        $new_user->confirmed = 1;
        $new_user->save();
        Auth::login($new_user);


        //after login redirecting to myDrops page
        return redirect($this->redirectPath());
    }


    public function loginApi(Request $request)
    {
        $email = $request->input("email");
        $password = $request->input("password");

        if(Auth::attempt(['email' => $email, 'password' => $password]))
        {
            $user = User::all()->where("email",$email)->first();


            return response($user, 200);


           // $user = User::f()->where('email', $email)->first();
           // return response()->json(['email' => $email, 'id' => $id]);
        }
        //$sensor = User::where('email',$request->input("email"))->where("name",$request->input("name"))->first();   //where("password",bcrypt($request->input("password")))->first();

        else{
            return response("Something is wrong", 401);  //return 401 Unauthorized
        }
    }


    public function registerApi(Request $request)
    {
       // $this->validator($request->all())->validate();
        $user = $this->create($request->all());
        $user->confirmation_code = str_random(15);

        if($user->save()){
          $this->sendEmail($user);
            return response()->json(['result' => 'account created']);

        }
        //$user = User::table('users')->where('email', $request->input("email"))->first();
      /*  Mail::send('emails.verify', ["user" => $request->input("name"),"confirmation_code" => $user->confirmation_code], function ($message) {
            //$message->from('us@example.com', 'Laravel');
           // $user = Auth::user();
            $message->to($u, name)->subject("Activate account!");
        });*/

        //Alert::success('Success', 'Email was send');

        //User::create(['confirmation_code' => str_random(15)]);
        //$this->guard()->login($user);
        // assign  endcustomer role
      //  $user->roles()->toggle(3);
        //return redirect($this->redirectPath());
    }
    public function sendEmail(User $user)
    {
        $data = array(
            'user' => $user->name,
            'confirmation_code' => $user->confirmation_code,
        );
        \Mail::queue('emails.verify', $data, function($message) use ($user) {
            $message->subject( 'Subject line Here' );
            $message->to($user->email);
        });
    }




}
