<?php

namespace App\Http\Controllers;

use App\Http\Service\Treatment;
use App\Jobs\ProcessReport;
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
            'email' => 'email|required',
        ]);

        if ($validator->fails()) {
            return new Response($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $report = Report::where('git_repository', '=', $request->git_repository)->first();

        // todo check if url exist, check if domain name is github
        if ($report instanceof Report) {
            return $this->update($request, $report);
        }

//        $report = new Report();

//        $report->git_repository = $request->git_repository;
        $data = array('git_repository' => $request->git_repository, 'email' => $request->email, 'action' => 'create');
//
//        try {
//            ProcessReport::dispatch($report);
//        } catch (Exception $e) {
//            dd($e->getMessage());
//        }
//        \Storage::disk('local')->put('file.txt', 'Content idk lol mdr');
        // todo create the report && recup the mail via $request->email
//        $treatment = new Treatment();
//        $reportFilename = $treatment->gitClone($request->git_repository);
//        $report->filename = $reportFilename;

        // $report->save();

        if ($request->user() !== null) {
            $data['user_id'] = $request->user()->id;
        }

        ProcessReport::dispatch($data);

        return new Response(array('message' => 'done'), Response::HTTP_CREATED);
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

        $data = array('git_repository' => $report->git_repository, 'email' => $request->email, 'action' => 'update');

        if ($request->user() !== null) {
//            $data = array(
//                'git_repository' => $report->git_repository,
//                'email' => $request->email,
//                'action' => 'update',
//                'report' => $report,
//                'user_id' => $report->users()->id
//                );
//            $data['user_id'] = $report->users()->id;
        } else {
//            $data = array(
//                'git_repository' => $report->git_repository,
//                'email' => $request->email,
//                'action' => 'update',
//                'report' => $report
//            );
        }
//        $data['report'] = $report;

//        dd($data);
        ProcessReport::dispatch($data);
        // todo create the report
        // $report->filename('test')

        return new Response(array('message' => 'done'), Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function getByUser(Request $request)
    {
        $user = $request->user();
        $reports = array();
        foreach ($user->reports as $report) {
            $reports[] = $report->toArray();
        }

        return new Response($reports, Response::HTTP_OK);
    }
}
