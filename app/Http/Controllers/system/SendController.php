<?php

namespace App\Http\Controllers\system;

use App\Chat;
use App\Http\Controllers\Controller;
use App\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SendController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $id = Auth::user()->id;


        if (\request()->isFile==null)
        {
            $this->validate(\request(),[
                'text'=>'required',
                'for'=>'required',
            ]);
            $text = \request()->text;
            $forUser = \request()->for;

            $message =  Messages::create([
                'user_id'=>$id,
                'text'=>$text,
                'for'=>$forUser,
            ]);
        }else{
            $this->validate(\request(),[
                'file'=>'required',
                'for'=>'required',
            ]);
            $forUser = \request()->for;

            if (\request()->isFile=='image')$text = 'image';
            else $text = 'file';

            $path = $this->upload();
            $message =  Messages::create([
                'user_id'=>$id,
                'text'=>$path,
                'for'=>$forUser,
                'isfile'=>$text,
            ]);
        }


        $chat = Chat::where([['user_id', Auth::user()->id],['in',$forUser]])->first();

        if ($chat=='')
        {
            Chat::create([
               'user_id'=>$id,
               'in'=>$forUser,
                'status'=>$text,
                'new'=>false
            ]);
        }
        else{
            $chat->update([
                'status'=>$text,
            ]);
        }
        if ($forUser!=Auth::user()->id)
        {$chat = Chat::where([['user_id', $forUser],['in',Auth::user()->id]])->first();
        if ($chat==null)
        {
            Chat::create([
                'user_id'=>$forUser,
                'in'=>$id,
                'status'=>$text,
                'new'=>true,
            ]);
        }
        else{
            $chat->update([
                'status'=>$text,
                'new'=>true,
            ]);
        }
        }


        return $message;

    }

    function upload()
    {
        $direct = '/upload/'.date('Y').'/'.date('m');
        $destination = public_path().$direct;
        if (!is_dir($destination))
        {
            mkdir($destination,0777,true);
        }
        $destination = $destination.'/';
        $milli = microtime();
        $fileName = date('ymdHis'.$milli[2]);
        $file = \request()->file('file');
        $formatFile = \request()->file->getClientOriginalName();
        $file->move($destination,$fileName.$formatFile);

        return $direct.'/'.$fileName.$formatFile;
    }

    function delete()
    {
        return Messages::find(\request()->id)->update([
           'delete'=>1,
        ]);
    }
    function deleted()
    {
        return Messages::find(\request()->id)->delete();
    }

    function delete_pv()
    {
        return Chat::where([['user_id',Auth::user()->id],['in',\request()->id]])->delete();
    }
}
