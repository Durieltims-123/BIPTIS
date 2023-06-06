<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\{APP, ProjectEngineer};
use Validator;
use App\Http\Controllers\ProcurementController;
use PhpOffice\PhpWord\TemplateProcessor;

class ProjectEngineerController extends Controller
{

  public function autoCompleteProjectEngineer  (Request $request)
  {
    $term = $request->term;
    $results = array();
    $projectEngineers = ProjectEngineer::where('name', 'LIKE', '%' . $term . '%')->take(10)->get();

    if (sizeOf($projectEngineers) != 0) {
      foreach ($projectEngineers as $engineer) {
        $results[] = [
          'id' => $engineer->id,
          'value' => $engineer->name
        ];
      }
    } else {
      $results[] = [
        'id' => '',
        'value' => 'No Match Found'
      ];
    }

    return response()->json($results);
  }
}
