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
        <table class="table table-striped" style="font-size: 12px;">
            <thead>
                <tr>
                    <th></th>
                    <th>Statut</th>
                    <th>Titre</th>
                    <th>Commentaire</th>
                    <th>Début</th>
                    <th>Projet AggloY</th>
                    <th>N° mesure</th>
                    <th>Nom mesure</th>
                    <th>Montant cofinancement</th>
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
                    <th scope="row" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ $i++ }}</th>
                    <td><span class="label label-default" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ $event->status->name }}</span></td>
                    <td><a href="{{ url('events/' . $event->id) }}">{{ $event->title }}</a>
                    @if($event->slots->count() > 1)
                        <span class="label label-default">{{ $event->slots->count() }}</span>
                    @endif
                    </td>
                    <td>
                        {!! nl2br($event->comment) !!}
                    </td>
                    @if($event->slots->min('start_time') == 0)
                        <td>Inconnu</td>
                    @else
                        <td><span class="label label-default" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ strftime("%d.%m.%Y", strtotime($event->slots->min('start_time'))) }}</span></td>
                    @endif
                    <td>
                        @if($event->aggloy != '')
                            <span class="label label-danger">AggloY</span>
                        @endif
                        @if($event->aggloy4 != '')
                            <span class="label label-warning">AggloY PA4</span>
                        @endif
                    </td>
                    <td>
                        @if($event->aggloy != '')
                            <span class="text-danger">{{ $event->aggloy }}</span>
                        @endif
                        @if($event->aggloy4 != '')
                            <span class="text-warning">{{ $event->aggloy4 }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $event->aggloy_name }}
                    </td>
                    <td>
                        @if($event->aggloy_amount > 0)
                            {{ number_format($event->aggloy_amount, 0, '.', ' ') }} CHF
                        @endif
                    </td>
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
