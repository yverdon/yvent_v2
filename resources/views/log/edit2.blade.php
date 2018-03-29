@extends('layout')

@section('content')

<div class="row hidden-sm hidden-xs">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li>Vous êtes ici: <a href="{{ url('/') }}">Calendrier</a></li>
            @if($insert)
                <li><a href="{{ url('/events/' . $event->id . '/edit') }}">Édition - {{ $event->title }}</a></li>
            @else
                <li><a href="{{ url('/events/' . $log->event->id . '/edit') }}">Édition: {{ $log->event->title }}</a></li>
            @endif
            <li class="active">{{ $page_title }}</li>
        </ol>
    </div>
</div>
<h1>{{ $page_title }}</h1>
@include('message')

@if($insert)
    <form action="{{ url('events/' . $event->id . '/logs') }}" method="POST" enctype="multipart/form-data">
@else
    <form action="{{ url('events/' . $log->event->id . '/logs/' . $log->id) }}" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="_method" value="PUT" />
@endif
    <div class="row bg-warning">
        
        <div class="col-lg-6">
            @if($errors)
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            @endif
            {{ csrf_field() }}
            <div class="form-group @if($errors->has('content')) has-error has-feedback @endif">
                <label for="content">Contenu*</label>
                <input type="text" class="form-control" name="content" placeholder="Par ex. Accepté par le service xx sous réserve de..." value="@if($insert){{ old('content') }}@else{{ $log->content }}@endif">
                @if ($errors->has('content'))
                    <p class="help-block"><span class="glyphicon glyphicon-exclamation-sign"></span> 
                    {{ $errors->first('content') }}
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
</form>
@if(!$insert)
    <div class="row">
        <div class="col-lg-3">
            <form action="{{ url('events/' . $log->event->id . '/logs/' . $log->id) }}" style="display:inline;" method="POST">
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