<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
      $this->middleware('auth');    
    }
    public function index()
    {
      $user=auth()->user();  
      $videos=[];  
      $friends=$user->profile->users;
      if($friends){
        foreach($friends as $friend){
            $videos=$friend->videos;
            foreach($videos as $video){
              array_push($videos,$video);
            }  
        }  
      }
      $groups=$user->groups;
      if($groups){
        foreach($groups as $group){
            $videos=$group->videos;
            foreach($videos as $video){
              array_push($videos,$video);
            }  
        }  
      }
      $channels=$user->channels;
      if($channels){
        foreach($channels as $channel){
            $videos=$channel->videos;
            foreach($videos as $video){
              array_push($videos,$video);
            }  
        }  
      }
      return view('video.index',['videos'=>$videos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('video.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVideoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVideoRequest $request)
    {
      $video=new Video();
      $video->title=$request->title;
      $video->description=$request->description;
      $video->save();
      $video->photo=$video->id.$request->cover->extension();
      $video->source=$video->id.$request->video->extension();
      $video->save();
      $request->cover->storeAs('photo',$video->photo,'public');
      $request->video->storeAs('video',$video->source,'public');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
      
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
      return view('video.edit',['video'=>$video]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVideoRequest  $request
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVideoRequest $request, Video $video)
    {
      $video->title=$request->title;
      $video->description=$request->description;
      $video->save();
      if($request->cover){
        Storage::delete("/storage/post/{{$video->cover}}");
        $video->photo=$video->id.$request->cover->extension();
        $request->cover->storeAs('photo',$video->photo,'public');
      }
      if($request->video){
        Storage::delete("/storage/video/{{$video->source}}");
        $video->source=$video->id.$request->video->extension();
        $request->video->storeAs('video',$video->source,'public');
      }
      $video->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        //
    }
}