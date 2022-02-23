<div class="listbox">
    {{--    search--}}
    <div id="search" class="search--box">
        <i class="icon icon--search">search</i>
        <input type="text" id="search-input" placeholder="search">
    </div>
    <a href="{{route('settings')}}">
        <div class="info--box">
            <div class="info--name">{{Auth::user()->name}} <i class="icon" style="position: absolute;right: 20px;padding-top: 2px">edit</i> </div>
        </div>
    </a>

    <div class="list">
        <?php $c = 0 ?>
        @foreach($listChat as $list)

            <div id="user_{{$list['id']}}" class="item--chat flip">
                <div class="item--chat--name">@if($list['id']!=Auth::user()->id){{$list['name']}}
                @else
                        خودت
                    @endif</div>

                <div id="user_time_{{$list['id']}}" class="item--chat--time">{{$list['time']}}</div>
                <div id="user_status_{{$list['id']}}" class="item--chat--status">{{$list['status']}}</div>
                <div id="new_{{$list['id']}}" class="@if($list['new']==true)chatbox__onlineDot item--chat--new @endif"></div>
                <div class="item--chat--id">
                    {{'@'.substr($list['username'],0,7)}}@if(strlen($list['username'])>7)... @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="show--item--search" style="display: none">
        <div class="item--search--title" ></div>
    </div>
</div>
<script>

    $('#search-input').keyup(function () {
        $('.item--search').remove();
        if (this.value !== '') {
            $('.list').hide();
            $('.info--box').hide();
            $('.item--search--title').text("Searching...");
            $('.show--item--search').show();
            $('.icon--search').text('close');
            search(this.value,"{{csrf_token()}}");

        } else {
            $('.list').show();
            $('.info--box').show()
            $('.show--item--search').hide();
            $('.icon--search').text('search');

        }
    });

    $('.icon--search').click(function () {
        var inputSearch = $('#search-input')
        if (inputSearch.val()!=='')
        {
            inputSearch.val("");
            $('.list').show();
            $('.info--box').show()
            $('.show--item--search').hide();
            $('.icon--search').text('search');

        }
    });

    $('.item--chat').click(function () {
        $('.item--chat').removeClass('item--chat--focus');
        itemChatSelected(this.id);
        openChat(this.id)
    })

    function search(text,token) {

        $.post("/search",{name:text,_token:token},function (result) {
            var count = result.length;
            if (count===0)
            {
                $('.item--search--title').text("None");
            }else
            {
                for (var i = 0;i<count;i++)
                {
                    var data = result[i];
                    addItemListChat("search",data['id'],data['name'],"",data['last_seen']);
                }
                $('.item--search--title').text("find "+count+" user.");
            }
        })

    }


    //delete PV
    var selectedPV = 0;
    function delpvFunction(p) {
      if (p.matches) { // If media query matches

        $('.item--chat').contextmenu(function () {
        if (selectedPV===0)
        {

            $('.menu-delete-pv').animate({bottom: '-160px',opacity: 1},300);
            selectedPV = parseInt(this.id.substr(5));
            $('body').click(function () {
                $('.menu-delete-pv').animate({right: '0',opacity: 0},300);
                selectedPV = 0;
            })
        }else{
            $('.menu-delete-pv').animate({right: '0',opacity: 0},300);
            selectedPV = 0;
        }
        return false;
        })     
        
       
      } else {

        $('.item--chat').contextmenu(function () {
        if (selectedPV===0)
        {

            $('.menu-delete-pv').animate({right: '-160px',opacity: 1},300);
            selectedPV = parseInt(this.id.substr(5));
            $('body').click(function () {
                $('.menu-delete-pv').animate({right: '0',opacity: 0},300);
                selectedPV = 0;
            })
        }else{
            $('.menu-delete-pv').animate({right: '0',opacity: 0},300);
            selectedPV = 0;
        }
        return false;
        })
        

      }
    }

    var p = window.matchMedia("(max-width: 887px)")
    delpvFunction(p) // Call listener function at run time
    p.addListener(delpvFunction) // Attach listener function on state changes



    

</script>
