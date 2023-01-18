<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\JobCategory;
use Illuminate\Http\Request;

class JobCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(JobCategory::query()->paginate()); //by defualt = 15
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $normal_validations = [
            'name' => 'required|unique:job_categories,name,',
            'description' => 'nullable'
        ];

        $validator = Validator::make($request->all(), $normal_validations); //كل البراميتر الموجودات بالريكويست (الاسم و الوصف)

        if ($validator->fails()) {
            return response()->json([
                'status' => False,
                'code' => 422,
                'message' => 'error',
                'data' => ['errors'=>$validator->errors()],
            ], 422);        
        }

        $category = JobCategory::query()->create($request->all());

        return response()->json($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = JobCategory::query()->findOrFail($id);

        return response()->json($category);
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
            'name' => 'required|unique:job_categories,name,'.$id,
            'description' => 'nullable'
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

        $category = JobCategory::query()->findOrFail($id);
        $category->update($request->all());

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        JobCategory::query()->findOrFail($id)->delete();

        return response()->json(['status' => 'success']);
    }
}
