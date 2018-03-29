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

<form action="{{ url('events') }}" method="POST">
    <div class="row">
        <div class="col-lg-6">
            {{ csrf_field() }}
            <div class="form-group @if($errors->has('title')) has-error has-feedback @endif">
                <label for="title">Titre de l'événement*</label>
                <input type="text" class="form-control" name="title" placeholder="Par ex. Concert de musique classique" value="{{ old('title') }}">
                @if ($errors->has('title'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('title') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('organisation')) has-error has-feedback @endif">
                <label for="organisation">Organisation*</label>
                <input type="text" class="form-control" name="organisation" placeholder="Par ex. Fédération suisse de jeu d'échec" value="{{ old('organisation') }}">
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
                <input type="text" class="form-control" name="contact_name" placeholder="Par ex. Jean Dupont" value="{{ old('contact_name') }}">
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
                <input type="text" class="form-control" name="contact_phone" placeholder="Par ex. 024 123 45 67" value="{{ old('contact_phone') }}">
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
                <input type="text" class="form-control" name="security_name" placeholder="Par ex. Jean Dupont" value="{{ old('security_name') }}">
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
                <input type="text" class="form-control" name="security_phone" placeholder="Par ex. 024 123 45 67" value="{{ old('security_phone') }}">
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
                {{ Form::textarea('comment', null, ['placeholder' => 'Par ex. Nécessite une autorisation cantonale...','class' => 'form-control','name' => "comment",'size' => '80x5']) }}
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
                {{--@if (Auth::user()->isAdmin())--}}
                    {{ Form::select('status', $status, old('status_id'), ['placeholder' => 'Choisissez un statut...','class' => 'form-control','name' => "status_id"]) }}
                {{--@elseif (Auth::user()->isEventEditor())
                    {{ Form::select('status', $event_status, old('status_id'), ['placeholder' => 'Choisissez un statut...','class' => 'form-control','name' => "status_id"]) }}
                @elseif (Auth::user()->isPoliceEditor())
                    {{ Form::select('status', $police_status, old('status_id'), ['placeholder' => 'Choisissez un statut...','class' => 'form-control','name' => "status_id"]) }}
                @else
                {{ Form::select('status', $project_status, old('status_id'), ['placeholder' => 'Choisissez un statut...','class' => 'form-control','name' => "status_id"]) }}
                @endif--}}
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
                    <input type="text" class="form-control" name="announcement_date" placeholder="Sélectionnez une date..." value="{{ old('announcement_date') }}">
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
                    <input type="text" class="form-control" name="decision_date" placeholder="Sélectionnez une date..." value="{{ old('decision_date') }}">
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
                {{--@if (Auth::user()->isPoliceEditor())
                    {{ Form::select('services', $police_services, old('service_id'), ['placeholder' => 'Choisissez un service...','class' => 'form-control','name' => "service_id"]) }}
                @else--}}
                    {{ Form::select('services', $services, old('service_id'), ['placeholder' => 'Choisissez un service...','class' => 'form-control','name' => "service_id"]) }}
                {{--@endif--}}
                <!--<input type="text" class="form-control" name="service_id" placeholder="Par ex. Jean Dupont" value="{{ old('service_id') }}">-->
                @if ($errors->has('service_id'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('service_id') }}
                    </p>
                @endif
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <small>* champ obligatoire</small>
        </div>
    </div>
</form>
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
</script>
@endsection