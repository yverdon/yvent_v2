@extends('layout')

@section('content')

<div class="row hidden-sm hidden-xs">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class="active">Vous êtes ici: {{ $page_title }}</li>
        </ol>
    </div>
</div>
<h1>{{ $page_title }}</h1>
<div>
<label for="decision_date">Accès direct à une date</label>
<div class="input-group">
    <input id="monthPicker" type="text" class="form-control" name="monthpicker">
    <span class="input-group-addon">
        <span class="glyphicon glyphicon-calendar"></span>
    </span>
</div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div id='calendar'></div>
    </div>
</div>

<!-- Fullcalendar Event filtering -->
<div class="row">
    @foreach(Auth::user()->eventtypesReadable() as $eventtype)
        <div class="col-lg-6 e{{ $eventtype->id }}Div">
            @if ($eventtype->checked)
                <input type="checkbox" checked="checked" name="e{{ $eventtype->id }}" id="e{{ $eventtype->id }}" />
            @else
                <input type="checkbox" name="e{{ $eventtype->id }}" id="e{{ $eventtype->id }}" />
            @endif
            <label for="e{{ $eventtype->id }}">{{ $eventtype->namePluralWithLabel() }} ({{ $eventtype->events->count() }})</label>
        </div>
    @endforeach
</div>

<div class="row">
    <div class="col-lg-1">
        <a href="#" data-toggle="popover" title="Calendrier au format ICS (iCal). A copier et utiliser dans Outlook." data-content="{{ url('') }}/ical/{{ Auth::user()->key }}/yvent_{{ Auth::user()->username }}.ics"><img src="{{ url('_asset') }}/ics.png" alt="ICS"></a>
    </div>
    <div class="col-lg-1">
      <a id="csvExport" href="#" class="btn btn-info" role="button"><span class="glyphicon glyphicon-th-list"></span> Export CSV</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-2">
        <button type="button" class="btn btn-default btn-sm" data-toggle="collapse" data-target="#legend">
            Afficher la légende <span class="glyphicon glyphicon-plus"></span>
        </button>
    </div>
</div>
<div class="row">
    <div id="legend" class="collapse">
        @foreach(Auth::user()->statusReadable() as $statu)
            <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 h6">
                <div class="fc-event" style='background-color: {{ $statu->color1 }}; border-color: {{ $statu->color2 }}; color: {{ $statu->color3 }};'>{{ $statu->name }}</div>
                <div class="hidden-xs"><small>{{ $statu->description }}</small></div>
            </div>
        @endforeach
    </div>
</div>

@endsection
@section('js')
<script src="{{ url('_asset/fullcalendar-3.1.0') }}/fullcalendar.min.js"></script>
<script src="{{ url('_asset/fullcalendar-3.1.0') }}/locale-all.js"></script>
<script src="{{ url('_asset/js') }}/daterangepicker.js"></script>

