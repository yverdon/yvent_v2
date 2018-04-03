@extends('layout_pdf')

@section('content')

<h1>{{ $page_title }}</h1>
<div class="row">
    <div class="col-lg-12 table-responsive">
        @if($events->count() > 0)
        <table class="table table-striped" style="font-size: 8px;">
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
