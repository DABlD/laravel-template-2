<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Auth;

use App\Helpers\Helper;

class UserController extends Controller
{
    public function __construct(){
        $this->table = "users";
    }

    public function get(Request $req){
        $array = DB::table($this->table)->select($req->select);

        // IF HAS SORT PARAMETER $ORDER
        if($req->order){
            $array = $array->orderBy($req->order[0], $req->order[1]);
        }

        // IF HAS WHERE
        if($req->where){
            $array = $array->where($req->where[0], isset($req->where[2]) ? $req->where[1] : "=", $req->where[2] ?? $req->where[1]);
        }

        // IF HAS WHERE2
        if($req->where2){
            $array = $array->where($req->where2[0], isset($req->where2[2]) ? $req->where2[1] : "=", $req->where2[2] ?? $req->where2[1]);
        }

        // IF HAS JOIN
        if($req->join){
            $alias = substr($req->join, 1);
            $array = $array->join("$req->join as $alias", "$alias.fid", '=', "$this->table.id");
        }

        $array = $array->get();

        // IF HAS LOAD
        if($array->count() && $req->load){
            foreach($req->load as $table){
                $array->load($table);
            }
        }

        // IF HAS GROUP
        if($req->group){
            $array = $array->groupBy($req->group);
        }

        echo json_encode($array);
    }

    public function store(Request $req){
        $data = new User();
        $data->username = $req->username;
        $data->fname = $req->fname;
        $data->mname = $req->mname;
        $data->lname = $req->lname;
        $data->role = $req->role;
        $data->email = $req->email;
        $data->birthday = $req->birthday;
        $data->gender = $req->gender;
        $data->address = $req->address;
        $data->contact = $req->contact;
        $data->password = $req->password;

        Helper::log(auth()->user()->id, "created $data->role user", $data->id);

        echo $data->save();
    }

    public function update(Request $req){
        if($req->hasFile('avatar')){
            $user = User::find($req->id);

            $temp = $req->file('avatar');
            $image = Image::make($temp);

            $name = $user->lname . '_' . $user->fname . '-' . time() . "." . $temp->getClientOriginalExtension();
            $destinationPath = public_path('uploads/' . env('UPLOAD_URL'));

            $image->resize(250, 250);
            $image->save($destinationPath . $name);
            $user->avatar = 'uploads/' . env('UPLOAD_URL') . $name;
            $user->save();
        }
        else{
            DB::table($this->table)->where('id', $req->id)->update($req->except(['id', '_token', 'avatar']));
        }

        echo Helper::log(auth()->user()->id, 'updated user', $req->id);
    }

    public function updatePassword(Request $req){
        $user = User::find($req->id);
        $user->password = $req->password;

        Helper::log(auth()->user()->id, 'updated password of user', $req->id);
        
        $user->save();
    }

    public function delete(Request $req){
        User::find($req->id)->delete();
        Helper::log(auth()->user()->id, 'deleted user', $req->id);
    }

    public function index(){
        return $this->_view('index', [
            'title' => ucfirst($this->table)
        ]);
    }

    private function _view($view, $data = array()){
        return view("$this->table.$view", $data);
    }
}
