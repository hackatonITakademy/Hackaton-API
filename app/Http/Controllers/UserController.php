<?php

namespace App\Http\Controllers;

use App\Report;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reports = Report::all();
        return new Response($reports->toArray(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|unique:users|email',
            'password' => 'required|max:191|confirmed',
        ]);

        if ($validator->fails()) {
            return new Response($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            dd($validator->errors());
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        return new Response($user->toArray(), Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $report = Report::find($id)->first();

        if ($request->user_id !== null) {
            $report->users()->attach($request->user_id);
        }

        // todo create the report
        // $report->filename('test')

        return new Response($report->toArray(), 201);
    }

    /**
     * @param $id
     *
     * @return Response
     */
    public function getByUser($id)
    {
        $users = User::find($id)->first();
        $reports = array();
        foreach ($users->reports as $report) {
            $reports[] = $report->toArray();
        }

        return new Response($reports, 200);
    }
}
