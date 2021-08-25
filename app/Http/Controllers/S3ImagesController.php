<?php

namespace App\Http\Controllers;

use App\Models\S3Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class S3ImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allImages = Storage::disk('s3')->files('images');
        if($allImages){
            foreach ($allImages as $img) {
                $images_link[] = Storage::disk('s3')->temporaryUrl($img, now()->addMinutes(2));
            }

            return response()->json([
                'all_images' => $images_link
            ], 200);
        }else{
            return response()->json([
                'msg' => 'Não existem imagens salvas.'
            ], 401);

        }
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
            foreach ($images as  $image) {
                $path = $image->store('images', 's3');
                $images = S3Image::create(['name' => $path]);
            }
            return response()->json([
                'data' => 'Arquivo(s) salvo(s) com sucesso na Amazon S3!'
            ], 201);
        }
        return response()->json([
            'msg' => 'Nenhum arquivo foi salvo'
        ], 401);
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
            $img = S3Image::findOrFail($id);
            $imgLink = Storage::disk('s3')->temporaryUrl($img->name, now()->addMinutes(2));

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
            $image = S3Image::findOrFail($id);

            // Removendo a imagem antiga do Amazon S3
            Storage::disk('s3')->delete($image->name);

            // Subindo do request uma nova imagem
            $images = $request->file('images');

            // Atualizando o Amazon S3 e o banco de dados
            foreach ($images as $img) {
                $path = $img->store('images', 's3');
                $image->update(['name' => $path]);
            }

            return response()->json([
                'data' => 'Arquivo do Amazon s3 atualizado com sucesso!'
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
            $img = S3Image::findOrFail($id);
            Storage::disk('s3')->delete($img->name);
            $img->delete();
            return response()->json([
                'msg' => 'Removido com sucesso do Amazon S3',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Imagem não encontrada',
            ]);
        }
    }
}
