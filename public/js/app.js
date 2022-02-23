
function htmlEncode(text) {
    text = $.trim(text);
    return text.replace(/[&"'\<\>]/g,function (c) {
        switch (c) {
            case "&":
                return "&amp;";
            case "'":
                return "&#39;";
            case '"':
                return "&quot;";
            case "<":
                return "&lt;";
            default:
                return "&gt;"
        }
    })
}

itemChatSelected("user_"+pv);
function itemChatSelected(idItem) {
    $('#'+idItem).addClass("item--chat--focus");
}
function addItemListChat(type,id,name,status,time,username) {
    var layout;
    if (id.toString()===myid.toString()) name = "خودت";
    var div = document.createElement("div");
    div.id = 'user_'+id;
    $(div).click(function () {
        $('.item--chat').removeClass('item--chat--focus');
        itemChatSelected(this.id);
        openChat(this.id)
    })
    var divName = document.createElement("div");
    divName.className = "item--chat--name";
    divName.innerHTML = name;
    var divTime = document.createElement("div");
    divTime.id = 'user_time_'+id;
    divTime.className = "item--chat--time";
    divTime.innerHTML = time;
    if (type==="main")
    {
        div.className = "item--chat";
        layout = document.getElementsByClassName("list")[0];
        layout.prepend(div);
        var newdiv =document.createElement("div");
        if (pv.toString()!==id.toString()) newdiv.className= "chatbox__onlineDot item--chat--new";
         newdiv.id = "new_"+id;

        var divstatus =document.createElement("div");
        divstatus.className= "item--chat--status";
        divstatus.id = 'user_status_'+id;
        divstatus.innerHTML = status;
        var divId = document.createElement("div");
        divId.className ="item--chat--id";
        if (username.length>7)
        {
            var user = username.substr(0,7)+"...";
        }else
        {
            var user = username.substr(0,7);
        }

        divId.innerHTML = "@"+user;


    }else if (type==="search")
    {
        div.className = "item--chat item--search";
        layout = document.getElementsByClassName("show--item--search")[0];
        layout.appendChild(div);
    }
    div.appendChild(divName);
    div.appendChild(divTime);
    div.appendChild(divstatus);
    div.appendChild(newdiv);
    div.appendChild(divId);
}
function addMessage(id ,s,text,date,isfile) {
    $('.isNew').remove();

    var layout = document.getElementById('box');

    var div = document.createElement("div");
    div.oncontextmenu = function () {
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
    };
    div.id = "new_"+id;
    if(s==='r') div.className = "chatbox__messageBox";
    else div.className = "chatbox__messageBox chatbox__messageBox--primary";


    var classImage = ''
    if (isfile==='')
    {
        var div2 = document.createElement("div");
        div2.innerHTML = text;

    }else if (isfile==='image')
    {

        var link = document.createElement("a");
        link.id = "link_"+id;
        link.target = '_blank';
        link.href = text;
        var div2 = document.createElement("img");
        div2.onload = function(){
            scrollBottom();
        };
        classImage = "image";
        div2.src = text;
    }else if (isfile==='file')
    {
        var div2 = document.createElement("div");
        if (s==='s') {
            div2.innerHTML = "file : "+text;
            var a = document.createElement("a");
            a.className = 'download';
            a.innerHTML = "ok";
        }else if (s==='r')
        {
            div2.innerHTML = "file : "+text.substr(29);
            var a = document.createElement("a");
            a.className = 'download';
            a.innerHTML = "download";
            a.href = '/download?link='+text;
            a.target = "_blank";
        }
        div2.appendChild(a);
    }

    if (s==='r')
        div2.className = "chatbox__message chatbox__message--get "+classImage;
    else div2.className = "chatbox__message chatbox__message--primary "+classImage;


    var time = document.createElement("div");
    time.id = "timeN_"+countNewSend;
    if (s==='s')
    {
        time.className = "chatbox__tooltip chatbox__tooltip--left";

        var isSend = document.createElement("i");
        isSend.className = "seen icon";
        isSend.id = "sendN_"+countNewSend;
        isSend.innerHTML = "query_builder";

    }
    else if (s==='r') time.className = "chatbox__tooltip chatbox__tooltip--right";
    time.innerHTML = date;


    if (lastTime!==nowTime)
    {
        var divTimeNow = document.createElement("div");
        divTimeNow.className = "dateChat";
        var d = nowTime.toString();
        divTimeNow.innerHTML = d.substr(0,2)+"/"+d.substr(2,2)+"/"+d.substr(4,2)
        lastTime = nowTime;
        layout.appendChild(divTimeNow);
    }
    if (isfile!=='image') {
        div2.appendChild(time);
        if (s === 's') div2.appendChild(isSend);
        div.appendChild(div2);

    }else if (isfile==='image')
    {
        div.appendChild(time);
        link.appendChild(div2)
        if (s === 's') div.appendChild(isSend);
        div.appendChild(link);
    }
    else {
        div.appendChild(time);
        if (s === 's') div.appendChild(isSend);
        div.appendChild(div2);

    }
    layout.appendChild(div);
    scrollBottom();
    $('.no--message').remove();

}

