<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;
use DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $employees = Employee::select(
            'employees.id', 
            'employees.name', 
            'email',
            'phone', 
            'department_id',
            'departments.name as department')
        ->join('departments', 'departments.id', '=', 'employees.department_id')
        ->paginate(10);
        $departments = Department::all();
        return Inertia::render(
            'Employees/Index', 
            ['employees' => $employees, 
            'departments'=> $departments]
        );
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
        //
        $request->validate([
            'name' => 'required|max:150',
            'email' => 'required|max:80',
            'phone' => 'required|max:15',
            'department_id' => 'required|numeric',
        ]);
        $employee = new Employee($request->input());
        $employee->save();
        return redirect('employees');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        //
        $request->validate([
            'name' => 'required|max:150',
            'email' => 'required|max:80',
            'phone' => 'required|max:15',
            'department_id' => 'required|numeric',
        ]);
        $employee->update($request->input());
        return redirect('employees');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        //
        $employee->delete();
        return redirect('employees');
    }

    public function EmployeeByDepartment(){
        $data = Employee::select(DB::raw('count(employees.id) as count, departments.name'))
        -join('departments', 'departments.id', '=', 'employees.department_id')
        -goupBy('departments.name')-get();
        return Inertia::render('Employees/Graphic',['data'=> $data]);

    }

    public function reports(){
        $employees = Employee::select(
            'employees.id', 
            'employees.name', 
            'email', 
            'phone', 
            'department_id',
            'departments.name as department')
        ->join('departments', 
            'departments.id', '=', 'employees.department_id')
        ->get();

        $departments = Department::all();
        return Inertia::render(
            'Employees/Reports', 
            ['employees' => $employees, 
            'departments'=> $departements]
        );

    }

}
