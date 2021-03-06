<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use App\DB\Role;
use App\DB\Course;
use App\DB\Category;
use App\DB\Lecture;
use App\DB\Type;
use App\DB\Page;
use Mail;

class IndexController extends Controller
{
    
	public function anyIndex() {
		$data = array();
		$data['courses'] = Course::all();
		return view('index',$data);
	}

	public function postRegister(Request $request) {
		$data = $request->all();
		$data['message'] = "";
   		$data['errors'] = array();

     	$validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'student_id' => 'required_if:optionsRole,student'
        ]);

       if ($validator->fails()) {
       		$data['errors'] = $validator->errors()->all();
        } else {
       		$new_user =  array(
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' =>  \Hash::make($request->input('password')),
            'student_id' => $request->input('student_id')
            );
       		$user = User::create($new_user);
       		$role = Role::where('name', $request->input('optionsRole'))->first();
       		$user->roles()->attach($role->id);
       		$data['message'] = "registrated";
        }

		return view('register',$data);
	}


	public function getRegister() {
		if(\Auth::check()) {
			return redirect('/');
		}
		$data = array();
		$data['email'] = "";
		$data['name'] = "";
		$data['optionsRole'] = "student";
		$data['student_id'] = "";
		$data['errors'] = array();
		$data['message'] = "";
		return view('register',$data);
	}


	public function anyContactus(Request $request) {
		$data = array();
		$i = array();
		$i['f_name'] = $request->input("name","");
		$i['f_surname'] = $request->input("surname","");
		$i['f_email'] = $request->input("email","");
		$i['f_phone'] = $request->input("phone","");
		$i['f_message'] = $request->input("message","");


		$data['sent'] = false;

		if(($i['f_email'] != "") && ($i['f_message'] != "")) {


			Mail::send('emails.feedback', $i, function($message) use ($i)
			{
				$message->from($i['f_email'], $i['f_name'].' '.$i['f_surname']);
    			$message->to('darkromanovich@gmail.com',"DD");
    			$message->to("ed.sherban@gmail.com","Ed");
    			$message->subject('Feedback from nteconlinecourses.tk');
			});
			$data['sent'] = true;
		}

		return view('contactus',$data);
	}

}
