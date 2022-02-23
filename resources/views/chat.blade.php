@extends('layouts.layout')

@section('content')
    <div class="main--box">
        <div id="app" class="chatbox">
        

            <!-- HEADER -->
            <div class="chatbox__header">
                <i class="arrow-icon list--show">keyboard_arrow_right</i><i class="news" style="font-size: 10px;color: red"></i>
                <i class="mini-arrow-icon" onclick="mFnction()">keyboard_arrow_left</i>
                <div>@if($user['id']!=Auth::user()->id)
                     <img  class="profile-img" src="/uploads/avatars/{{ $user['avatar'] }}" style="width:32px; height:32px; position:absolute; top:10px; left:50px; border-radius:50%">
                     @else
                     <img class="profile-img" src="/uploads/avatars/{{ Auth::user()->avatar }}" style="width:32px; height:32px; position:absolute; top:10px; left:50px; border-radius:50%">
                     @endif
                </div>
                <div class="chatbox__headerText">{{$user['name']}}@if($user['id']!=Auth::user()->id)({{"@".$user['username']}})@endif
                    <div class="last-seen"></div>
                </div>
            </div>

            <!-- messages -->
            <div id="box" class="chatbox__messages">
                <?php $isNew = true; $timeAfter = null; $timeBefor = null;?>
                @foreach($messages as $message)
                    <?php
                        $timeBefor = \Carbon\Carbon::parse($message->created_at)->format('y/m/d');
                        if ($timeAfter!=null && $timeAfter!=$timeBefor)
                        {
                            echo "<div class='dateChat'>".$timeBefor."</div>";
                        }elseif ($timeAfter==null)
                            {
                                echo "<div class='dateChat'>".$timeBefor."</div>";
                            }

                        $timeAfter = \Carbon\Carbon::parse($message->created_at)->format('y/m/d');
                        ?>
                    @if($message->user_id==Auth::user()->id)
                        <div id="{{$message->id}}" class="chatbox__messageBox chatbox__messageBox--primary">
                            @if($message->isfile==null)
                            <div class="chatbox__message chatbox__message--primary" >{{$message->text}}
                                <div class="chatbox__tooltip chatbox__tooltip--left">{{substr($message->created_at,10,-3)}}</div>
                                <i id="send_{{$message->id}}" class="icon seen @if($message->seen!=2) notseen @endif">
                                    @if($message->seen==2)
                                        done_all
                                    @else
                                        done
                                    @endif
                                </i>
                            </div>

                            @elseif($message->isfile=='image')
                                <a target="_blank" href="{{$message->text}}"><img onload="scrollBottom()" src="{{$message->text}}" class="chatbox__message chatbox__message--primary image"  >
                                </a>
                                <div class="chatbox__tooltip chatbox__tooltip--left">{{substr($message->created_at,10,-3)}}</div>
                                    <i id="send_{{$message->id}}" class="icon seen @if($message->seen!=2) notseen @endif">
                                        @if($message->seen==2)
                                            done_all
                                        @else
                                            done
                                        @endif
                                    </i>
                            @elseif($message->isfile=='file')
                            <div class="chatbox__message chatbox__message--primary image">file : {{substr($message->text,29)}}
                                <a class="download" target="_blank" href="{{route('download',['link='.$message->text])}}" >download</a>

                                <div class="chatbox__tooltip chatbox__tooltip--left">{{substr($message->created_at,10,-3)}}</div>
                                <i id="send_{{$message->id}}" class="icon seen @if($message->seen!=2) notseen @endif">
                                    @if($message->seen==2)
                                        done_all
                                    @else
                                        done
                                    @endif
                                </i>
                            </div>
                            @endif
                        </div>
                    @else
                            @if ($message->seen==1 && $isNew)
                                <div class="isNew">new messages...</div>
                                <?php $isNew = false; ?>
                            @endif
                        <div id="{{$message->id}}" class="chatbox__messageBox">
                            @if($message->isfile==null)
                            <div class="chatbox__message chatbox__message--get">{{$message->text}}
                                <div class="chatbox__tooltip chatbox__tooltip--right">{{substr($message->created_at,10,-3)}}</div>
                            </div>

                            @elseif($message->isfile=='image')
                                <a target="_blank" href="{{$message->text}}"><img onload="scrollBottom()" class="chatbox__message chatbox__message--get image" src="{{$message->text}}">
                                </a> <div class="chatbox__tooltip chatbox__tooltip--right">{{substr($message->created_at,10,-3)}}</div>
                            @elseif($message->isfile=='file')
                                <div class="chatbox__message chatbox__message--get">file : {{substr($message->text,29)}}
                                    <a class="download" target="_blank" href="{{route('download',['link='.$message->text])}}" >download</a>
                                    <div class="chatbox__tooltip chatbox__tooltip--right">{{substr($message->created_at,10,-3)}}</div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
                @if(count($messages)==0)
                    <div class="no--message" >
                        No messages here yet...
                    </div>
                @endif
            </div>
{{--            scroll to botton--}}
            <script>
                var lastTime;
                var nowTime;
                @if(isset($message->created_at)) lastTime = {{\Carbon\Carbon::parse($message->created_at)->format('ymd')}}; @endif
                scrollBottom();
                function scrollBottom() {
                    var newbox = $('.isNew');

                    if (newbox.length>0)
                    {
                        newbox.get(0).scrollIntoView();
                    }
                    else {
                        var box =document.getElementById('box');
                        box.scrollTo(0,box.scrollHeight);
                    }
                }
            </script>
            <!-- input -->
            <div class="chatbox__inputPanel">
                <i class="icon btn-emoji">tag_faces</i>
                <input id="input" type="text" placeholder="Aa" class="chatbox__input">
                <div class="btn-file noselect"><i class="icon">attachment</i></div>

                <div class="btn-send noselect" style="display: none"><i class="icon">send</i></div>
            </div>
            <!-- form file -->
            <form id="file-form" style="display: none" class="form--file" action="{{route('send')}}"  method="post" enctype="multipart/form-data">
                @csrf
                <input id="file" class="input--file" name="file" type="file" required>
                <input id="isFile" name="isFile" type="text" value="" >
                <input id="for" name="for" type="text" value="" >

            </form>
        </div>

        @include('layouts.list-chat')

        @include('layouts.list-emoji')

        @include('layouts.menu-delete')

    </div>





