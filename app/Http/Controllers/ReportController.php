<?php

namespace App\Http\Controllers;

use App\Report;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reports = Report::all();
        return new Response($reports->toArray(), Response::HTTP_OK);
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
            'git_repository' => 'max:191|url',
            'user_id' => 'integer|nullable',
        ]);

        if ($validator->fails()) {
            return new Response($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $report = Report::where('git_repository', '=', $request->git_repository)->first();

        // todo check if url exist, check if domain name is github

        if ($report instanceof Report) {
            return $this->update($request, $report);
        }

        $report = new Report();

        $report->git_repository = $request->git_repository;

        // todo create the report

        $report->save();

        if ($request->user_id !== null) {
            $report->users()->attach($request->user_id);
        }

        return new Response($report->toArray(), Response::HTTP_CREATED);
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

        return new Response($report->toArray(), Response::HTTP_CREATED);
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

        return new Response($reports, Response::HTTP_OK);
    }
}
