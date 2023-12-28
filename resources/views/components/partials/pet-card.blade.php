<div class="pet" data-pet="{{json_encode($pet)}}">
    <a href="{{$pet->photo}}" target="_blank">
        <img src="{{$pet->photo}}" width="256">
    </a>
    <b>{{$pet->name}}</b><br>
    <b>Rescue: {{$pet->rescue_name}}</b><br>
    <b>Found: {{$pet->intake_date->diffForHumans()}}</b><br>
    <a href="https://lost.petcolove.org/pet/{{$pet->animal_id}}" target="_blank">view</a>
</div>
