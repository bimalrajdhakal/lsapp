<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PagesController extends Controller
{
    /* index page */
    public function index(){
        $title = 'Welcome to Laravel!';
        // return view('pages.index',compact('title'));
        // sending parameter to template [single value allowed]
        return view('pages.index')->with('title',$title); 
        // sending parameter to template [multiple parameters(values) allowed]
    }
    /* about page */
    public function about(){
        $title = 'About Us';
        //return (string) Str::uuid();
        //return (string) Str::orderedUuid();
        return view('pages.about')->with('title',$title);
    }
    /* service page */
    public function services(){
        $data = array(
            'title' => 'Services',
            'services' => ['Web Designing','Software Development','Consulatation','SEO']
        );
        return view('pages.services')->with($data);
    }

}
