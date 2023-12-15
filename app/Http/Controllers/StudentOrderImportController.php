<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelImportData;


class StudentOrderImportController extends Controller
{
    public function fileImport(Request $request){
        $file = $request->file;

        Excel::import(new ExcelImportData, $file);

    }
}
