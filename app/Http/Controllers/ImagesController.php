<?php

namespace App\Http\Controllers;

use App\Models\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allImages = Images::all();

        foreach ($allImages as $value) {
            $imagesLink[] = asset('storage/' . $value->name);
        }

        return response()->json([
            'data' => $imagesLink,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $images = $request->file('images');
        if ($images) {
            foreach ($images as $image) {
                // store('nome da pasta que será usada', o nome do disco)
                $path = $image->store('images', 'public');
                $images = Images::create(['name' => $path]);
            }

            return response()->json([
                'data' => 'Arquivo(s) salvo(s) com sucesso!'
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $img = Images::findOrFail($id);
            $imgLink = asset('storage/' . $img->name);

            return response()->json([
                'image_link' => $imgLink,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Imagem não encontrada',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $image = Images::findOrFail($id);

            // Removendo a imagem antiga do Storage
            Storage::disk('public')->delete($image->name);

            // Subindo uma nova imagem e atualizando o bando de dados
            $images = $request->file('images');
            foreach ($images as $img) {
                $path = $img->store('images', 'public');
                $image->update(['name' => $path]);
            }

            return response()->json([
                'data' => 'Arquivo atualizado com sucesso!'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Erro ao atualizar o arquivo'
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $image = Images::findOrFail($id);
            Storage::disk('public')->delete($image->name);
            $image->delete();

            return response()->json([
                'data' => 'Arquivo removido com sucesso!'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Erro ao remover o arquivo'
            ], 401);
        }
    }
}
