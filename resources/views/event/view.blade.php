@extends('layout')

@section('content')

<div class="row hidden-sm hidden-xs">
    <div clss="col-lg-12">
        <ol class="breadcrumb">
            <li>Vous êtes ici: <a href="{{ url('/') }}">Calendrier</a></li>
            <li class="active">{{ $event->title }}</li>
        </ol>
    </div>
</div>
<td><span class="label label-default" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ $event->commune->name }}</span></td>
<h1>{{ $page_title }}</h1>
@include('message')

<div class="row">
    <div class="col-lg-4">
        <strong>Horaire(s)</strong>
    </div>
</div>
@if ( !$event->slots->count() )
    Votre événement n'a pas d'horaire.
@else
    @foreach ( $event->slots->sortBy('start_time') as $slot )
        <div class="row" style="color:{{ $slot->color3() }}; border-style: solid; border-width: 1px; border-color: {{ $slot->color2() }}; background-color: {{ $slot->color1() }};;">
        <div class="col-lg-2">
            {{ $slot->slottype->name }}
        </div>
        <div class="col-lg-4">
            <i class="fa fa-clock-o"></i>
            {{ date("d.m.Y\, G:i", strtotime($slot->start_time)) . ' - ' . date("d.m.Y\, G:i", strtotime($slot->end_time)) }}
        </div>
        <div class="col-lg-4">
            <i class="fa fa-map-marker"></i>
            {{ $slot->location }}
        </div>
        <div class="col-lg-2">
            <a class="btn btn-default btn-xs" href="{{ url('/') . '#year=' . date("Y", strtotime($slot->start_time)) . '&month=' . date("m", strtotime($slot->start_time)) .'&day=' . date("d", strtotime($slot->start_time)) . '&view=month' }}">
                                <span class="glyphicon glyphicon-calendar"></span> Calendrier</a>
        </div>
    </div>
    @endforeach
@endif

@if ( $event->documents->count() )
    <div class="row">
        <div class="col-lg-4">
            <strong>Document(s)</strong>
        </div>
    </div>
    @foreach ( $event->documents->sortBy('filename') as $document )
        <div class="row bg-warning">
            <div class="col-lg-6">
                    <a href="{{ url('doc/' . $document->id)}}">
                @if ( preg_match('/pdf/', $document->mime)) 
                    <i class="fa fa-file-pdf-o "></i>
                @elseif ( preg_match('/word/', $document->mime)) 
                    <i class="fa fa-file-word-o "></i>
                @elseif ( preg_match('/image/', $document->mime)) 
                    <i class="fa fa-file-image-o "></i>
                @else
                    <i class="fa fa-file-o "></i>
                @endif
                    {{ $document->filename }}
                </a>
            </div>
        </div>
    @endforeach
@endif

@if ( $event->logs->count() )
    <div class="row">
        <div class="col-lg-4">
            <strong>Entrée(s) au journal</strong>
        </div>
    </div>
    @foreach ( $event->logs->sortBy('updated_at') as $log )
        <div class="row bg-danger">
            <div class="col-lg-1">
                <i class="fa fa-user"></i>
                {{ $log->author }}
            </div>
            <div class="col-lg-3">
                <i class="fa fa-clock-o"></i>
                {{ $log->updated_at }}
            </div>
            <div class="col-lg-6">
                {{ $log->content }}
            </div>
        </div>
    @endforeach
@endif

<div class="row">
    <div class="col-lg-6">
        <p><strong>Organisation: </strong><br>
        {{ $event->organisation }}
        </p>
    </div>
</div>        
<div class="row">
    <div class="col-lg-6">
        <p><strong>Contact: </strong><br>
        {{ $event->contact_name }}
        </p>
    </div>
    <div class="col-lg-6">
        <p><strong>Tél. contact: </strong><br>
        {{ $event->contact_phone }}
        </p>
    </div>
</div>
@if ( $event->security_name or $event->security_phone )
    <div class="row">
    @if ( $event->security_name )
        <div class="col-lg-6">
            <p><strong>Sécurité: </strong><br>
            {{ $event->security_name }}
            </p>
        </div>
    @endif
    @if ( $event->security_phone )
        <div class="col-lg-6">
            <p><strong>Tél. sécurité: </strong><br>
            {{ $event->security_phone }}
            </p>
        </div>
    @endif
    </div>
@endif
@if ( $event->comment )
    <div class="row">
        <div class="col-lg-6">
            <p><strong>Commentaire: </strong><br>
            {!! nl2br($event->comment) !!}
            </p>
        </div>
    </div>
@endif
<div class="row">
    <div class="col-lg-6">
        <p><strong>Statut: </strong><br>
        {{ $event->Status->name }}
        </p>
    </div>
    @if ($event->eventtype_id == 2)
        <div class="col-lg-3">
            <p><strong>Date annonce: </strong><br>
            {{ $event->announcement_date }}
            </p>
        </div>
        <div class="col-lg-3">
            <p><strong>Date décision: </strong><br>
            {{ $event->decision_date }}
            </p>
        </div>
    @endif
</div>        
<div class="row">
    <div class="col-lg-6">
        <p><strong>Service responsable: </strong><br>
        {{ $event->Service->name }}
        </p>
    </div>
    @if(count($event->investments))
        <div class="col-lg-6">
            <strong>Plan des investissements:</strong><br>
        @foreach ($event->investments->sortBy("numero") as $investment)
            <p><a href="{{ url('pi#') }}{{ $investment->numero }}">
                <span class="label label-default" style="background-color: #AAAAAA; color: #FFFFFF;">PI {{ $investment->numero }}</span>
            {{ $investment->objet }}</a></p>
        @endforeach
        </div>
    @endif
</div>
@if (Auth::user()->isEventEditor($event->eventtype_id))
<div class="row">
    <div class="col-lg-3">        
            <a class="btn btn-primary" href="{{ url('events/' . $event->id . '/edit')}}">
                    <span class="glyphicon glyphicon-edit"></span> Éditer</a> 
    </div>
</div>
@endif
@endsection

@section('js')
<script src="{{ url('_asset/js') }}/daterangepicker.js"></script>
<script type="text/javascript">
$(function () {
    $('input[name="time"]').daterangepicker({
        "timePicker": true,
        "timePicker24Hour": true,
        "timePickerIncrement": 15,
        "autoApply": true,
        "locale": {
            "format": "DD/MM/YYYY HH:mm:ss",
            "separator": " - ",
        }
    });
});
</script>
@endsection