<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


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
        'name' => 'required|string|max:50',
        'email' => 'required|string|email|max:70|unique:users',
        'passw' => 'required|string|min:8',
        'phoneNumb' => 'required|string|max:12|unique:users',
        'city' => 'required|string|max:80',
        'gender' => 'required|string|max:15',
        'description' => 'nullable|string|max:255',
        'imageId' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Kép validálása
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Kép mentése
    $imageId = null;
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('images', 'public');
        $image = Image::create(['path' => $path]);
        $imageId = $image->id;
    }

    // Felhasználó létrehozása
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'passw' => Hash::make($request->passw), // Jelszó hash-elése
        'phoneNumb' => $request->phoneNumb,
        'city' => $request->city,
        'gender' => $request->gender,
        'description' => $request->description,
        'imageId' => $imageId, // Kép azonosító mentése
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => [
            'id' => $user->userId, // Az adatbázisban az elsődleges kulcs neve userId
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

    if (!Hash::check($request->passw, $user->passw)) {
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
            'imageId' => $request->user()->imageId,
            'created_at' => $request->user()->created_at,
            'updated_at' => $request->user()->updated_at,
            'image' => $request->user()->image ? asset('storage/' . $request->user()->image->path) : null,
        ], 200);
    }
    
}

class LikeController extends Controller
{
    // Létrehozás vagy frissítés (like/dislike)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'liked' => 'required|boolean',
        ]);

        $like = Like::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'post_id' => $validated['post_id'],
            ],
            [
                'liked' => $validated['liked'],
            ]
        );

        return response()->json(['success' => true, 'like' => $like]);
    }

    // Like/dislike szám lekérdezése
    public function getLikes($postId)
    {
        $likes = Like::where('post_id', $postId)->get();

        return response()->json([
            'likes' => $likes->where('liked', true)->count(),
            'dislikes' => $likes->where('liked', false)->count()
        ]);
    }
}
// Noti kezelés
class NotificationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Feltételezzük, hogy van egy "notifications" tábla
        $notifications = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['notifications' => $notifications]);
    }
}