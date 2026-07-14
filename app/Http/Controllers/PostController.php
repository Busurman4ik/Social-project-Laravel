<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('images')->where('user_id', auth()->id())->latest()->get();
        return PostResource::collection($posts);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'images'    => 'nullable|array',
            'images.*'    => 'nullable|image'
        ]);

        $userId = Auth::id();

        DB::beginTransaction();

        try {
            $post = Post::create([
                'user_id' => $userId,
                'title' => $validated['title'],
                'content' => $validated['content'] ?? '',
            ]);


            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('post_images', 'public');
                    $post->images()->create([
                        'path'    => $imagePath,
                        'user_id' => $userId,
                    ]);
                }

                $post->load('images');
            }

            DB::commit();

            return new PostResource($post);


        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Не удалось создать пост с картинками.',
                'error' => $e->getMessage()
            ], 500);
        }

    }
}
