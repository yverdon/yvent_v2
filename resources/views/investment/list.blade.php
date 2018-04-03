@extends('layout')

@section('content')
<style>

table {
    font-size: 11px;
}

</style>

<div class="row hidden-sm hidden-xs">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li>Vous êtes ici: <a href="{{ url('/') }}">Calendrier</a></li>
            <li class="active"><a href="{{ url('/pi') }}">{{ $page_title }}</a></li>
        </ol>
    </div>
</div>
<h1>{{ $page_title }}</h1>
<div class="row">
    <div class="col-lg-12 table-responsive">
        
        @if($investments->count() > 0)

        <table class="table table-condensed">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Services</th>
                    <th>Objet</th>
                    <th>CC</th>
                    <th class="text-right">Voté</th>
                    <th class="text-right">A voter</th>
                    <th class="text-right" style="background-color: #000000; color: #FFFFFF;">2017</th>
                    <th class="text-right">2018</th>
                    <th class="text-right">2019</th>
                    <th class="text-right">2020</th>
                    <th class="text-right">2021</th>
                    <th class="text-right">2022</th>
                    <th class="text-right">2023</th>
                    <th class="text-right">2024</th>
                    <th class="text-right">2025</th>
                    <th class="text-right">2026</th>
                    <th class="text-right">Suiv.</th>
                    <th>Chantiers</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; ?>
            @foreach($investments as $investment)
                @if($investment->priorite_politique == 'P')
                    @if(abs($investment->credits_votes) > 0)
                        <tr style="background-color: #CCDDCC;">
                    @else
                        <tr style="background-color: #EEEEEE;">
                    @endif
                @elseif(abs($investment->credits_votes) > 0)
                    <tr style="background-color: #F0FAF0;">
                @else
                    <tr>
                @endif
                    <td id="{{ $investment->numero }}">
                        {{ $investment->numero }}
                    </td>
                    <td>
                        @if($investment->aggloy)
                                <span class="label label-default" style="background-color: #64B9E3; color: #FFFFFF;">AGGLOY</span>
                        @endif
                        @if($investment->jecos)
                                <span class="label label-default" style="background-color: #933589; color: #FFFFFF;">JECOS</span>
                        @endif
                        @if($investment->culture)
                                <span class="label label-default" style="background-color: #CF346C; color: #FFFFFF;">SCU</span>
                        @endif
                        @if($investment->sey)
                                <span class="label label-default" style="background-color: #008E59; color: #FFFFFF;">SEY</span>
                        @endif
                        @if($investment->ste)
                                <span class="label label-default" style="background-color: #56AC42; color: #FFFFFF;">STE</span>
                        @endif
                        @if($investment->sdis)
                                <span class="label label-default" style="background-color: #E20045; color: #FFFFFF;">SDIS</span>
                        @endif
                        @if($investment->urbat)
                                <span class="label label-default" style="background-color: #005D92; color: #FFFFFF;">URBAT</span>
                        @endif
                        @if($investment->finances)
                                <span class="label label-default" style="background-color: #58585A; color: #FFFFFF;">FIN</span>
                        @endif
                        @if($investment->ssp)
                                <span class="label label-default" style="background-color: #0076BD; color: #FFFFFF;">SSP</span>
                        @endif
                        @if($investment->sg)
                                <span class="label label-default" style="background-color: #DDDDDD; color: #FFFFFF;">SG</span>
                        @endif
                        @if($investment->sports)
                                <span class="label label-default" style="background-color: #EE824F; color: #FFFFFF;">SPORTS</span>
                        @endif
                    </td>
                    <td>
                    @if($investment->priorite_politique == 'P')
                        <span class="label label-default" style="background-color: #FFFFFF; color: #000000;">PRIORITAIRE</span>
                    @endif
                    {{ $investment->objet }}
                    </td>
                    <td>
                    @if($investment->conseil_communal <> 0)
                        {{ strftime("%d.%m.%Y", strtotime($investment->conseil_communal)) }}
                    @endif
                    </td>
                    <td class="text-right">
                    @if(abs($investment->credits_votes) > 0)
                        <strong>{{ $investment->credits_votes }}</strong>
                    @else
                        {{ $investment->credits_votes }}
                    @endif
                    </td>
                    <td class="text-right">{{ $investment->credits_a_voter }}</td>
                    @if(abs($investment->montant_2017) > 0)
                        @if(abs($investment->credits_votes) > 0)
                            <td class="text-right" style="border-left: 1px solid #DDD; border-right: 1px solid #DDD; background-color: #88BB88; color: #FFFFFF;">
                        @else
                            <td class="text-right" style="border-left: 1px solid #DDD; border-right: 1px solid #DDD; background-color: #888888; color: #FFFFFF;">
                        @endif
                            {{ $investment->montant_2017 }}
                    @else
                        <td class="text-right" style="border-left: 1px solid #DDD; border-right: 1px solid #DDD;">
                    @endif
                    </td>
                    <td class="text-right">{{ $investment->montant_2018 }}</td>
                    <td class="text-right">{{ $investment->montant_2019 }}</td>
                    <td class="text-right">{{ $investment->montant_2020 }}</td>
                    <td class="text-right">{{ $investment->montant_2021 }}</td>
                    <td class="text-right">{{ $investment->montant_2022 }}</td>
                    <td class="text-right">{{ $investment->montant_2023 }}</td>
                    <td class="text-right">{{ $investment->montant_2024 }}</td>
                    <td class="text-right">{{ $investment->montant_2025 }}</td>
                    <td class="text-right">{{ $investment->montant_2026 }}</td>
                    <td class="text-right">{{ $investment->montant_annees_suivantes }}</td>
                    <td>
                        @if(count($investment->events))
                            @foreach ($investment->events->sortBy("id") as $event)
                                <a data-toggle="tooltip" data-placement="top" title="Chantier {{ $event->service->name }} {{ $event->title }}" href="{{ url('events/' . $event->id) }}">
                                    🚧 {{ $event->id }}
                                    <span class="label label-default" style="background-color: {{ $event->Status->color1 }}; color: {{ $event->Status->color3 }};">{{ $event->status->name }}</span>
                                </a>
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
