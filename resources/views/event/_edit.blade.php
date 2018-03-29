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

<form action="{{ url('events/' . $event->id) }}" method="POST">
    <div class="row">
        <div class="col-lg-6">
            @if($errors)
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            @endif
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="PUT" />
            <div class="form-group @if($errors->has('title')) has-error has-feedback @endif">
                <label for="title">Titre de l'événement*</label>
                <input type="text" class="form-control" name="title" placeholder="Par ex. Concert de musique classique" value="{{ $event->title }}">
                @if ($errors->has('title'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('title') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
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
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('organisation')) has-error has-feedback @endif">
                <label for="organisation">Organisation*</label>
                <input type="text" class="form-control" name="organisation" placeholder="Par ex. Fédération suisse de jeu d'échec" value="{{ $event->organisation }}">
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
                <input type="text" class="form-control" name="contact_name" placeholder="Par ex. Jean Dupont" value="{{ $event->contact_name }}">
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
                <input type="text" class="form-control" name="contact_phone" placeholder="Par ex. 024 123 45 67" value="{{ $event->contact_phone }}">
                @if ($errors->has('contact_phone'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('contact_phone') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('security_name')) has-error has-feedback @endif">
                <label for="security_name">Sécurité</label>
                <input type="text" class="form-control" name="security_name" placeholder="Par ex. Jean Dupont" value="{{ $event->security_name }}">
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
                <input type="text" class="form-control" name="security_phone" placeholder="Par ex. 024 123 45 67" value="{{ $event->security_phone }}">
                @if ($errors->has('security_phone'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('security_phone') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group @if($errors->has('comment')) has-error has-feedback @endif">
                <label for="comment">Commentaire</label>
                <!--<input type="text" class="form-control" name="comment" placeholder="Par ex. Nécessite une autorisation cantonale" value="{{ old('comment') }}">-->
                {{ Form::textarea('comment', $event->comment, ['placeholder' => 'Par ex. Nécessite une autorisation cantonale...','class' => 'form-control','name' => "comment",'size' => '80x5']) }}
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
                @if (Auth::user()->isAdmin())
                    {{ Form::select('status', $status, $event->status_id, ['placeholder' => 'Choisissez un statut...','class' => 'form-control','name' => "status_id"]) }}
                @elseif (Auth::user()->isEventEditor())
                    {{ Form::select('status', $event_status, $event->status_id, ['placeholder' => 'Choisissez un statut...','class' => 'form-control','name' => "status_id"]) }}
                @elseif (Auth::user()->isPoliceEditor())
                    {{ Form::select('status', $police_status, $event->status_id, ['placeholder' => 'Choisissez un statut...','class' => 'form-control','name' => "status_id"]) }}
                @else
                    {{ Form::select('status', $project_status, $event->status_id, ['placeholder' => 'Choisissez un statut...','class' => 'form-control','name' => "status_id"]) }}
                @endif
                <!--<input type="text" class="form-control" name="status_id" placeholder="Par ex. Jean Dupont" value="{{ $event->status_id }}">-->
                @if ($errors->has('status_id'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('status_id') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group @if($errors->has('announcement_date')) has-error @endif">
                <label for="announcement_date">Date annonce</label>
                <div class="input-group">
                    @if (!Auth::user()->isProjectEditor())
                        <input type="text" class="form-control" name="announcement_date" placeholder="Sélectionnez une date..." value="{{ $event->announcement_date }}">
                    @else
                        <input type="text" class="form-control" name="announcement_date" placeholder="Sélectionnez une date..." value="{{ $event->announcement_date }}" disabled>
                    @endif
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
                    @if (!Auth::user()->isProjectEditor())
                        <input type="text" class="form-control" name="decision_date" placeholder="Sélectionnez une date..." value="{{ $event->decision_date }}">
                    @else
                        <input type="text" class="form-control" name="decision_date" placeholder="Sélectionnez une date..." value="{{ $event->decision_date }}" disabled>
                    @endif
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
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('service_id')) has-error has-feedback @endif">
                <label for="service_id">Service responsable*</label>
                @if (Auth::user()->isPoliceEditor())
                    {{ Form::select('services', $police_services, $event->service_id, ['placeholder' => 'Choisissez un service...','class' => 'form-control','name' => "service_id"]) }}
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
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon glyphicon-save"></span> Enregistrer</button>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-lg-3">
        <form id="delete" action="{{ url('events/' . $event->id) }}" style="display:inline;" method="POST">
                <input type="hidden" name="_method" value="DELETE" />
                {{ csrf_field() }}
                <button class="btn btn-danger" type="submit"><span class="glyphicon glyphicon-trash"></span> Supprimer</button>
        </form>
    </div>
</div>
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

</script>
@endsection