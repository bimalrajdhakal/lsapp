<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Post;   // using post model 
use DB; // To use SQL Queries 

class PostsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth',['except'=>['index','show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // to display post from database to html page
        //$posts = DB::select('SELECT * FROM posts'); // select sql query
        //$posts = Post::where('title','Post Two')->get(); // where statement 
        //$posts = Post::all();  // selecting all 
        // creating posts variable to store returned data 
        //$posts = Post::orderBy('title','desc')->take(1)->get(); //limiting data 
        //$posts = Post::orderBy('title','desc')->get(); //orderby statement 
        $posts = Post::orderBy('created_at','desc')->paginate(10); //Pagination
        return view('posts.index')->with('posts',$posts); //returning data to the view
        // fetching all data from database using ORM method rather than SQL
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // creating post and returning create post view
        return view('posts.createPost');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // storing data from showPost view 
        $this->validate($request,[
            'title'=>'required',
            'body'=>'required',
            'cover_image'=>'image|nullable|max:1999'
        ]);

        //Handle file upload
        if($request->hasFile('cover_image')){
            // get file name with extension
            $fileNameWithExt = $request->file('cover_image')->getClientOriginalName();
            // get just file name
            $filename = pathinfo($fileNameWithExt,PATHINFO_FILENAME);
            // get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // file name to store
            $fileNameToStore =$filename.'_'.time().'.'.$extension;
            // upload image
            $path = $request->file('cover_image')->storeAs('public/cover_images',$fileNameToStore);

        }else{
            $fileNameToStore = 'noimage.jpg';
        }

        // storing data to DB
        $post = new Post;
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = auth()->user()->id;
        // inserting user id from logged in user 
        $post->cover_image = $fileNameToStore;
        $post->save();
            // Redirecting page to post
        return redirect('/posts')->with('success','Post created successfully!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // displaying post by id 
        $post = Post::find($id);
        return view('posts.showPost')->with('post',$post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //rendering data to edit view 

        $post = Post::find($id);
        // check for correct user
        if(auth()->user()->id !==$post->user_id){
            return redirect('/posts')->with('error','Unauthized Access!');
        }
        return view('posts.editPost')->with('post',$post);
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
        // updating populated values in view
        $this->validate($request,[
            'title'=>'required',
            'body'=>'required'
        ]);

        //Handle file upload
        if($request->hasFile('cover_image')){
        // get file name with extension
        $fileNameWithExt = $request->file('cover_image')->getClientOriginalName();
        // get just file name
        $filename = pathinfo($fileNameWithExt,PATHINFO_FILENAME);
        // get just extension
        $extension = $request->file('cover_image')->getClientOriginalExtension();
        // file name to store
        $fileNameToStore =$filename.'_'.time().'.'.$extension;
        // upload image
        $path = $request->file('cover_image')->storeAs('public/cover_images',$fileNameToStore);
        
        }
        // storing data to DB
        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        if($request->hasFile('cover_image')){
            $post->cover_image = $fileNameToStore;
        }
        $post->save();
            // Redirecting page to post
        return redirect('/posts')->with('success','Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //deleting post from DB

        $post = Post::find($id);
        // check for correct user
        if(auth()->user()->id !==$post->user_id){
            return redirect('/posts')->with('error','Unauthized Access!');
        }

        if($post->cover_image != 'noimage.jpg'){
            // delete image from storage folder
            Storage::delete('/public/cover_images/'.$post->cover_image);

        }

        $post->delete();
        return redirect('/posts')->with('success','Post Deleted successfully!');
    }
}
