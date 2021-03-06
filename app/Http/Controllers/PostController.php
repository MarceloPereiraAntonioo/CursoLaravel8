<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::paginate();
     
        return view ('admin.posts.index', compact('posts'));
    }

  public function create()
  {
      return view ('admin.posts.create');
  }

  public function store(StoreUpdateRequest $request)
  {
    $data = $request->all();

    if($request->image->isValid())
      {
        $nameFile= Str::of($request->title)->slug('-'). '.' .$request->image->getClientOriginalExtension();
        $image = $request->image->storeAs('public/posts', $nameFile);
        $image = str_replace('public/','', $nameFile);
        $data['image'] = $image;
      }

   $post = Post::create($data);
   return redirect()
            ->route('posts.index')
            ->with('message', 'Post criado com sucesso');
  }

  public function show($id)
  {
      
    if(!$post = Post::find($id))
        {
            return redirect()->route('posts.index');
        }

     return view('admin.posts.show', compact('post'));
    
  }
  public function destroy($id)
  {
     if(!$post = Post::find($id))
        return redirect()->route('posts.index');
        if(Storage::exists('public/posts/'.$post->image))
           Storage::delete('public/posts/'.$post->image);
        $post->delete();
        return redirect()
            ->route('posts.index')
            ->with('message', 'Post deletado com sucesso');
    

  }
  public function edit($id)
  {
    if(!$post = Post::find($id))
    {
    return redirect()->back();
    }
    return view('admin.posts.edit', compact('post'));

  }
  public function update(StoreUpdateRequest $request, $id)
  {
    
    if(!$post = Post::find($id)){
        return redirect()->back();
    }
    $data = $request->all();  
      
    if($request->image && $request->image->isValid())
      {
        if(Storage::exists('public/posts/'.$post->image))
          Storage::delete('public/posts/'.$post->image);

          $nameFile= Str::of($request->title)->slug('-'). '.' .$request->image->getClientOriginalExtension();
          $image = $request->image->storeAs('public/posts', $nameFile);
          $image = str_replace('public/','', $nameFile);
          $data['image'] = $image;
      }
    
    $post->update($data);
    return redirect()
            ->route('posts.index')
            ->with('message', 'Post atualizado com sucesso');
  }

  public function search(Request $request)
  {
    $filters = $request->except('_token');
    $posts = Post::where('title', 'LIKE', "%$request->search%")
                    ->paginate(1);
      return view('admin.posts.index', compact('posts', 'filters'));
  }

}
