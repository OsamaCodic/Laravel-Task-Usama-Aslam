<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserTodo;
use Auth;

class UserTodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = UserTodo::select('user_todos.*');

            if (!empty($request->query('title'))) {
                $query->where("title", 'LIKE', "%" . $request->query('title') . "%") ;
            }
            
            $todos= $query->where('user_id', Auth::user()->id)->orderBy('display_order')->paginate(@$_GET['per_page'] ? $_GET['per_page'] : 10);
            
            return response([
                "todos" => $todos
            ],200);

        } catch (\Exception $e) {
            return response([
                "error"=>$e->getMessage()
            ],500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            
            $validator = $this->todoValidator($request);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'error' => true,
                        'message' => $validator->errors()
                    ],
                );
            }

            $request->merge([
                'user_id' => Auth::user()->id
            ]);
    
            UserTodo::updateOrCreate(['id'=>$request->id],$request->except('_token'));
            return response([
                "success"=>true,
                "message" => @$request->id? 'Task updated successfully.': 'Task created successfully.'
            ],200);

        } catch (\Exception $e) {
            return response([
                "error"=>$e->getMessage()
            ],500);
        }
    }

    public function todoValidator($request)
    {
        $custom_messages = array(
            'title.required' => 'Title field is required.',
            'display_order.required' => 'Display order field is required.',
            'display_order.integer' => 'Display order must be an number.'
        );

        return \Validator::make($request->all(), 
            [
                'title' => 'required',
                'display_order' => 'required|integer',
            ],
            $custom_messages
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $todo = UserTodo::whereId($id)->with('user')->first();
            return response($todo,200);
        } catch (\Exception $e) {
            return response([
                "error"=>$e->getMessage()
            ],500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        UserTodo::findorfail($id)->delete();
        return "User task deleted successfully!";
    }
}