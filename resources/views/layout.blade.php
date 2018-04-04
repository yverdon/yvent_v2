<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ url('/') }}/_asset/favicon.png">

    <title>YVENT - {{ $page_title or ''}}</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="{{url('css/yverdon')}}/fulltextsearch.css">


    <!-- Custom styles for this template -->
    <link href="{{ url('_asset/css') }}/style.css" rel="stylesheet">
    <link href="{{ url('_asset/css') }}/daterangepicker.css" rel="stylesheet">
    <link href="{{ url('_asset/fullcalendar-3.1.0') }}/fullcalendar.min.css" rel="stylesheet">

  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{ url('/') }}"><i class="fa fa-calendar"></i> YVENT</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <form class="navbar-form navbar-left">
            <div class="form-group">
              <input id="eventFulltextSearch" type="text" class="form-control" placeholder="Rechercher...">
            </div>
          </form>
          <ul class="nav navbar-nav navbar-right">
            <!-- <li>
              <div>
                <input id="eventFulltextSearch" placeholder="Rechercher un événement...">
              </div>
            </li> -->
            <!-- Authentication Links -->
            <?php //@if (Auth::guest()) ?>
            @if (Auth::guest())
            @else
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-list-ul"></i> Listes <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        @foreach (Auth::user()->eventtypesReadable() as $eventtype)
                            @if ($eventtype->id == 4)
                                <li><a href="{{ url('chantiers/') }}">{{ $eventtype->namePluralWithLabel() }} (tous)</a></li>
                                @php
                                    $services = App\Service::whereHas('events',function($query) use ($eventtype) {
                                        $query->whereHas('eventtype',function($query) use ($eventtype) {
                                            $query->where([['id','=',$eventtype->id]]);
                                        });
                                    })->orderBy('name')->get();
                                @endphp
                                @foreach ($services as $service)
                                    <li><a href="{{ url('chantiers/' . $service->id) }}">{{ $eventtype->namePluralWithLabel() }} {{ $service->name }}</a></li>
                                @endforeach
                                <li><a href="{{ url('chantiers/aggloy') }}">{{ $eventtype->namePluralWithLabel() }} (AggloY)</a></li>
                            @else
                                <li><a href="{{ url('events/' . $eventtype->id . '/all') }}">{{ $eventtype->namePluralWithLabel() }}</a></li>
                                <li><a href="{{ url('events/' . $eventtype->id . '/old') }}">{{ $eventtype->namePluralWithLabel() }} (anciens)</a></li>
                            @endif
                            <li role="separator" class="divider"></li>
                        @endforeach
                        <li><a href="{{ url('pi/') }}">💳 Plan des investissements</a></li>
                        <li role="separator" class="divider"></li>
                    </ul>
                </li>
                @if (Auth::user()->isEditor())
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <i class="fa fa-calendar-plus-o"></i> Ajouter <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            @foreach(Auth::user()->eventtypesWriteable() as $eventtype)
                                <li><a href="{{ url('events/create/' . $eventtype->id) }}">{{ $eventtype->nameWithLabel() }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endif
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-user"></i> {{ Auth::user()->username }}{{-- ({{ Auth::user()->role_rel->description }})--}} <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="{{ url('/logout') }}"
                                onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                Se déconnecter
                            </a>

                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                        @if (Auth::user()->isAdmin())
                        @endif
                    </ul>
                </li>
            @endif
          </ul>
        </div>
      </div>
    </nav>

    <div class="container">

        @yield('content')

    </div> <!-- /container -->

    <footer class="footer">
        <p>Cette application a été réalisée par <a href="http://www.yverdon-les-bains.ch/prestations-deladministration/informatique/sit/">SIT Yverdon</a> à l'aide de <a href="https://laravel.com/">Laravel</a>, <a href="http://getbootstrap.com/">Bootstrap</a> et <a href="https://fullcalendar.io/">FullCalendar</a>.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <script src="{{url('_asset/fullcalendar/lib')}}/moment.min.js"></script>
    <script>
      $(document).ready(function() {

          // fulltextSearch
          $(function() {
            var urlft = "{{ url('/') }}" + '/eventfulltext';
              $("#eventFulltextSearch").autocomplete({
                  source: urlft,
                  minLength: 2,
                  select: function(event, ui) {
                    event.preventDefault();
                    var url = "{{ url('/') }}" + '/events/' + ui.item.value;
                    $("#eventFulltextSearch").val('');
                    $(location).attr('href',url);
                  }
                }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                  return $( "<li>" )
                    .append( "<div>" + item.label + " - " + item.last_update + "</div>" )
                    .appendTo( ul );
                };
          });
      });
  </script>


    @yield('js')

  </body>
</html>