<!--script for chatbox-->
<script>
    var s =$('.notseen');
    var notreadCount = s.length;
    for(var i = 0;i<notreadCount;i++)
    {
        notRead[notRead.length] = s[i].id.substr(5);
    }


    var countNewSend= 0;
    var input = $('#input');
    input.focus();
    input.keyup(function (e) {
        var key = e.which;
        if(this.value!=='')
        {
            $('.btn-send').show();
            $('.btn-file').hide();
            if (key===13){
                $('.btn-send').click();
            }
        }
        else {
            $('.btn-send').hide();
            $('.btn-file').show();

        }

    });

    $('.btn-send').click(function (e) {
        var text = htmlEncode(input.val());
        if (text!=='') {
            addMessage(countNewSend, 's', text, "01:01",'');

            setTopItemChat(pv,pvName,text,"",pvUsername)

            sendMessage(text, pv, "{{csrf_token()}}", countNewSend);

            countNewSend += 1;
            scrollBottom();
            input.val('');
            $('.btn-send').hide();
            $('.btn-file').show();

        }
        input.focus();
    });


    //emoji..................
    function myFunction(x) {
      var sEmoji = false;
      $('.chatbox').animate({width: '100%',opacity: 1},0);
      if (x.matches) { // If media query matches
        //emoji
        $('.btn-emoji').click(function () {
          if (!sEmoji)
          {
            sEmoji =true;
            $(this).text('close');
            input.focus();
            $('.list--emoji').animate({right: '-7px',opacity: 1},30);
            $('.chatbox').animate({width: '93%',opacity: 1},30);
          }
          else {
            sEmoji = false;
            $(this).text('tag_faces');
            input.focus();
            $('.list--emoji').animate({right: '0',opacity:0},30);
            $('.chatbox').animate({width: '100%',opacity: 1},0);
          }

        });
      } else {
        //emoji
        $('.btn-emoji').click(function () {
          if (!sEmoji)
          {
            sEmoji =true;
            $(this).text('close');
            input.focus();
            $('.list--emoji').animate({right: '-130px',opacity: 1},30);
            $('.chatbox').animate({width: '100%',opacity: 1},0);
          }
          else {
            sEmoji = false;
            $(this).text('tag_faces');
            input.focus();
            $('.list--emoji').animate({right: '0',opacity:0},300);
            $('.chatbox').animate({width: '100%',opacity: 1},0);
          }

        });
      }
    }

    var x = window.matchMedia("(max-width: 887px)")
    myFunction(x) // Call listener function at run time
    x.addListener(myFunction) // Attach listener function on state changes



    $('.item--list--emoji').click(function () {
        var emoji = $(this);
        var t = input.val();
        input.val(t+emoji.text());
        input.focus();
        $('.btn-send').show();
        $('.btn-file').hide();
    })

    


    var isListBox = true
    $('.list--show').click(function () {
        if (!isListBox)
        {
            $('.listbox').animate({left:'-200px',opacity:1},200);
            $(this).text('keyboard_arrow_right');
            isListBox = true;
            $('.news').text('')
        }
        else{
            $('.listbox').animate({left:'0px',opacity:0},200);
            $(this).text('keyboard_arrow_left');
            isListBox = false;
        }
    })



    //delete message
    var select_id = 0;
    function delmessageFunction(m) {
      if (m.matches) { // If media query matches

        $('.chatbox__messageBox--primary').contextmenu(function () {
        if (select_id===0)
        {
            $('.chatbox__messageBox--primary').css('filter','blur(1px)');
            $(this).css('filter','blur(0px)');
            $('.menu-delete').animate({bottom: '-160px',opacity: 1},300);
            select_id = parseInt(this.id);
        }else{
            $('.chatbox__messageBox--primary').css('filter','blur(0px)');
            $('.menu-delete').animate({right: '0',opacity: 0},300);
            select_id = 0;
        }
        return false;
        })    
        
       
      } else {

        $('.chatbox__messageBox--primary').contextmenu(function () {
        if (select_id===0)
        {
            $('.chatbox__messageBox--primary').css('filter','blur(1px)');
            $(this).css('filter','blur(0px)');
            $('.menu-delete').animate({right: '-160px',opacity: 1},300);
            select_id = parseInt(this.id);
        }else{
            $('.chatbox__messageBox--primary').css('filter','blur(0px)');
            $('.menu-delete').animate({right: '0',opacity: 0},300);
            select_id = 0;
        }
        return false;
        })
        

      }
    }

    var m = window.matchMedia("(max-width: 887px)")
    delmessageFunction(m) // Call listener function at run time
    m.addListener(delmessageFunction) // Attach listener function on state changes





    
    

    $('#box').click(function () {
        $('.chatbox__messageBox--primary').css('filter','blur(0px)');
        $('.menu-delete').animate({right: '0',opacity: 0},300);
        select_id = 0;
    })

    $('.btn-file').click(function () {
        $('.input--file').click();
    })
    $('.input--file').change(function () {
        countNewSend += 1;
        setTopItemChat(pv,pvName,'file',"",pvUsername)
        sendFile(this,countNewSend);
        scrollBottom();
        input.focus();

    })

   
</script>



<script>
function mFnction() {
 var q = document.getElementsByClassName("chatbox");
 q[0].style.display = "none";
}
</script>



@endsection
