<?php
$emojis = \App\Emoji::all();
?>
<div class="list--emoji noselect" >
    @foreach($emojis as $emoji)
        <div class="item--list--emoji ">{{$emoji->code}}</div>
    @endforeach
</div>
