<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use App\Models\Unit;
use App\Models\UnitUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ImportController extends Controller
{
    public function index()
    {
        return view('import');
    }

    public function import(Request $request)
    {
        $file = fopen($request->file('csv'), 'r');
        while($column = fgetcsv($file))
        {

            if ($column[0] == 'nome') continue;
            else if ($column[0] == '') break;

            $student_name = $column[0];
            $student_cpf = $column[1];
            $student_email = $column[2];
            $course_workload = $column[3];
            $certification_start = $column[4];
            $certification_end = $column[5];
            $certification_emission = $column[6];
            $unit_name = $column[7];
            $course_name = $column[8];
            $user_name = $column[11];

            $unit = Unit::firstOrCreate(['name' => $unit_name], [
                'name' => $unit_name,
            ]);

            $student = Student::firstOrCreate(['cpf' => $student_cpf], [
                'name' => $student_name,
                'cpf' => $student_cpf,
                'email' => $student_email,
                'unit_id' => $unit->id,
            ]);

            $user_id = (User::orderBy('id', 'DESC')->first()?->id ?? 0) + 1;
            $user_password = str_pad($user_id, 4, "0", STR_PAD_LEFT);
            $user_email = str_replace('*', $user_password, env('USER_EMAIL_PATTERN'));

            $user = User::firstOrCreate(['name' => $user_name], [
                'name' => $user_name,
                'email' => $user_email,
                'password' => Hash::make($user_password),
            ]);

            $unit_user_object = [
                'unit_id' => $unit->id,
                'user_id' => $user->id,
            ];

            UnitUser::firstOrCreate($unit_user_object, $unit_user_object);

            $course = Course::firstOrCreate(['name' => $course_name], [
                'name' => $course_name,
                'workload' => intval($course_workload),
            ]);

            $certification_object = [
                'student_id' => $student->id,
                'unit_id' => $unit->id,
                'course_id' => $course->id,
                'start' => $certification_start,
                'end' => $certification_end,
                'emission' => $certification_emission,
            ];

            $certification = Certification::firstOrCreate($certification_object, $certification_object);
            echo "Certification {$certification->id} created successfully <br>";
        }
    }
}