<script defer type="text/javascript">

    $(document).ready(function() {

          //Fullcalendar Event filtering
          var curSource = new Array();
          //sources definition
          @foreach(Auth::user()->eventtypesReadable()->pluck('id') as $eventtypeid)
              curSource[{{ $eventtypeid }}] = '{{ url('/') }}/api2/{{ $eventtypeid }}';
          @endforeach
          var newSource = new Array(); //we'll use this later

          function calAspectRatio() {
              if ($(window).width() > 480) {
                  return 1.35;
                  }
              else {
                  return 1;
              }
          }

          // Fullcalendar Event filtering, calendar refresh
          function calEventFilter() {
              //get current status of our filters into newSource
              @foreach(Auth::user()->eventtypesReadable()->pluck('id') as $eventtypeid)
                  newSource[{{ $eventtypeid }}] = $('#e{{ $eventtypeid }}').is(':checked') ? '{{ url('/') }}/api2/{{ $eventtypeid }}' : '';
              @endforeach

              //remove the old eventSources
              @foreach(Auth::user()->eventtypesReadable()->pluck('id') as $eventtypeid)
                  $('#calendar').fullCalendar('removeEventSource', curSource[{{ $eventtypeid }}]);
              @endforeach

              //attach the new eventSources
              @foreach(Auth::user()->eventtypesReadable()->pluck('id') as $eventtypeid)
                  $('#calendar').fullCalendar('addEventSource', newSource[{{ $eventtypeid }}]);
              @endforeach

              @foreach(Auth::user()->eventtypesReadable()->pluck('id') as $eventtypeid)
                  curSource[{{ $eventtypeid }}] = newSource[{{ $eventtypeid }}];
              @endforeach
          }

        var base_url = '{{ url('/') }}';

        // keep history : set default values
        var today = new Date();
        var tmpYear = today.getFullYear();
        var tmpMonth = today.getMonth();
        var tmpDay = today.getDate();
        var tmpView = 'month';
        // keep history : get values from url
        var vars = window.location.hash.split("&");
        for (var i = 0; i < vars.length; i++) {
          if (vars[i].match("^#year")) tmpYear = vars[i].substring(6);
          if (vars[i].match("^month")) tmpMonth = vars[i].substring(6) - 1;
          if (vars[i].match("^day")) tmpDay = vars[i].substring(4);
          if (vars[i].match("^view")) tmpView = vars[i].substring(5);
        }

        if ($(window).width() > 480) {
            calViews = 'month,listWeek,listMonth,listYear';
            calViewsTitles = {
                month: { buttonText: 'Calendrier' },
                listWeek: { buttonText: 'Semaine' },
                listMonth: { buttonText: 'Mois' },
                listYear: { buttonText: 'Année' },
            }
        }
        else {
            calViews = 'month,listWeek,listMonth';
            calViewsTitles = {
                month: { buttonText: 'Cal.' },
                listWeek: { buttonText: 'Sem.' },
                listMonth: { buttonText: 'Mois' },
            }
        }

        $('#calendar').fullCalendar({
            weekends: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: calViews
            },
            views: calViewsTitles,
            allDaySlot: false,
            editable: false,
            locale: 'fr',
            aspectRatio: calAspectRatio(),
            eventLimit: true,
            eventSources: [],

            eventMouseover: function(calEvent, jsEvent) {
                var startDate = calEvent.start.format('ddd DD.MM.YYYY HH:mm');
                var endDate = calEvent.end.format('ddd DD.MM.YYYY HH:mm');
                var tooltip = '<div class="tooltipevent"style="width:150px; height:140px; color:' + calEvent.textColor + '; background:' + calEvent.backgroundColor + '; border-color:' + calEvent.borderColor + '; border-style: solid; border-width: 1px; border-radius: 4px; padding: 3px 8px; position:absolute; z-index:10001;"><p><small><strong>' + startDate + '<br/>' + endDate + '</strong></small></p><p>' + calEvent.title + '</p></div>';
                var $tooltip = $(tooltip).appendTo('body');

                $(this).mouseover(function(e) {
                    $(this).css('z-index', 10000);
                    $tooltip.fadeIn('500');
                    $tooltip.fadeTo('10', 1.9);
                }).mousemove(function(e) {
                    $tooltip.css('top', e.pageY + 10);
                    $tooltip.css('left', e.pageX + 20);
                });
            },

            eventMouseout: function(calEvent, jsEvent) {
                $(this).css('z-index', 8);
                $('.tooltipevent').remove();
            },

            loading: function(isLoading, view) {
                if (isLoading) {// isLoading gives boolean value
                    $("#loading").addClass("loading");
                } else {
                    $("#loading").removeClass("loading");
                    prepareCSV();
                }
            },
            year: tmpYear,
            month: tmpMonth,
            day: tmpDay,
            defaultView: tmpView,
            viewRender: function (view) {
                var moment = $('#calendar').fullCalendar('getDate');
                if (moment) {
                    window.location.hash = 'year=' + moment.format('YYYY') + '&month=' + ( moment.format('M')
                        ) + '&day=' + moment.format('DD') + '&view=' + view.name;
                }
            }
        });

        var date = new Date(tmpYear, tmpMonth, tmpDay, 0, 0, 0);
        var moment = $('#calendar').fullCalendar('getDate');
        var view = $('#calendar').fullCalendar('getView');
        if (date.getFullYear() != moment.format('YYYY') || date.getMonth() != moment.format('M') || date.getDate() != moment.format('DD'))
            $('#calendar').fullCalendar('gotoDate', date);
        if (view.name != tmpView)
            $('#calendar').fullCalendar('changeView', tmpView);

        // Fullcalendar Event filtering, checkbox state taken in account by calendar load
        calEventFilter();

        // Legend button
        $("#legend").on("hide.bs.collapse", function(){
            $(".btn").html('Afficher la légende <span class="glyphicon glyphicon-plus"></span>');
        });
        $("#legend").on("show.bs.collapse", function(){
            $(".btn").html('Masquer la légende <span class="glyphicon glyphicon-minus"></span>');
        });

        $('[data-toggle="popover"]').popover();

        // Fullcalendar Event filtering
        $("#e{{ Auth::user()->eventtypesReadable()->implode('id',', #e') }}").change(function() {
            calEventFilter();
        });

        if(calendar) {
            $(window).resize(function() {
                $('#calendar').fullCalendar('option', 'aspectRatio', calAspectRatio());
            });
        };

        var today = new Date();
        var maxDate = today.getDate().toString() + '/' + (today.getMonth() + 1).toString() + '/' + (today.getFullYear() + 10).toString();
        $(function() {
          $('#monthPicker').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minDate: '01/01/2016',
            maxDate: maxDate,
            locale: {
                format: 'DD/MM/YYYY'
            }
          },
          function(start, end, label) {
              $('#calendar').fullCalendar('gotoDate', start);
          });
        });

        var prepareCSV = function() {

          var  d = $('#calendar').fullCalendar('clientEvents');
          var csv = 'event_id\ttitle\tlocation\tstart_time\tend_time\n';
          var datstr = today.getDate().toString() + '_' + (today.getMonth() + 1).toString() + '_' + (today.getFullYear()).toString();
          var filename = 'yvent_export_' + datstr + '.txt';

          for (var i=0; i<d.length; i++) {
            csv += d[i].event_id + '\t' + d[i].title + '\t' + d[i].location + '\t' + d[i].start_time + '\t' + d[i].end_time + '\n';
          }

          var blob = new Blob([csv], {
            "type": "text/string;charset=utf-8;"
          });

          var link = $("#csvExport");
          link.attr("href", window.URL.createObjectURL(blob));
          link.attr("download", filename);

        };

    });


</script>

<div id="loading" class="modal"><!-- Place at bottom of page --></div>

@endsection
