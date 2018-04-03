<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Requests;

use App\User;
use App\Slot;
use App\Event;
use App\Slottype;
use DateTime;
use DB;

class SlotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth',           ['except' => ['ical']]);
        $this->middleware('reader',         ['except' => ['ical']]);
        $this->middleware('editor.event',   ['only' => ['create','store','edit','update','destroy']]);
        $this->middleware('key',            ['only' => ['ical']]);
        $this->middleware('relation.slot',       ['only' => ['edit']]);
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
        $count = $event->slots->count() + 1;
        $slottypes = $event->Eventtype->Slottypes->pluck('name','id');

        $data = [
            'page_title'        => 'Ajouter un horaire (' . $count . ')',
            'slottypes'         => $slottypes,
            'event'             => $event,
            'insert'            => true
        ];

        // return redirect('events/' . $event->id . '/slots/create');
        return view('slot/edit2', $data);
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
            'location'=> 'required|min:5|max:100',
            'time' => 'required',
            'slottype_id'=> 'required',
        ]);
        $time = explode(" - ", $request->input('time'));

        $slot                          = new Slot;
        $slot->event_id                = $event->id;
        $slot->start_time              = $this->change_date_format($time[0]);
        $slot->end_time                = $this->change_date_format($time[1]);
        $slot->location                = $request->input('location');
        $slot->slottype_id             = $request->input('slottype_id');
        $slot->save();

        $request->session()->flash('success', 'Le nouvel horaire a été enregistré !');
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
    public function edit(Event $event, Slot $slot)
    {
        $slot->start_time =  $this->change_date_format_fullcalendar($slot->start_time);
        $slot->end_time =  $this->change_date_format_fullcalendar($slot->end_time);
        $slottypes = $event->Eventtype->Slottypes->pluck('name','id');

        $data = [
            'page_title'        => 'Édition horaire',
            'slottypes'         => $slottypes,
            // 'police_slottypes'  => Slottype::where('police',true)->pluck('name','id'),
            'insert'            => false,
            'slot'              => $slot
        ];

        return view('slot/edit2', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event, Slot $slot)
    {
        $this->validate($request, [
            'location'=> 'required|min:5|max:100',
            'time' => 'required',
            'slottype_id'=> 'required',
        ]);

        $time = explode(" - ", $request->input('time'));

        $slot->start_time              = $this->change_date_format($time[0]);
        $slot->end_time                = $this->change_date_format($time[1]);
        $slot->location                = $request->input('location');
        $slot->slottype_id             = $request->input('slottype_id');
        $slot->save();

        $request->session()->flash('success', 'L\'horaire a été mis à jour !');
        return redirect('events/' . $event->id . '/edit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event, Slot $slot)
    {
        $slot->delete();

        return redirect('events/' . $event->id . '/edit');
    }

    /**
     * Fetches the slots from database and performs some fomatting.
     *
     * @param  \Illuminate\Http\Request  $eventtype
     * @return \Illuminate\Http\Response
     */
    public function api2($eventtype)
    {
        $query = Slot::whereHas('event',function($query) use ($eventtype) {
            $query->whereHas('eventtype',function($query) use ($eventtype) {
                $query->where('id','=',$eventtype);
            });
        })->whereHas('slottype',function($query) {
                    $query->where('visible','=',true);
        })->orderBy('start_time');

        Log::info(get_class($this) . ' : QUERY SENT TO DB WITH EVENTYPE ID : ' . $eventtype);
        $slots = $query->get();
        Log::info(get_class($this) . ' : DATA FETCHED FROM DB');

        foreach($slots as $slot)
        {
            $slot->title = ($slot->event->eventtype->label <> '' ? $slot->event->eventtype->label . ' ' : '')
                                . $slot->event->title
                                . ' - '
                                . $slot->location
                                . ($slot->event->status->label <> '' ? ' - ' . $slot->event->status->label : '')
                                . ($slot->slottype->label <> '' ? ' - ' . $slot->slottype->label : '');
            $slot->url = url('events/' . $slot->event->id);
            $slot->start = $slot->start_time;
            $slot->end = $slot->end_time;

            $slot->backgroundColor = $slot->color1();
            $slot->borderColor = $slot->color2();
            $slot->textColor = $slot->color3();
        }
        Log::info(get_class($this) . ' : DATA SENT TO THE VIEW');
        return $slots;
    }

    public function ical($key,$username)
    {

        $eventtypes = User::where('username','=',$username)->firstOrFail()->eventtypesReadable()->pluck('id');

        $slots = Slot::whereHas('event', function($query) use ($eventtypes) {
                            $query->whereHas('eventtype', function($query) use ($eventtypes) {
                                $query->whereIn('id', $eventtypes);
                            });
                        })
                        ->orderBy('start_time')
                        ->get();
        $vCalendar = new \Eluceo\iCal\Component\Calendar('mapnv.ch');
        $vCalendar->setPublishedTTL('PT2H');
        foreach($slots as $slot)
        {
            $vEvent = new \Eluceo\iCal\Component\Event();
            $vEvent->setDtStart(new \DateTime($slot->start_time));
            $vEvent->setDtEnd(new \DateTime($slot->end_time));
            $vEvent->setNoTime(false);
            $vEvent->setSummary($slot->event->title . ' - ' . $slot->event->organisation);
            $vEvent->setCategories([$slot->event->Status->name]);
            $vEvent->setUseUtc(false);
            $vCalendar->addComponent($vEvent);
        }
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="yvent_'. $username .'.ics"');
        return $vCalendar->render();
    }

    public function change_date_format($date)
    {
        $time = DateTime::createFromFormat('d/m/Y H:i:s', $date);
        return $time->format('Y-m-d H:i:s');
    }

    public function change_date_format2($date)
    {
        $time = DateTime::createFromFormat('d/m/Y', $date);
        return $time->format('Y-m-d');
    }

    public function change_date_format_fullcalendar($date)
    {
        $time = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $time->format('d/m/Y H:i:s');
    }

    public function change_date_format_fullcalendar2($date)
    {
        $time = DateTime::createFromFormat('Y-m-d', $date);
        return $time->format('d/m/Y');
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
}
