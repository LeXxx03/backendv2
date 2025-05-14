<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()

    {

        return Post::all();

    }

 

    public function show($id)

    {

        return Post::findOrFail($id);

    }

 

    public function store(Request $request)

    {

        $post = Post::create($request->all());

        return response()->json($post, 201);

    }

 

    public function update(Request $request, $id)

    {

        $post = Post::findOrFail($id);

        $post->update($request->all());

        return response()->json($post, 200);

    }

 

    public function destroy($id)

    {

        Post::destroy($id);

        return response()->json(null, 204);

    }
    /**
     * Felhasználó regisztrálása.
     */
    public function register(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'passw' => 'required|string|min:8',
        'phoneNumb' => 'required|string|max:15|unique:users',
        'city' => 'required|string|max:255',
        'gender' => 'required|string|max:10',
        'description' => 'nullable|string|max:1000',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'passw' => Hash::make($request->password), // Jelszó hash-elése
        'phoneNumb' => $request->phoneNumb,
        'city' => $request->city,
        'gender' => $request->gender,
        'description' => $request->description,
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
    ], 201);
    }

    /**
     * Felhasználó bejelentkezése.
     */
public function login(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'passw' => 'required|string|min:8',
        'phoneNumb' => 'required|string|max:15',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)
                ->where('phoneNumb', $request->phoneNumb)
                ->first();

    if (!$user) {
        return response()->json(['message' => 'A megadott email vagy telefonszám nem található'], 404);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Hibás jelszó'], 401);
    }

    $token = $user->createToken('auth_token', ['*'])->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phoneNumb' => $user->phoneNumb,
        ],
    ], 200);
}

    /**
     * Felhasználó kijelentkezése (token érvénytelenítése).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sikeres kijelentkezés'], 200);
    }

    /**
     * Bejelentkezett felhasználó adatainak lekérése.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'phoneNumb' => $request->user()->phoneNumb,
            'city' => $request->user()->city,
            'gender' => $request->user()->gender,
            'description' => $request->user()->description,
        ], 200);
    }
}  