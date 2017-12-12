<?php

namespace App\Http\Controllers;

use DummyFullModelClass;
use Illuminate\Http\Request;

use App\Models\Post;
use Carbon\Carbon;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $posts = Post::where('published_at', '<=', Carbon::now())
                ->orderBy('published_at','desc')
                ->paginate(config('blog.posts_per_page'));

        return view('blog.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPost($slug){
        $post = Post::shereSlug($slug)->firstOrFail();
        return view('blog.post')->withPost($post);
    }
}
