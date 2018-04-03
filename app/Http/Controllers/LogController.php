<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests;

use App\Log;
use App\Event;
use App\User;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() 
    {
        $this->middleware('auth');
        $this->middleware('reader');
        $this->middleware('editor.event',   ['only' => ['create','store','edit','update','destroy']]);
        $this->middleware('relation.log',   ['only' => ['edit']]);
    }
    
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event)
    {
        $count = $event->logs->count() + 1;
        
        $data = [
            'page_title'        => 'Ajouter une entrée au journal (' . $count . ')',
            'event'             => $event,
            'insert'            => true
        ];
        
        return view('log/edit2', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Event $event)
    {
        $this->validate($request, [
            'content'=> 'required|min:5',
        ]);
        
        $user = Auth::user();
        
        $log            = new Log;
        $log->event_id  = $event->id;
        $log->content   = $request->input('content');
        $log->author    = $user->username;
        $log->save();
        
        $request->session()->flash('success', 'Le nouveau log a été enregistré !');
        return redirect('events/' . $event->id . '/edit');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event, Log $log)
    {
        $data = [
            'page_title'        => 'Édition entrée au journal',
            'insert'            => false,
            'log'               => $log
        ];
        
        return view('log/edit2', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event, Log $log)
    {
        $this->validate($request, [
            'content'=> 'required|min:5',
        ]);
        
        $user = Auth::user();
        
        $log->content   = $request->input('content');
        $log->author    = $user->username;
        $log->save();
        
        $request->session()->flash('success', 'Le log a été mis à jour !');
        return redirect('events/' . $event->id . '/edit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event, Log $log)
    {
        $log->delete();
        
        return redirect('events/' . $event->id . '/edit');
    }
}
