@extends('layout')

@section('content')

<div class="row hidden-sm hidden-xs">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li>Vous êtes ici: <a href="{{ url('/') }}">Calendrier</a></li>
            <li><a href="{{ url('/events/' . $event->id . '/edit') }}">Édition - {{ $event->title }}</a></li>
            <li class="active">{{ $page_title }}</li>
        </ol>
    </div>
</div>
<h1>{{ $page_title }}</h1>
@include('message')

<form action="{{ url('events/' . $event->id . '/slots') }}" method="POST">
    <div class="row bg-info">
        <div class="col-lg-6">
            {{ csrf_field() }}
            <div class="form-group @if($errors->has('time')) has-error @endif">
                <label for="time">Horaire*</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="time" placeholder="Select your time" value="{{ old('time') }}">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                @if ($errors->has('time'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('time') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('location')) has-error has-feedback @endif">
                <label for="location">Emplacement*</label>
                <input type="text" class="form-control" name="location" placeholder="Par ex. Place Pestalozzi" value="{{ old('location') }}">
                @if ($errors->has('location'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('location') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    <div class="row bg-info">
        <div class="col-lg-6">
            <div class="form-group @if($errors->has('slottype_id')) has-error has-feedback @endif">
                <label for="slottype_id">Type d'horaire*</label>
                {{ Form::select('slottypes', $slottypes, old('slottype_id'), ['placeholder' => 'Choisissez un type...','class' => 'form-control','name' => "slottype_id"]) }}
                @if ($errors->has('slottype_id'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('slottype_id') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
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
</script>
@endsection