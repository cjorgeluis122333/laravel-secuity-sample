<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\PostRequest;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     * Get all post
     */
    public function index()
    {
        try {
            $posts = Post::with('user', 'comments.user')
                ->orderBy('published_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $posts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los posts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Insert new post
     */
    public function store(PostRequest $request)
    {
        try {

            $post = Post::create([
                'user_id' => Auth::id(),
                'title' => $request['title'],
                'content' => $request['content'],
                'published_at' => $request['published_at'] ?? now(),
                'status' => $request['status'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Post creado exitosamente',
                'data' => $post->load('user'),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validacion',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * Get posts by id
     */
    public function show(string $id)
    {
        try {
            $post = Post::with('user', 'comments.user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $post,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $post = Post::findOrFail($id);

            // Verificar que el usuario es el propietario del post
            if ($post->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para actualizar este post',
                ], 403);
            }

            $validated = $request->validate([
                'title' => ['sometimes', 'string', 'max:255'],
                'content' => ['sometimes', 'string'],
                'published_at' => ['sometimes', 'nullable', 'date'],
                'status' => ['sometimes', 'in:draft,published,archived'],
            ]);

            $post->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Post actualizado exitosamente',
                'data' => $post->load('user'),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post no encontrado',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validacion',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $post = Post::findOrFail($id);

            // Verificar que el usuario es el propietario del post
            if ($post->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este post',
                ], 403);
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post eliminado exitosamente',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
