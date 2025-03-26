<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::all();

        return response()->json([
            'status' => true,
            'message' => 'Posts Fetched Successfully',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateUser= Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required'
            ]);
            if($validateUser->fails()){
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Validation Error',
                        'errors' => $validateUser->errors()->all(),
    
                    ], 422
                );
            }
            $img = $request->image;
            $ext= $img->getClientOriginalExtension();
            $imageName = time().'.'.$ext;
            $img->move(public_path('images'), $imageName);
            $post = Post::create([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $imageName,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Post Created Successfully',
                'post' => $post
            ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    $data['post'] = Post::select(
        'id',
        'title',
        'description',
        'image'
    )->where('id', $id)->get();
    return response()->json([
        'status' => true,
        'message' => 'Single Post Fetched Successfully',
        'data' => $data
    ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateUser= Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required'
            ]);
            if($validateUser->fails()){
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Validation Error',
                        'errors' => $validateUser->errors()->all(),
    
                    ], 422
                );
            }
            $postImage = Post::select('id', 'image')->where('id', $id)->get();
            if($request->image != ''){
                $path = public_path(). '/uploads';
                if($postImage[0]->image != '' && $postImage[0]->image != null){
                    $file_old = $path . $postImage[0]->image;
                    if(file_exists($file_old)){
                        unlink($file_old);
                    }
                }

            $img = $request->image;
            $ext= $img->getClientOriginalExtension();
            $imageName = time().'.'.$ext;
            $img->move(public_path().'/uploads', $imageName);

            }else{
                $imageName = $postImage->image;
            }
            $post = Post::where('id', $id)->update([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $imageName,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Post Updated Successfully',
                'post' => $post
            ], 200);
   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagePath = Post::select('image')->where('id', $id)->get();
        $filePath = public_path().'/uploads/'.$imagePath[0]['image'];
        unlink($filePath);
        $post = Post::where('id', $id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Post Deleted Successfully',
            'post' => $post
        ], 200);
     
    }
}
