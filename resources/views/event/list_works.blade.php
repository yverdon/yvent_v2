@extends('layout')

@section('content')

<div class="row hidden-sm hidden-xs">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li>Vous êtes ici: <a href="{{ url('/') }}">Calendrier</a></li>
            <li class="active"><a href="{{ url('/events') }}">{{ $page_title }}</a></li>
        </ol>
    </div>
</div>
<h1>{{ $page_title }}</h1>
<div class="row">
    <div class="col-lg-12 table-responsive">
        @if($events->count() > 0)
        <table class="table table-striped table-condensed" style="font-size: 12px;">
            <thead>
                <tr>
                    <th></th>
                    <th>Statut</th>
                    <th>Titre</th>
                    <th>Commentaire</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Contact</th>
                    <th>Partenaires</th>
                    <th>PI</th>
                    <th>Carte</th>
                    @if (Auth::user()->isEditor())
                        <th>Modifier</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            @php
                $i = 1;
                $service = 0;
            @endphp
            @foreach ($events as $event)
                @if ($event->service_id <> $service)
                    <tr>
                        <th scope="row"  colspan="99" style="background-color: white; color: black;">{{ $event->service->name }}</th>
                    </tr>
                @endif
                @php
                    $service = $event->service_id;
                @endphp
                <tr>
                    <th scope="row" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ $event->id }}</th>
                    <td><span class="label label-default" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ $event->status->name }}</span></td>
                    <td><a href="{{ url('events/' . $event->id) }}">{{ $event->title }}</a>
                    @if($event->slots->count() > 1)
                        <span class="label label-default">{{ $event->slots->count() }}</span>
                    @endif
                    @if($event->aggloy != '')
                        <br/><span class="label label-danger">AggloY</span> <span class="text-danger">{{ $event->aggloy }}</span>
                    @endif
                    @if($event->aggloy4 != '')
                        <br/><span class="label label-warning">AggloY PA4</span> <span class="text-warning">{{ $event->aggloy4 }}</span>
                    @endif
                    </td>
                    <td>{!! nl2br($event->comment) !!}</td>
                    @if($event->slots->min('start_time') == 0)
                        <td colspan="2">Inconnu</td>
                    @else
                        <td><span class="label label-default" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ strftime("%d.%m.%Y", strtotime($event->slots->min('start_time'))) }}</span></td>
                        <td><span class="label label-default" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ strftime("%d.%m.%Y", strtotime($event->slots->max('end_time'))) }}</span></td>
                    @endif
                    <td>{{ $event->contact_name }}</td>
                    <td>
                        @if(count($event->partners))
                            @foreach ($event->partners->sortBy("name") as $partner)
                                <span class="label label-default" style="background-color: #DDDDDD; color: #888888;">{{ $partner->name }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if(!is_null($event->investments))
                            @foreach ($event->investments->sortBy("numero") as $investment)
                                <a href="{{ url('pi#') }}{{ $investment->numero }}">
                                    <span class="label label-default" style="background-color: #AAAAAA; color: #FFFFFF;">PI {{ $investment->numero }}</span>
                                </a>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if (!is_null($event->surface))
                            <a data-toggle="tooltip" data-placement="top" title="Carte" href="/main/wsgi/theme/cctech?wfs_layer=CCTECH_travaux_surface&wfs_id={{ $event->surface->id }}&baselayer_opacity=100&baselayer_ref=asitvd.fond_gris" target="_blank" class="btn btn-sm btn-default">
                                <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
                            </a>
                        @endif
                    </td>
                    @if (Auth::user()->isEditor())
                        <td>
                            @if (Auth::user()->isEventEditor($event->eventtype_id))
                                <a class="btn btn-primary btn-xs" href="{{ url('events/' . $event->id . '/edit')}}">
                                    <span class="glyphicon glyphicon-edit"></span> Éditer
                                </a>
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
        @else
            <h2>Aucun objet saisi !</h2>
        @endif
    </div>
</div>
@endsection
