<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Slot;
use App\Event;
use App\Slottype;
use App\Document;
use DateTime;
use DB;

class DocumentController extends Controller
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
        $this->middleware('editor.event',       ['only' => ['create','store','edit','update','destroy']]);
        $this->middleware('relation.document',  ['only' => ['edit']]);
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
        $count = $event->documents->count() + 1;
        
        $data = [
            'page_title'        => 'Ajouter un document (' . $count . ')',
            'event'             => $event,
            'insert'            => true
        ];
        
        // return redirect('events/' . $event->id . '/slots/create');
        return view('document/edit2', $data);
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
            'file' => 'file|mimes:jpg,doc,docx,pdf|max:5120'
        ]);
        
        $document                    = new Document;
        $document->event_id         = $event->id;
        $document->filename         = $request->file('image')->getClientOriginalName();
        $document->data             = base64_encode(file_get_contents($request->file('image')->getRealPath()));
        $document->mime             = $request->file('image')->getMimeType();
        $document->size             = $request->file('image')->getSize();
        $document->save();
        
        $request->session()->flash('success', 'Le nouveau document a été enregistré !');
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
    public function edit(Event $event, Document $document)
    {       
        $data = [
            'page_title'        => 'Édition document',
            'insert'            => false,
            'document'          => $document
        ];
        
        return view('document/edit2', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event, Document $document)
    {
        $this->validate($request, [
            'file' => 'file|mimes:jpg,doc,docx,pdf|max:5120'
        ]);
    
        $document->filename         = $request->file('image')->getClientOriginalName();
        $document->data             = base64_encode(file_get_contents($request->file('image')->getRealPath()));
        $document->mime             = $request->file('image')->getMimeType();
        $document->size             = $request->file('image')->getSize();
        $document->save();
        
        $request->session()->flash('success', 'Le document a été mis à jour !');
        return redirect('events/' . $event->id . '/edit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event, Document $document)
    {
        $document->delete();
        
        return redirect('events/' . $event->id . '/edit');
    }
    
    public function doc($id)
    {
        $content    = base64_decode(Document::find($id)->data);
        $type       = Document::find($id)->mime;
        $filename   = Document::find($id)->filename;
        
        return response($content)
            ->header('Content-Type', $type)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
