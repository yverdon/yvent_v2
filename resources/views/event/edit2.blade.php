@extends('layout')

@section('content')

<div class="row hidden-sm hidden-xs">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li>Vous êtes ici: <a href="{{ url('/') }}">Calendrier</a></li>
            <li class="active">{{ $page_title }}</li>
        </ol>
    </div>
</div>
<h1>{{ $page_title }}</h1>
@include('message')

@if($insert)
    <form action="{{ url('events') }}" method="POST">
    <input type="hidden" name="eventtype_id" value="{{ $eventtype->id }}" />
@else
    <form action="{{ url('events/' . $event->id) }}" method="POST" enctype="multipart/form-data">
    {{-- Form::open(array('url'=>'events/' . $event->id,'method'=>'POST', 'files'=>true)) --}}
    <input type="hidden" name="_method" value="PUT" />
    <input type="hidden" name="eventtype_id" value="{{ $event->eventtype_id }}" />
@endif
    <div class="row">
        <div class="col-lg-6">
        {{--@if($errors)
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
        @endif--}}
            {{ csrf_field() }}
            <div class="form-group @if($errors->has('title')) has-error has-feedback @endif">
                <label for="title">Titre de l'événement*</label>
                <input type="text" class="form-control" name="title" placeholder="Par ex. Concert de musique classique" value="@if($insert){{ old('title') }}@else{{ $event->title }}@endif">
                @if ($errors->has('title'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('title') }}
                    </p>
                @endif
            </div>
        </div>
        @if($communes->count() > 0)
            <div class="col-lg-6">
                <div class="form-group @if($errors->has('commune_id')) has-error has-feedback @endif">
                    <label for="commune_id">Commune*</label>
                    @if($insert)
                        {{ Form::select('communes', $communes, old('commune_id'), ['placeholder' => 'Choisissez une commune...','class' => 'form-control','name' => "commune_id"]) }}
                    @else
                        {{ Form::select('communes', $communes, $event->commune_id, ['placeholder' => 'Choisissez une commune...','class' => 'form-control','name' => "commune_id"]) }}
                    @endif
                    @if ($errors->has('commune_id'))
                        <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                        {{ $errors->first('commune_id') }}
                        </p>
                    @endif
                </div>
            </div>
        @endif
    </div>
    @if(!$insert)
        <div class="row">
            <div class="col-lg-4">
                <strong>Horaire(s)</strong>
            </div>
        </div>
        @if ( !$event->slots->count() )
        Votre événement n'a pas d'horaire.
        @else
            @foreach( $event->slots->sortBy('start_time') as $slot )
                <!--<li><a href="{{ route('events.slots.show', [$event->id, $slot->id]) }}">{{ $slot->location }}</a></li>-->
                <div class="row bg-info">
                    <div class="col-lg-2">
                        <a class="btn btn-primary btn-xs" href="{{ url('events/' . $event->id . '/slots/' . $slot->id . '/edit')}}">
                            <span class="glyphicon glyphicon-edit"></span> Éditer</a>
                    </div>
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
                </div>
            @endforeach
        @endif
        <div class="row">
            <div class="col-lg-2">
                <a class="btn btn-primary btn-xs" href="{{ url('events/' . $event->id . '/slots/create') }}">
                            <span class="glyphicon glyphicon-plus-sign"></span> Ajouter un horaire</a>
            </div>
        </div>
    @endif
    @if(!$insert)
        <div class="row">
            <div class="col-lg-4">
                <strong>Document(s)</strong>
            </div>
        </div>
        @if ( !$event->documents->count() )
        Votre événement n'a pas de document.
        @else
            @foreach( $event->documents->sortBy('filename') as $document )
                <div class="row bg-warning">
                    <div class="col-lg-2">
                        <a class="btn btn-warning btn-xs" href="{{ url('events/' . $event->id . '/documents/' . $document->id . '/edit')}}">
                            <span class="glyphicon glyphicon-edit"></span> Éditer</a>
                    </div>
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
        <div class="row">
            <div class="col-lg-2">
                <a class="btn btn-warning btn-xs" href="{{ url('events/' . $event->id . '/documents/create') }}">
                            <span class="glyphicon glyphicon-plus-sign"></span> Ajouter un document</a>
            </div>
        </div>
    @endif
    @if(!$insert)
        <div class="row">
            <div class="col-lg-6">
                <strong>Entrée(s) au journal</strong> <a href="#" data-toggle="popover"  title="Journal" data-content="Le journal permet de saisir des informations ou des décisions relative à l'événement. Elles se présenteront sous la forme d'un tableau."><span class="glyphicon glyphicon-info-sign"></span></a>
            </div>
        </div>
        @if ( !$event->logs->count() )
        Votre événement n'a pas d'entrée au journal.
        @else
            @foreach( $event->logs->sortBy('updated_at') as $log )
                <!--<li><a href="{{ route('events.logs.show', [$event->id, $log->id]) }}">{{ $log->location }}</a></li>-->
                <div class="row bg-danger">
                    <div class="col-lg-2">
                        <a class="btn btn-danger btn-xs" href="{{ url('events/' . $event->id . '/logs/' . $log->id . '/edit')}}">
                            <span class="glyphicon glyphicon-edit"></span> Éditer</a>
                    </div>
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
            <div class="col-lg-2">
                <a class="btn btn-danger btn-xs" href="{{ url('events/' . $event->id . '/logs/create') }}">
                            <span class="glyphicon glyphicon-plus-sign"></span> Ajouter une entrée au journal</a>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('organisation')) has-error has-feedback @endif">
                <label for="organisation">Organisation*</label>
                <input type="text" class="form-control" name="organisation" placeholder="Par ex. Fédération suisse de jeu d'échec" value="@if($insert){{ old('organisation') }}@else{{ $event->organisation }}@endif">
                @if ($errors->has('organisation'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('organisation') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('contact_name')) has-error has-feedback @endif">
                <label for="contact_name">Contact*</label>
                <input type="text" class="form-control" name="contact_name" placeholder="Par ex. Jean Dupont" value="@if($insert){{ old('contact_name') }}@else{{ $event->contact_name }}@endif">
                @if ($errors->has('contact_name'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('contact_name') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('contact_phone')) has-error has-feedback @endif">
                <label for="contact_phone">Tél. contact*</label>
                <input type="text" class="form-control" name="contact_phone" placeholder="Par ex. 024 123 45 67" value="@if($insert){{ old('contact_phone') }}@else{{ $event->contact_phone }}@endif">
                @if ($errors->has('contact_phone'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('contact_phone') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    @if (in_array($eventtype->id, array(1,2)))
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('security_name')) has-error has-feedback @endif">
                <label for="security_name">Sécurité</label>
                <input type="text" class="form-control" name="security_name" placeholder="Par ex. Jean Dupont" value="@if($insert){{ old('security_name') }}@else{{ $event->security_name }}@endif">
                @if ($errors->has('security_name'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('security_name') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('security_phone')) has-error has-feedback @endif">
                <label for="security_phone">Tél. sécurité</label>
                <input type="text" class="form-control" name="security_phone" placeholder="Par ex. 024 123 45 67" value="@if($insert){{ old('security_phone') }}@else{{ $event->security_phone }}@endif">
                @if ($errors->has('security_phone'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('security_phone') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group @if($errors->has('comment')) has-error has-feedback @endif">
                <label for="comment">Commentaire</label>
                <!--<input type="text" class="form-control" name="comment" placeholder="Par ex. Nécessite une autorisation cantonale" value="{{ old('comment') }}">-->
                @if($insert)
                    {{ Form::textarea('comment', null, ['placeholder' => 'Par ex. Nécessite une autorisation cantonale...','class' => 'form-control','name' => "comment",'size' => '80x5']) }}
                @else
                    {{ Form::textarea('comment', $event->comment, ['placeholder' => 'Par ex. Nécessite une autorisation cantonale...','class' => 'form-control','name' => "comment",'size' => '80x5']) }}
                @endif
                @if ($errors->has('comment'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('comment') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('status_id')) has-error has-feedback @endif">
                <label for="status_id">Statut*</label>
                @if($insert)
                    {{ Form::select('status', $status, old('status_id'), ['placeholder' => 'Choisissez un statut...','class' => 'form-control','name' => "status_id"]) }}
                @else
                    {{ Form::select('status', $status, $event->status_id, ['placeholder' => 'Choisissez un statut...','class' => 'form-control','name' => "status_id"]) }}
                @endif
                <!--<input type="text" class="form-control" name="status_id" placeholder="Choisissez un statut..." value="{{ old('status_id') }}">-->
                @if ($errors->has('status_id'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('status_id') }}
                    </p>
                @endif
            </div>
        </div>
        @if ($eventtype->id == 2)
        <div class="col-lg-3">
            <div class="form-group @if($errors->has('announcement_date')) has-error @endif">
                <label for="announcement_date">Date annonce</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="announcement_date" placeholder="Sélectionnez une date..." value="@if($insert){{ old('announcement_date') }}@else{{ $event->announcement_date }}@endif">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                @if ($errors->has('announcement_date'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('announcement_date') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group @if($errors->has('decision_date')) has-error @endif">
                <label for="decision_date">Date décision</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="decision_date" placeholder="Sélectionnez une date..." value="@if($insert){{ old('decision_date') }}@else{{ $event->decision_date }}@endif">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                @if ($errors->has('decision_date'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('decision_date') }}
                    </p>
                @endif
            </div>
        </div>
        @endif
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('service_id')) has-error has-feedback @endif">
                <label for="service_id">Service responsable*</label>
                @if($insert)
                    {{ Form::select('services', $services, old('service_id'), ['placeholder' => 'Choisissez un service...','class' => 'form-control','name' => "service_id"]) }}
                @else
                    {{ Form::select('services', $services, $event->service_id, ['placeholder' => 'Choisissez un service...','class' => 'form-control','name' => "service_id"]) }}
                @endif
                <!--<input type="text" class="form-control" name="service_id" placeholder="Par ex. Jean Dupont" value="{{ old('service_id') }}">-->
                @if ($errors->has('service_id'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('service_id') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    @if(!$insert and $eventtypes->count() > 1)
        <div class="row bg-danger">
                <div class="col-lg-6">
                    <div class="form-group @if($errors->has('eventtype_id')) has-error has-feedback @endif">
                        <label for="eventtype_id">Type d'événement*</label>
                        {{ Form::select('eventtypes', $eventtypes, $event->eventtype_id, ['placeholder' => 'Choisissez un type...','class' => 'form-control','name' => "eventtype_id"]) }}
                        @if ($errors->has('eventtype_id'))
                            <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                            {{ $errors->first('eventtype_id') }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <i class="fa fa-warning"></i> Une "Ébauche d'événement" peut être créée et modifiée par les services. Lorsque la manifestation est confirmée, son type sera modifié en "Événement". Elle ne sera alors plus éditable que par la police du commerce.
                </div>
        </div>
    @endif
    @if (in_array($eventtype->id, array(4)))
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('aggloy')) has-error has-feedback @endif">
                <label for="aggloy">AggloY, mesures actuelles</label>
                <input type="text" class="form-control" name="aggloy" placeholder="Par ex. 2-31" value="@if($insert){{ old('aggloy') }}@else{{ $event->aggloy }}@endif">
                @if ($errors->has('aggloy'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('aggloy') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('aggloy4')) has-error has-feedback @endif">
                <label for="aggloy4">AggloY, mesures PA4</label>
                <input type="text" class="form-control" name="aggloy4" placeholder="Par ex. 2-31" value="@if($insert){{ old('aggloy4') }}@else{{ $event->aggloy4 }}@endif">
                @if ($errors->has('aggloy4'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('aggloy4') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('aggloy_name')) has-error has-feedback @endif">
                <label for="aggloy_name">AggloY, nom de la mesure</label>
                @if($insert)
                    {{ Form::textarea('aggloy_name', null, ['placeholder' => 'Par ex. Réaménagement des ruelles et places du centre historique...','class' => 'form-control','name' => "aggloy_name",'size' => '80x5']) }}
                @else
                    {{ Form::textarea('aggloy_name', $event->aggloy_name, ['placeholder' => 'Par ex. Réaménagement des ruelles et places du centre historique...','class' => 'form-control','name' => "aggloy_name",'size' => '80x5']) }}
                @endif
                @if ($errors->has('aggloy_name'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('aggloy_name') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('aggloy_amount')) has-error has-feedback @endif">
                <label for="aggloy_amount">AggloY, montant cofinancement</label>
                <input type="text" class="form-control" name="aggloy_amount" placeholder="Par ex. 500000" value="@if($insert){{ old('aggloy_amount') }}@else{{ $event->aggloy_amount }}@endif">
                @if ($errors->has('aggloy_amount'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('aggloy_amount') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-lg-6">
            {{-- Form::file('image') --}}
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon glyphicon-save"></span> Enregistrer</button>
        </div>
    </div>
</form>
@if(!$insert)
    <div class="row">
        <div class="col-lg-3">
            <form id="delete" action="{{ url('events/' . $event->id) }}" style="display:inline;" method="POST">
                    <input type="hidden" name="_method" value="DELETE" />
                    {{ csrf_field() }}
                    <button class="btn btn-danger" type="submit"><span class="glyphicon glyphicon-trash"></span> Supprimer</button>
            </form>
        </div>
    </div>
@endif
<div class="row">
    <div class="col-lg-3">
        <small>* champ obligatoire</small>
    </div>
</div>
@endsection

@section('js')
<script src="{{ url('_asset/js') }}/daterangepicker.js"></script>
<script type="text/javascript">
$(function () {
    $('input[name="time"]').daterangepicker({
        "minDate": moment('<?php echo date('Y-m-d G')?>'),
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

$(function() {
    $('input[name="announcement_date"]').daterangepicker({
        "singleDatePicker": true,
        "autoUpdateInput": false,
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
        }
    });
});
$('input[name="announcement_date"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD/MM/YYYY'));
});
$('input[name="announcement_date"]').on('cancel.daterangepicker', function(ev, picker) {
  $(this).val('');
});

$(function() {
    $('input[name="decision_date"]').daterangepicker({
        "singleDatePicker": true,
        "autoUpdateInput": false,
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
        }
    });
});
$('input[name="decision_date"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD/MM/YYYY'));
});
$('input[name="decision_date"]').on('cancel.daterangepicker', function(ev, picker) {
  $(this).val('');
});
$("#delete").on("submit", function(){
    return confirm("Voulez-vous vraiment supprimer cet événement ?");
});

$('[data-toggle="popover"]').popover();
</script>
@endsection