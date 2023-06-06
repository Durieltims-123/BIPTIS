<?php

use Illuminate\Database\Seeder;

class ProcurementProcessesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('procurement_processes')->insert([
            [
              "process_name" => "Advertisement",
              "mode_of_procurement" => "negotiated",
              "status" => 'active'
            ],[
              "process_name" => "Submission of Bid",
              "mode_of_procurement" => "negotiated",
              "status" => 'active'
            ],[
              "process_name" => "Bid Evaluation",
              "mode_of_procurement" => "negotiated",
              "status" => 'active'
            ],[
              "process_name" => "Post Qualification",
              "mode_of_procurement" => "negotiated",
              "status" => 'active'
            ],[
              "process_name" => "Issuance of Notice of Awards",
              "mode_of_procurement" => "negotiated",
              "status" => 'active'
            ],[
              "process_name" => "Contract Preparation and Signing",
              "mode_of_procurement" => "negotiated",
              "status" => 'active'
            ],[
              "process_name" => "Approval by Higher Authority",
              "mode_of_procurement" => "negotiated",
              "status" => 'active'
            ],[
              "process_name" => "Notice to Proceed",
              "mode_of_procurement" => "negotiated",
              "status" => 'active'
            ]
          ]);
    }
}
