<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Validator;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\JobProposal;
use Auth;
use Carbon\Carbon;

class JobController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $jobs = Job::with(['user', 'jobCategory', 'jobImages']);

        if ($request->has('status')) {
            $jobs->where('status', $request->get('status'));
        }

        return response()->json($jobs->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */



    public function store(Request $request)
    {
        $normal_validations = [
            'job_category_id' =>'required|numeric',
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'period' => 'required|numeric',
            'address1' => 'required',
            'address2' => 'required',
            'city' => 'required'

        ];

        $validator = Validator::make($request->all(), $normal_validations);

        if ($validator->fails()) {
            return response()->json([
                'status' => False,
                'code' => 422,
                'message' => 'error',
                'data' => ['errors'=>$validator->errors()],
            ], 422);
        }
        $user = User::where('api_token', request('api_token'))->first();
        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['status'] = 0;
        $Job = Job::query()->create($data);

        return response()->json($Job);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Job = Job::with(['user', 'jobCategory', 'jobImages', 'jobProposals.user'])->findOrFail($id);

        return response()->json($Job);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $normal_validations = [
            'job_category_id' =>'required|numeric',
            'title' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'period' => 'required|numeric',
            'address1' => 'required',
            'address2' => 'required',
            'city' => 'required'
        ];

        $validator = Validator::make($request->all(), $normal_validations);

        if ($validator->fails()) {
            return response()->json([
                'status' => False,
                'code' => 422,
                'message' => 'error',
                'data' => ['errors'=>$validator->errors()],
            ], 422);
        }
        $user = User::where('api_token', request('api_token'))->first();
        $data = $request->all();
        $data['user_id'] = $user->id;
        $Job = Job::where('id', $id)->where('user_id',$user->id)->first();
        if ($Job == null) {
            return response()->json([
                'status' => False,
                'code' => 422,
                'message' => 'error',
                'data' => ['errors'=>"Not Found"],
            ], 422);
        }
        $Job->update($data);

        return response()->json($Job);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('api_token', request('api_token'))->first();
        $Job = Job::where('id', $id)->where('user_id',$user->id)->first();
        if ($Job == null) {
            return response()->json([
                'status' => False,
                'code' => 422,
                'message' => 'error',
                'data' => ['errors'=>"Not Found"],
            ], 422);
        }
        Job::query()->findOrFail($id)->delete();

        return response()->json(['status' => 'success']);
    }

    public function addProposal(Request $request)
    {
      //  return($request->all());
        $normal_validations = [
            'job_id' => 'required|numeric',
            'type' => 'required|numeric',
            'price' => 'required|numeric',
            'period' => 'required|numeric',
            'description' => 'required'
        ];

        $validator = Validator::make($request->all(), $normal_validations);
        if ($validator->fails()) {
            return response()->json([
                'status' => False,
                'code' => 422,
                'message' => 'error',
                'data' => ['errors'=>$validator->errors()],
            ], 422);
        }
        $user = User::where('api_token', request('api_token'))->first();
        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['status'] = 0;
        $proposal = JobProposal::query()->create($data);

        $job = Job::query()->findOrFail($request->input('job_id'));
        Notification::query()->create([
            'user_id' => $job->user_id,
            'message' => 'New proposal added for your job',
            'job_id' => $job->id,
        ]);

        return response()->json($proposal);
    }

    public function acceptProposal(Request $request, $proposalId)
    {
        $proposal = JobProposal::query()->findOrFail($proposalId);
        $user = User::where('api_token', request('api_token'))->first();
        $Job = Job::where('id', $proposal->job_id)->where('user_id', $user->id)->first();
        if ($Job == null) {
            return response()->json([
                'status' => False,
                'code' => 422,
                'message' => 'error',
                'data' => ['errors'=>"Not Found"],
            ], 422);
        }
        $proposal->status = 1;
        $proposal->accepted_at = Carbon::now();
        $proposal->save();

        $Job->status = 1;
        $Job->save();

        Notification::query()->create([
            'user_id' => $proposal->user_id,
            'message' => 'Your proposal has been accepted',
            'job_id' => $Job->id,
        ]);

        return response()->json($proposal);
    }

    public function finishProposal(Request $request, $proposalId)
    {
        $proposal = JobProposal::query()->findOrFail($proposalId);
        $proposal->status = 2;
        $proposal->finished_at = Carbon::now();
        $proposal->save();

        $proposal->job->status = 2;
        $proposal->job->save();

        Notification::query()->create([
            'user_id' => $proposal->job->user_id,
            'message' => 'Your job has been delivered',
            'job_id' => $proposal->job_id,
        ]);

        return response()->json($proposal);
    }

    public function rateProposal(Request $request, $proposalId)
    {
        $validated = $request->validate(['rate' => 'required|integer|between:0,5', 'rate_message' => 'nullable']);

        $proposal = JobProposal::query()->findOrFail($proposalId);
        $proposal->update($validated);

        $userRate = JobProposal::query()->where('user_id', $proposal->user_id)
            ->whereNotNull('rate')
            ->groupBy('user_id')
            ->selectRaw('sum(`rate`) / count(*) as user_rate')->first();

        $proposal->user->update(['rate' => $userRate->user_rate]);

        Notification::query()->create([
            'user_id' => $proposal->user_id,
            'message' => 'Your proposal has been rated',
            'job_id' => $proposal->job_id,
        ]);

        return response()->json(['status' => 'success']);
    }
}
