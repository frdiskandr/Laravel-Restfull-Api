<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Laravel OpenApi Demo Documentation",
 *      description="L5 Swagger OpenApi description",
 *      @OA\Contact(
 *          email="admin@admin.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer"
 * )
 */
class UserController
{
    /**
     * @OA\Post(
     *      path="/api/users/register",
     *      tags={"User"},
     *      summary="Register a new user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"username", "password", "name"},
     *              @OA\Property(property="username", type="string", example="testuser"),
     *              @OA\Property(property="password", type="string", format="password", example="password"),
     *              @OA\Property(property="name", type="string", example="Test User")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User registered successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      )
     * )
     */
    public function Register(UserRegisterRequest $request):Response{
        $data = $request->validated();

        // check user exsist?
        if(User::where("username", $data['username'])->count() >= 1){
            return throw new HttpResponseException(response([
                "errors" => "Username Already Exist!",
            ], 400));
        }

        // hash pass and save user to db
        $user = new User($data);
        $user->password = Hash::make($user['password']);
        $user->save();

        return response([
            "message" => "sucesss",
        ],201);
    }

    /**
     * @OA\Post(
     *      path="/api/users/login",
     *      tags={"User"},
     *      summary="Login a user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"username", "password"},
     *              @OA\Property(property="username", type="string", example="testuser"),
     *              @OA\Property(property="password", type="string", format="password", example="password")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User logged in successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      )
     * )
     */
    public function Login(UserLoginRequest $request):Response{
        $data = $request->validated();

        $user = User::where("username", $data['username'])->first();
        if(!$user || !Hash::check($data['password'], $user->password)){
            return throw new HttpResponseException(response([
                "message" => "username/password failed!"
            ],400));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return response([
            "message"=> "sucess",
            "token" => $user->token,
        ],200);
    }

    /**
     * @OA\Get(
     *      path="/api/users",
     *      tags={"User"},
     *      summary="Get current user",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function Get(Request $request){
        $user = Auth::user();

        return response([
            "message" => "success",
            "data"=> $user,
        ],200);
    }

    /**
     * @OA\Patch(
     *      path="/api/users",
     *      tags={"User"},
     *      summary="Update current user",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Test User"),
     *              @OA\Property(property="password", type="string", format="password", example="newpassword")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function Update(UserUpdateRequest $request): Response
    {
        $data = $request->validated();
        $user = Auth::user();

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response([
            'message' => 'success',
            'data' => $user
        ], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/users/logout",
     *      tags={"User"},
     *      summary="Logout a user",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="User logged out successfully"
     *      )
     * )
     */
    public function Logout(){
        $user = Auth::user();
        $user->token = null;

        $user->save();

        return response([
            "message" => "logout sucess",
        ], 200);

    }
}