function openChat(id,layout) {
    id = id.substring(5,id.length);
    if (id!==pv)
    {
        var box =$('.chatbox');
        box.css('filter','blur(1px)');
        window.location.href="/?pv="+id;

    }
}
function sendMessage(text,forUser,token,idMessage) {
    $.post('/send/message',{text:text,for:forUser,_token:token},function (result) {
        var message = $('#new_'+idMessage);
        var send = $('#sendN_'+idMessage);
        var time = $('#timeN_'+idMessage);
        var listChatTime = $('#user_time_'+result['for']);
        message.attr("id",result['id']);
        send.attr("id","send_"+result['id']);
        send.text("done");
        time.text(result['created_at'].substr(11,5));
        time.attr("id","time_"+result['id']  );
        listChatTime.text(result['created_at'].substr(11,5));
        notRead[notRead.length] = result['id'];
    })
}
function sendFile(input,idMessage) {
    var file = $('.input--file').val();
    if (file!=='')
    {

        var type = document.getElementById('file').files[0].type;
        if (type.substr(0,5)==='image')
        {
            var reader = new FileReader();
            reader.onload = function(e)
            {
                addMessage(idMessage, 's', e.target.result, "01:01",'image');
            }
            reader.readAsDataURL(input.files[0])
            $('#isFile').val("image");
        }else
        {
            addMessage(idMessage, 's',document.getElementById('file').files[0].name, "01:01",'file');
            $('#isFile').val("file");
        }
        $('#for').val(pv);
        var form = document.getElementById('file-form');
        var fd = new FormData(form);
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function()
        {
            if (this.readyState == 4)
            {
                var result = JSON.parse(this.responseText);
                var message = $('#new_'+idMessage);
                var send = $('#sendN_'+idMessage);
                var time = $('#timeN_'+idMessage);
                var listChatTime = $('#user_time_'+result['for']);
                message.attr("id",result['id']  );
                send.attr("id","send_"+result['id']  );
                send.text("done");
                time.text(result['created_at'].substr(11,5));
                time.attr("id","time_"+result['id']  );
                listChatTime.text(result['created_at'].substr(11,5));
                if (result['isfile']==='image')
                {
                    var a = $('#link_'+idMessage);
                    a.attr('href',result['text']);
                }
                notRead[notRead.length] = result['id'];
            }
        }
        xhr.open("POST","/send/message",true);
        xhr.send(fd);
    }
}

function getData() {
    $.post('/get',{_token:token,unread:notRead,pv:pv},function (result) {
        if (result!=='')
        {
            $('.last-seen').text(result['last_seen']);
            nowTime = parseInt(result['now']);
            var messages = result['messages'];
            var deleted = result['deleted'];
            var countdeleted = deleted.length;
            var read = result['read'];
            var countread = read.length;
            var count = messages.length;
            for (var i = 0;i<count;i++)
            {
                var message = messages[i];
                if (message['user_id'].toString()===pv.toString() && message['user_id'].toString()!==myid)
                {
                    var file = '';
                    if (message['isfile']==="image" ||message['isfile']==="file") file = message['isfile'];
                    addMessage(message['id'],"r",message['text'],message['created_at'].substr(11,5),file);
                }
                else if (message['user_id'].toString()!==myid) {
                    // alert('new message');
                    if (!isListBox)
                    addNews()
                }

                setTopItemChat(message['user_id'],message['name'],message['text'],message['created_at'],message['username']);


            }
            for (var j = 0;j<countread;j++)
            {
                var send = $('#send_'+read[j]);
                send.text("done_all");
                for(var b =0;b<notRead.length;b++)
                {
                    if (notRead[b]!==null && notRead[b].toString()===read[j].toString())
                    {
                        notRead[b] = null;
                    }
                }
            }

            for (var n= 0;n<countdeleted;n++)
            {
     

                $('#'+deleted[n]).remove();
                $('#new_'+deleted[n]).remove();
                $.post('/deleted',{_token:token,id:deleted[n]},function (result) {

                });
            }
        }
    });
}
 setInterval('getData()',1000);//check 1s

function setTopItemChat(id,name,status,time,username) {
    var item = $('#user_'+id);
    status = status.substr(0,10);
    if (item.length>0)
    {
        $('.list').prepend(item);
        var listChatTime = $('#user_time_'+id);
        var listChatStatus = $('#user_status_'+id);
        var listChatNew = $('#new_'+id);
        listChatTime.text(time.substr(11,5));
        listChatStatus.text(status)
        if (pv.toString()!==id.toString()) listChatNew.addClass("chatbox__onlineDot item--chat--new");

    }else
    {
        addItemListChat("main",id,name,status,time.substr(11,5),username);
        itemChatSelected("user_"+id);

    }
}
function addNews() {
    var newsElement = $('.news');
    var oldNews = parseInt(newsElement.text());
    if (isNaN(oldNews))
    {

        newsElement.text("1");
    }else
    {
        newsElement.text(oldNews+1);
    }
}
//delete message
function deleteMessage() {
    $('#'+select_id).remove();
    $('.chatbox__messageBox--primary').css('filter','blur(0px)');
    $('.menu-delete').animate({right: '0',opacity: 0},300);
    $.post('/delete',{id:select_id,_token:token},function (result) {

    })
    select_id =0;
}
function deleteCancel() {
    $('.chatbox__messageBox--primary').css('filter','blur(0px)');
    $('.menu-delete').animate({right: '0',opacity: 0},300);
    select_id =0;
}
function deletePv() {
    $('#user_'+selectedPV).remove();
    $('.menu-delete-pv').animate({right: '0',opacity: 0},300);
    $.post('/delete/pv',{id:selectedPV,_token:token},function (result) {

    })
    selectedPV =0;
}
function deletePvCancel() {
    $('.menu-delete-pv').animate({right: '0',opacity: 0},300);
    selectedPV =0;
}
