@extends('layout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Demande de nouveau compte (réservé aux membres de l'administration communale)</div>

                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register2') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label">Nom d'utilisateur</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" autofocus>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @elseif (isset($username))
                                    <span class="help-block">
                                        <strong>La demande de création de compte pour l'utilisateur {{ $username }} a bien été envoyée.</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Envoyer la demande
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <p>
                 Votre nom d'utilisateur existe déjà ? Avez-vous pensé à <a href="{{ url('/password/reset') }}" >réinitialiser votre mot de passe</a> ?
            </p>
        </div>
    </div>
</div>
@endsection
