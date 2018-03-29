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
        <div class="alert alert-warning">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong><i class="fa fa-warning"></i></strong> Un événement peut avoir plusieurs occurrences, il n'apparaîtra toutefois qu'une seule fois dans cette liste.
        </div>
        <div class="text-center">
            {{$events->setPath('list')->render()}}
        </div>
        @if($events->count() > 0)

        <table class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th></th>
                    <th>Titre</th>
                    <th>Organisateur</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Service resp.</th>
                    @if (Auth::user()->isEditor())
                        <th>Modifier</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; ?>
            @foreach($events as $event)
                <tr>
                    <th scope="row" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ $i++ }}</th>
                    <td><a href="{{ url('events/' . $event->id) }}">{{ mb_strimwidth($event->title, 0, 30, '...') }}</a> 
                    @if($event->eventtype_id == 3)
                        @foreach($event->slotsByType as $slotByType)
                            <span class="label label-default" style="background-color: {{ $slotByType->color1 }}; color: {{ $event->Status->color3 }};">{{ $slotByType->total }}</span>
                        @endforeach
                    @elseif($event->slots->count() > 1)
                        <span class="label label-default">{{ $event->slots->count() }}</span>
                    @endif
                    @if($event->commune_id <> 1)
                        <span class="label label-default" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ $event->commune->name }}</span>
                    @endif
                    </td>
                    <td>{{ mb_strimwidth($event->organisation, 0, 30, '...') }}</td>
                    @if($event->slots->min('start_time') == 0)
                        <td colspan="2"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class=""> Pas d'horaire saisi</span></td>
                    @else
                        <td><span class="label label-default" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ strftime("%d.%m.%Y, %H:%M", strtotime($event->slots->min('start_time'))) }}</span></td>
                        <td><span class="label label-default" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ strftime("%d.%m.%Y, %H:%M", strtotime($event->slots->max('end_time'))) }}</span></td>
                    @endif
                    <!--<td>{{-- strftime("%d/%m/%Y, %H:%M", strtotime($event->start_time)) --}}</td>
                    <td>{{-- date("d/m/Y\, G:i", strtotime($event->end_time)) --}}</td>-->
                    <td>{{ $event->Service->name }}</td>
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
        <div class="text-center">
            {{$events->setPath('list')->render()}}
        </div>
        @else
            <h2>Aucun objet saisi !</h2>
        @endif
    </div>
</div>
@endsection
