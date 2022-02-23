<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Messages;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $lists = Chat::where('user_id', Auth::user()->id)->latest('updated_at')->get();
        $messages = [];
        $user = User::find(\request()->pv);
        if ($user != null) {
            $chat = $lists
                ->where('in', \request()->pv)->first();
            if ($chat != null) {
                $messages = Messages::where([['user_id', Auth::user()->id], ['for', \request()->pv], ['delete', null]])
                    ->orwhere([['user_id', \request()->pv], ['for', Auth::user()->id], ['delete', null]])->get();

                $unmessages = Messages::where([['user_id', \request()->pv], ['for', Auth::user()->id], ['delete', null]])
                    ->where('seen', '!=', 2)->get();
                foreach ($unmessages as $message) {
                    $message->update([
                        'seen' => 2
                    ]);
                }
                $chat->timestamps = false;
                $chat->new = false;
                $chat->save();
            }
        } else {
            $user["name"] = "خودت";
            $user["id"] = Auth::user()->id;
            $user["username"] = Auth::user()->username;
            $messages = Messages::where([['user_id', Auth::user()->id], ['for', Auth::user()->id]])->where('delete', '=', null)->get();

        }
        $listChat = [];
        $i = 0;
        foreach ($lists as $list) {
            $i++;
            $userInList = User::find($list->in);
            $listChat[$i]['id'] = $userInList->id;
            $listChat[$i]['name'] = $userInList->name;
            $listChat[$i]['username'] = $userInList->username;
            $listChat[$i]['status'] = $list->status;
            $listChat[$i]['new'] = $list->new;

            $time = Carbon::parse($list->updated_at);

            $now = Carbon::now()->format('ymd');
            if ($now > $time->format('ymd')) {
                $t = $now - $time->format('ymd');
                if ($t == 1)
                    $listChat[$i]['time'] = "دیروز";
                else
                    $listChat[$i]['time'] = $time->format('y/n/d');
            } else
                $listChat[$i]['time'] = $time->format('H:i');

        }
        return view('chat', compact('listChat', 'user', 'messages'));
    }

    public function search()
    {
        $text = request()->name;
        $users = User::where('username', 'like', $text . "%")->get();
        return $users;
    }

    public function settings()
    {
        return view('settings');
    }

    public function edit()
    {

        User::find(Auth::user()->id)->update([
           'name'=>request()->name,
           'username'=>request()->username,
        ]);
        return redirect()->back();

    }
    public function change_password()
    {
        $this->validate(request(),[
           'old_password' =>'required',
           'new_password' =>'required',
        ]);
        $old_password = request()->old_password;
        $new_password = request()->new_password;
        if (password_verify($old_password,Auth::user()->getAuthPassword()))
        {
            User::find(Auth::user()->id)->update([
               'password'=>Hash::make($new_password),
            ]);
            return redirect()->back();
        }
        else
            return 'Password not set';
    }
}
