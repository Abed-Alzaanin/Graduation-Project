<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserBusinessWork;
use App\Models\UserFavourite;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash as FacadesHash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected function create(array $data)
    {
        if (isset($data['email'])) {

            $password = FacadesHash::make($data['password']);

            return User::create([
                'role'     => $data['role'],
                'password' => $password,
                'name'     => $data['name'],
                'email'    => $data['email'],
            ]);

        }
    }

    public function register(Request $request)
    {

        // $users = DB::table('users')
        // ->where ("id",5)
        // ->update(["deleted_at" => now()]);
        

        // // $users = DB::table('users')
        // // ->select("name")
        // // ->whereNotNull ("deleted_at")
        // // ->get();

        // $users2 = User::where("name","aebd")
        // //->withTrashed ()
        // ->delete();

        // return $users;



        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:60',
            'email'    => 'required|string|email|max:255|unique:users,email,NULL,id',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|numeric',
        ]);

        if ($validator->fails())
            return response()->json([
                'status'  => False,
                'code'    => 422,
                'message' => 'error',
                'data'    => ["errors" => $validator->errors()],
            ], 422);
        $user = $this->create($request->all());
        $user->generateToken();
        $userr = User::where('email', $user->email)->first();
        return response()->json([
            'status'  => True,
            'code'    => 200,
            'message' => 'success',
            'data'    => ['user' => $userr],
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => False,
                'code'    => 422,
                'message' => 'error',
                'data'    => ['errors' => $validator->errors()],
            ], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            $user->generateToken();
            return response()->json([
                'status'  => True,
                'code'    => 200,
                'message' => 'success',
                'data'    => ['user' => $user],
            ], 200);
        } else {
            return response()->json([
                'status'  => False,
                'code'    => 422,
                'message' => 'Wrong credentials',
                'data'    => Null,
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();

        if ($user) {
            $user->api_token = null;
            $user->save();
            return response()->json([
                'status'  => True,
                'code'    => 200,
                'message' => 'success',
                'data'    => null,
            ], 200);
        } else {
            return response()->json([
                'status'  => False,
                'code'    => 422,
                'message' => 'Invalid Token',
                'data'    => Null,
            ], 422);
        }
    }

    public function view($id)
    {
        return response()->json(User::find($id));
    }

    public function businessWorks($id)
    {
        return response()->json(UserBusinessWork::where('user_id', $id)->get());
    }

    public function addBusinessWork(Request $request, $id)
    {
        $validated = $request->validate([
            'title'       => 'required',
            'description' => 'required',
            'image'       => 'required',
        ]);

        $validated['user_id'] = $id;

        $ubw = UserBusinessWork::create($validated);

        return response()->json($ubw);
    }

    public function favouriteCraftmans($id)
    {
        return response()->json(UserFavourite::with('craftmanUser')->get());
    }

    public function addFavouriteCraftman($id, $craftman_id)
    {
        return response()->json(UserFavourite::query()->create(['user_id' => $id, 'craftman_id' => $craftman_id]));
    }

    public function removeFavouriteCraftman($id, $craftman_id)
    {
        UserFavourite::query()->where(['user_id' => $id, 'craftman_id' => $craftman_id])->delete();

        return response()->json(['status' => 'success']);
    }

    public function updateProfile(Request $request)
    {
        $user = User::where('api_token', request('api_token'))->first();

        $validated = $request->validate([
           'name' => 'sometimes|required',
           'email' => "sometimes|unique:users,email,{$user->id}",
           'password' => 'nullable',
           'image' => 'sometimes|required',
           'gender' => 'sometimes|required',
           'address' => 'sometimes|required',
           'price_hourly' => 'sometimes|required|numeric',
           'job_category_id' => 'sometimes|required|exists:job_categories,id',
           'bio' => 'sometimes|required',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    public function notifications()
    {
        $user = User::where('api_token', request('api_token'))->first();

        return response()->json(Notification::query()->where('user_id', $user->id)->paginate());
    }

    public function readNotification($notificationId)
    {
        $notification = Notification::query()->findOrFail($notificationId);
        $notification->read_at = now();
        $notification->save();

        return response()->json($notification);
    }
}
