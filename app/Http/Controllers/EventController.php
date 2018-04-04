<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use App\Http\Requests;

use DB;
use App\Event;
use App\Eventtype;
use App\Service;
use App\Status;
use App\Commune;
use Response;
use PDF;

use DateTime;

class EventController extends Controller
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
        $this->middleware('reader.event',   ['only' => ['show']]);
        $this->middleware('editor',         ['only' => ['create','store']]);
        $this->middleware('editor.event',   ['only' => ['edit','update','destroy']]);
    }

    // Liste des événements
    public function index($eventtype,$listtype)
    {
        $events = Event::where('eventtype_id','=',$eventtype)->get()->sortBy('starting_date');

        foreach($events as $event)
        {

            $event->project = $event->status->project;

            $event->slotsByType = DB::table('slots')
                 ->join('slottypes', 'slottype_id', '=', 'slottypes.id')
                 ->where('event_id','=',$event->id)
                 ->select('slottype_id', DB::raw('count(*) as total'), 'color1', 'color2', 'color3')
                 ->groupBy('slottype_id', 'color1', 'color2', 'color3')
                 ->orderBy('slottype_id')
                 ->get();
        }
        if ($listtype == 'old') {
            $events = $events->where('ending_date','<',date("Y-m-d H:i:s"));
            $page_title = 'Liste: ' . Eventtype::find($eventtype)->namePluralWithLabel() . ' (anciens)';
        }
        else {
            $events = $events->where('ending_date','>=',date("Y-m-d H:i:s"));
            $page_title = 'Liste: ' . Eventtype::find($eventtype)->namePluralWithLabel();
        }

        //Custom Pagination
        //Get current page form url e.g. &page=6
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        //Create a new Laravel collection from the array data
        $collection = new Collection($events);

        //Define how many items we want to be visible in each page
        $perPage = 30;

        //Slice the collection to get the items to display in current page
        $currentPageSearchResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        //Create our paginator and pass it to the view
        $paginatedSearchResults= new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);

        $data = [
            'page_title'                => $page_title,
            // 'page_title_with_label'     => $page_title_with_label,
            'events'                    => $paginatedSearchResults,
            ];

        return view('event/list', $data);
    }

    // Liste des chantiers
    public function index_works($service = null)
    {
        $eventtype = 4;
        if (!is_null($service)) {
            $events = Event::where('eventtype_id','=',$eventtype)->where('service_id','=',$service)->get();
            $page_title = 'Liste: ' . Eventtype::find($eventtype)->namePluralWithLabel() . ' ' . Service::find($service)->name;
        }
        else {
            $events = Event::where('eventtype_id','=',$eventtype)->get();
            $page_title = 'Liste: ' . Eventtype::find($eventtype)->namePluralWithLabel();
        }

        foreach($events as $event)
        {
            $event->project = $event->status->project;
        }
        $events = $events->sortBy(function($event) { return $event->service->name .'-'. $event->status->idx .'-'. (is_Null($event->starting_date) ? 9999 : $event->starting_date) .'-'. $event->title; } );

        $data = [
            'page_title'    => $page_title,
            'events'        => $events,
            ];

        return view('event/list_works', $data);
    }

    // Liste des chantiers en lien avec l'AggloY
    public function index_works_aggloy($service = null)
    {
        $eventtype = 4;

        if (!is_null($service)) {
            $events = Event::where(function ($query) use ($eventtype) {
                $query  ->where('eventtype_id','=',$eventtype)
                        ->whereNotNull('aggloy')
                        ->orWhereNotNull('aggloy4');
            })->where('service_id','=',$service)->get();
            $page_title = 'Liste: ' . Eventtype::find($eventtype)->namePluralWithLabel() . ' ' . Service::find($service)->name . ' (en lien avec AggloY)';
            }
        else {
            $events = Event::where('eventtype_id','=',$eventtype)->whereNotNull('aggloy')->orWhereNotNull('aggloy4')->get();
            $page_title = 'Liste: ' . Eventtype::find($eventtype)->namePluralWithLabel() . ' (en lien avec AggloY)';
        }

        foreach($events as $event)
        {
            $event->project = $event->status->project;
        }

        $events = $events->sortBy(function($event) { return $event->service->name .'-'. $event->status->idx .'-'. (is_Null($event->starting_date) ? 9999 : $event->starting_date) .'-'. $event->title; } );

        $data = [
            'page_title'    => $page_title,
            'events'        => $events,
            ];

        return view('event/list_works_aggloy', $data);
    }

    // Liste des chantiers PDF
    public function index_works_pdf($eventtype = 4)
    {
        $events = Event::with('startingDate')->where('eventtype_id','=',$eventtype)->get();

        foreach($events as $event)
        {
            $event->startDate = $event->getStartingDate();
            $event->endDate = $event->getEndingDate();
            $event->project = $event->status->project;
        }

        $events = $events->sortBy(function($event) { return $event->service->name .'-'. $event->status->idx .'-'. $event->title; } );
        $page_title = 'Liste: ' . Eventtype::find($eventtype)->name_plural;

        $data = [
            'page_title'    => $page_title,
            'events'        => $events,
            ];


        $view = \View::make('event/list_works_pdf', $data);
        $html = $view->render();

        PDF::SetTitle('Hello World');
        PDF::AddPage();
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('hello_world.pdf');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // Ajouter un événement
    public function create($eventtype = 2)
    {
        $data = [
            'page_title'        => 'Ajouter: ' . Eventtype::find($eventtype)->name,
            'services'          => Eventtype::find($eventtype)->Services->pluck('name','id'),
            'status'            => Eventtype::find($eventtype)->Status->pluck('name','id'),
            'communes'          => Eventtype::find($eventtype)->Communes->pluck('name','id'),
            'eventtype'         => Eventtype::find($eventtype),
            'insert'            => true
        ];

        return view('event/edit2', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // Appelé par: Ajouter un événement
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'=> 'required|min:5|max:100',
            'organisation'=> 'required|min:5|max:100',
            'contact_name'=> 'required|min:5|max:100',
            'contact_phone'=> 'required|min:5|max:100',
            'status_id'=> 'required',
            'service_id'=> 'required',
            'commune_id'=> 'required',
            'aggloy_amount'=> 'integer',
        ]);

        $event                          = new Event;
        $event->eventtype_id            = $request->input('eventtype_id');
        $event->title                   = $request->input('title');
        $event->organisation            = $request->input('organisation');
        $event->contact_name            = $request->input('contact_name');
        $event->contact_phone           = $request->input('contact_phone');
        $event->security_name           = $request->input('security_name');
        $event->security_phone          = $request->input('security_phone');
        $event->aggloy                  = $request->input('aggloy');
        $event->aggloy4                 = $request->input('aggloy4');
        $event->aggloy_name             = $request->input('aggloy_name');
        $event->aggloy_amount           = $request->input('aggloy_amount') == '' ? null : $request->input('aggloy_amount');
        $event->comment                 = $request->input('comment');
        $event->status_id               = $request->input('status_id');
        $event->announcement_date       = $this->change_date_format2($request->input('announcement_date'));
        $event->decision_date           = $this->change_date_format2($request->input('decision_date'));
        $event->service_id              = $request->input('service_id');
        $event->commune_id              = $request->input('commune_id');
        $event->save();

        $request->session()->flash('success', 'Le nouvel événement a été enregistré !');
        // return redirect('events/create');
        return redirect('events/' . $event->id . '/edit');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // Lecture d'un événement
    public function show(Event $event)
    {
        $event->announcement_date =  $this->change_date_format_fullcalendar2($event->announcement_date);
        $event->decision_date = $this->change_date_format_fullcalendar2($event->decision_date);

        $data = [
            'page_title'     => $event->title . ' (' . $event->eventtype->name . ')',
            'event'          => $event,
        ];

        return view('event/view', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // Édition
    public function edit(Event $event)
    {
        $event->announcement_date =  $this->change_date_format_fullcalendar2($event->announcement_date);
        $event->decision_date =  $this->change_date_format_fullcalendar2($event->decision_date);

        $data = [
            'page_title'        => 'Édition: '. $event->title . ' (' . $event->eventtype->name . ')',
            'services'          => $event->eventtype->services->pluck('name','id'),
            'status'            => $event->eventtype->status->pluck('name','id'),
            'communes'          => $event->eventtype->communes->pluck('name','id'),
            'eventtype'         => $event->eventtype,
            'eventtypes'        => $event->status->eventtypes->pluck('name','id'),
            'insert'            => false,
            'event'             => $event
        ];

        return view('event/edit2', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // Appelé par: Édition
    public function update(Request $request, Event $event)
    {
        $this->validate($request, [
            'title'=> 'required|min:5|max:100',
            'organisation'=> 'required|min:5|max:100',
            'contact_name'=> 'required|min:5|max:100',
            'contact_phone'=> 'required|min:5|max:100',
            'status_id'=> 'required',
            'service_id'=> 'required',
            'eventtype_id'=> 'required',
            'commune_id'=> 'required',
            'aggloy_amount'=> 'integer',
        ]);

        $event->eventtype_id            = $request->input('eventtype_id');
        $event->title                   = $request->input('title');
        $event->organisation            = $request->input('organisation');
        $event->contact_name            = $request->input('contact_name');
        $event->contact_phone           = $request->input('contact_phone');
        $event->security_name           = $request->input('security_name');
        $event->security_phone          = $request->input('security_phone');
        $event->aggloy                  = $request->input('aggloy');
        $event->aggloy4                 = $request->input('aggloy4');
        $event->aggloy_name             = $request->input('aggloy_name');
        $event->aggloy_amount           = $request->input('aggloy_amount') == '' ? null : $request->input('aggloy_amount');
        $event->comment                 = $request->input('comment');
        $event->status_id               = $request->input('status_id');
        $event->announcement_date       = $this->change_date_format2($request->input('announcement_date'));
        $event->decision_date           = $this->change_date_format2($request->input('decision_date'));
        $event->service_id              = $request->input('service_id');
        $event->commune_id              = $request->input('commune_id');
        $event->save();

        $request->session()->flash('success', 'L\'événement a été mis à jour !');

        return redirect('events/' . $event->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // Supprimer
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect('events/' . $event->eventtype_id . '/old');
    }


    public function getdata($term)
    {
        $data = array(
            'R' => 'Red',
            'O' => 'Orange',
            'Y' => 'Yellow',
            'G' => 'Green',
            'B' => 'Blue',
            'I' => 'Indigo',
            'V' => 'Violet',
        );
        $return_array = array();

        foreach ($data as $k => $v) {
            if (strpos($v, $term) !== FALSE) {
                $return_array[] = array('value' => $v, 'id' =>$k);
            }
        }
        return Response::json($return_array);
    }


    public function change_date_format($date)
    {
        $time = DateTime::createFromFormat('d/m/Y H:i:s', $date);
        return $time->format('Y-m-d H:i:s');
    }

    public function change_date_format2($date)
    {
        if ($date) {
            $time = DateTime::createFromFormat('d/m/Y', $date);
            return $time->format('Y-m-d');
        }
    }

    public function change_date_format_fullcalendar($date)
    {
        $time = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $time->format('d/m/Y H:i:s');
    }

    public function change_date_format_fullcalendar2($date)
    {
        if ($date) {
            $time = DateTime::createFromFormat('Y-m-d', $date);
            return $time->format('d/m/Y');
        }
    }

    public function format_interval(\DateInterval $interval)
    {
        $result = "";
        if ($interval->y) { $result .= $interval->format("%y année(s) "); }
        if ($interval->m) { $result .= $interval->format("%m mois(s) "); }
        if ($interval->d) { $result .= $interval->format("%d jour(s) "); }
        if ($interval->h) { $result .= $interval->format("%h heure(s) "); }
        if ($interval->i) { $result .= $interval->format("%i minute(s) "); }
        if ($interval->s) { $result .= $interval->format("%s seconde(s) "); }

        return $result;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // Appelé par: Ajouter un événement
    public function event_fulltextsearch(Request $request)
    {
        $events = Event::search($request->input('term'))->get();
        $ui = array();

        foreach($events as $event)
        {
           array_push($ui, (object) array(
              'label' => $event->title,
              'value' => $event->id,
              'last_update' => $event->updated_at->format("d.m.Y")
            ));
        }
        return $ui;

    }
}
