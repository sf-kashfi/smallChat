<?php

namespace App\Http\Controllers\system;

use App\Chat;
use App\Http\Controllers\Controller;
use App\Messages;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function GuzzleHttp\Promise\unwrap;

class GetDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $id = Auth::user()->id;
        $pv = \request()->pv;
        $pvName = User::find($pv)['name'];
        $messageForPv = false;
        if (\request()->unread!=null) $unreads = \request()->unread;
        else $unreads = [];
        $Cunread = count($unreads);
        $data = [];
        $data['read'] = [];
        $data['now'] = Carbon::now()->format('ymd');
        $data['messages'] = [];
        $messages = Messages::where([['for',$id],['seen',false],['delete',null]])->get();
//        $chats = Chat::where([['user_id',$id],['status','new']])->get();
        if(count($messages)>0)
        {
            $countNewMessage = 0;
            foreach ($messages as $message) {
                $data['messages'][$countNewMessage]['id'] = $message->id;
                $data['messages'][$countNewMessage]['text'] = $message->text;
                $data['messages'][$countNewMessage]['isfile'] = $message->isfile;
                $data['messages'][$countNewMessage]['created_at'] =  $message->created_at;
                $data['messages'][$countNewMessage]['seen'] =  $message->seen;
                $data['messages'][$countNewMessage]['user_id'] =  $message->user_id;
                $data['messages'][$countNewMessage]['name'] =  $pvName;
                $data['messages'][$countNewMessage]['username'] =  User::find($message->user_id)['username'];
                if ($message->user_id==$pv)
                $message->update([
                    'seen'=>2,
                ]);
                else
                    $message->update([
                        'seen'=>1,
                    ]);
                $countNewMessage++;
            if ($message->user_id==$pv) $messageForPv = true;
            }
            if ($messageForPv==true)
            {
                $chat = Chat::where('in',$pv)->first();
                $chat->timestamps = false;
                $chat->new = false;
                $chat->save();
            }
        }

        for ($i = 0;$i<$Cunread;$i++)
        {
            $unread = $unreads[$i];
            if ($unread!='') {
                $m = Messages::find($unread);
                if ($m['seen'] == 2) {
                    $data['read'][] = $unread;
                }
            }
        }

        $now = Carbon::now();
        User::find($id)->update([
           'last_seen'=> $now,
        ]);

        $last_seen = User::find($pv)->last_seen;

        $dateNow = $now->format('ymdHi');
        $datePV = Carbon::parse($last_seen)->format('ymdHi');
        if ($datePV==$dateNow)
        {
            $data['last_seen'] = "online";
        }elseif(substr($dateNow,0,-4)-substr($datePV,0,-4)==1)
        {
            $data['last_seen'] = " دیروز ".Carbon::parse($last_seen)->format('H:i');
        }else
        {
            $data['last_seen'] = Carbon::parse($last_seen)->format('y/m/d H:i');
        }
        $messageDeleted = Messages::where([['for',$id],['user_id',$pv],['delete','!=',null]])->get();
        $data['deleted'] =[];
        foreach ($messageDeleted as $item)
        {
            $data['deleted'][] = $item->id;
        }

        return $data;
    }

    public function download()
    {
        $link = \request()->link;
        $headers = array(
            'Content-Type: application'
        );
        return Response()->download(public_path().$link,substr($link,29),$headers);
    }
}
