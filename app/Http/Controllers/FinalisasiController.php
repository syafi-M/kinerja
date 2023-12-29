<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Checklist;
use App\Models\Finalisasi;

class FinalisasiController extends Controller
{
    public function exportPDF(Request $request)
    {
        $star_date = $request->start_date;
        $end_date = $request->end_date;
        
        $checklistQuery = Checklist::query();
        $datas = [];
    
        if ($star_date == $end_date) {
            // If start date and end date are the same, use whereDate
            $checklistQuery->whereDate('created_at', $star_date);
            $checklist = $checklistQuery->get();
        } else {
            // If start date and end date are different, use whereBetween
            $checklist = Checklist::whereBetween('created_at', [$star_date, $end_date])->get();
        }
        
        foreach($checklist as $arr)
        {
            $datas = Finalisasi::where('checklist_id', $arr->id)->whereNotNull('approve')->get();
            echo $datas;
        }
        
    }
}
