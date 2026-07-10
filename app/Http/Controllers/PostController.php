<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'images'    => 'nullable|array',
            'images.*'    => 'nullable|image'
        ]);

        $userId = Auth::id();

        DB::beginTransaction();

        try {
            $post = Post::create([
                'user_id' => $userId,
                'title' => $validated['title'],
                'content' => $validated['content'],
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

            return response()->json($post);


        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Не удалось создать пост с картинками.',
                'error' => $e->getMessage() // Уберите эту строку в продакшене из соображений безопасности
            ], 500);
        }

    }
}
