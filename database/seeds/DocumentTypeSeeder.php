<?php

use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // document types
      DB::table('document_types')->insert([
        [
          'document_number'=>'1',
          'project_type' => 'bidding',
          'document_type' => 'Notice to Proceed(NTP)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'2',
          'project_type' => 'bidding',
          'document_type' => 'Approved Construction Safety and Health Program',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'3',
          'project_type' => 'bidding',
          'document_type' => 'Contract Agreement',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'4',
          'project_type' => 'bidding',
          'document_type' => 'Notice of Award',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'5',
          'project_type' => 'bidding',
          'document_type' => 'Performance Security',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'6',
          'project_type' => 'bidding',
          'document_type' => 'Obligation Request(OBR)/Trust Fund Utilization Slip(TFUS)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'7',
          'project_type' => 'bidding',
          'document_type' => 'Annual Procurement Plan(APP)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'8',
          'project_type' => 'bidding',
          'document_type' => 'BAC Resolution of Award',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'9',
          'project_type' => 'bidding',
          'document_type' => 'Notice of Post Qualification',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'10',
          'project_type' => 'bidding',
          'document_type' => 'Notice of Post Disqualification',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'11',
          'project_type' => 'bidding',
          'document_type' => 'Post Qualification Evaluation Report(PQER)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'12',
          'project_type' => 'bidding',
          'document_type' => 'Bid Evaluation Report(BER)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'13a',
          'project_type' => 'bidding',
          'document_type' => 'Notice of Meeting',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'13b',
          'project_type' => 'bidding',
          'document_type' => 'Minutes of Meeting',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'14',
          'project_type' => 'bidding',
          'document_type' => 'Table of Contents',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'15',
          'project_type' => 'bidding',
          'document_type' => 'PhilGEPS Certification of Registration(Platinum)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'16',
          'project_type' => 'bidding',
          'document_type' => 'Statement of all On going Government and Private Contract',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17',
          'project_type' => 'bidding',
          'document_type' => 'Statement of Single Largest Completed Contract(SLCC)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'18',
          'project_type' => 'bidding',
          'document_type' => 'Net Financial Contracting Capacity(NFCC)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'19',
          'project_type' => 'bidding',
          'document_type' => 'Joint Venture Agreement(JVA)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'20',
          'project_type' => 'bidding',
          'document_type' => 'Special PCAB License',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'21',
          'project_type' => 'bidding',
          'document_type' => 'PCAB License',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'22',
          'project_type' => 'bidding',
          'document_type' => 'Bid Security',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'23',
          'project_type' => 'bidding',
          'document_type' => 'Organizational Chart for contract to be bid',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'24',
          'project_type' => 'bidding',
          'document_type' => 'List of Contractor\'s Personnel',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'25',
          'project_type' => 'bidding',
          'document_type' => 'List of Contractor\'s Major Equipment Units',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'26',
          'project_type' => 'bidding',
          'document_type' => 'Omnibus Sworn Statement',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'27',
          'project_type' => 'bidding',
          'document_type' => 'Audited Financial Statement',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'28',
          'project_type' => 'bidding',
          'document_type' => 'Bill of Quantities',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'29',
          'project_type' => 'bidding',
          'document_type' => 'Bid Form',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'30',
          'project_type' => 'bidding',
          'document_type' => 'Detailed Estimate of the Contract Cost and Summary',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'31',
          'project_type' => 'bidding',
          'document_type' => 'Cash Flow and Payment Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'32',
          'project_type' => 'bidding',
          'document_type' => 'Invitation to Bid',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'33',
          'project_type' => 'bidding',
          'document_type' => 'Bid Data Sheet',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'34',
          'project_type' => 'bidding',
          'document_type' => 'Speacial Condition of Contract',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'35',
          'project_type' => 'bidding',
          'document_type' => 'Lastest Income and Business Tax Returns',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'36',
          'project_type' => 'bidding',
          'document_type' => 'Independent Auditor\'s Report',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'37',
          'project_type' => 'bidding',
          'document_type' => 'Mayor\'s Permit',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'38',
          'project_type' => 'bidding',
          'document_type' => 'Provincial Permit',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'39',
          'project_type' => 'bidding',
          'document_type' => 'Copy of PhilGEPS Bid Notice Abstract',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'40',
          'project_type' => 'bidding',
          'document_type' => 'Construction Schedule and S-Curve',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'41',
          'project_type' => 'bidding',
          'document_type' => 'Manpower Utilization Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'42',
          'project_type' => 'bidding',
          'document_type' => 'Equipment Utilization Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'43',
          'project_type' => 'bidding',
          'document_type' => 'Construction Methods',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'44',
          'project_type' => 'bidding',
          'document_type' => 'Construction Satety and Health Program',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'45',
          'project_type' => 'bidding',
          'document_type' => 'Program of Work',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'46',
          'project_type' => 'bidding',
          'document_type' => 'Detailed Estimates',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'47',
          'project_type' => 'bidding',
          'document_type' => 'Quantity Take-off',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'48',
          'project_type' => 'bidding',
          'document_type' => 'Time Spot Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'49',
          'project_type' => 'bidding',
          'document_type' => 'Work Program Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'50',
          'project_type' => 'bidding',
          'document_type' => 'Approved Plans and Specifications',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'51',
          'project_type' => 'bidding',
          'document_type' => 'Authorization by Sangguinan to enter into contract',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'52a',
          'project_type' => 'bidding',
          'document_type' => 'Copy of Memorandum of Agreement(MOA)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'52b',
          'project_type' => 'bidding',
          'document_type' => 'Implementation Management Agreement(IMA)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'53',
          'project_type' => 'bidding',
          'document_type' => 'Agenda and/or Supplemental Bulletins',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'1',
          'project_type' => 'svp',
          'document_type' => 'Notice to Proceed(NTP)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'2',
          'project_type' => 'svp',
          'document_type' => 'Approved Construction Safety and Health Program',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'3',
          'project_type' => 'svp',
          'document_type' => 'Contract Agreement',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'4',
          'project_type' => 'svp',
          'document_type' => 'Notice of Award',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'5',
          'project_type' => 'svp',
          'document_type' => 'Performance Security',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'6',
          'project_type' => 'svp',
          'document_type' => 'BAC Resolution of Award',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'7',
          'project_type' => 'svp',
          'document_type' => 'Obligation Request(OBR)/Trust Fund Utilization Slip(TFUS)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'8',
          'project_type' => 'svp',
          'document_type' => 'Annual Procurement Plan(APP)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'9',
          'project_type' => 'svp',
          'document_type' => 'Notice of Post Qualification',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'10',
          'project_type' => 'svp',
          'document_type' => 'Notice of Post Disqualification',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'11',
          'project_type' => 'svp',
          'document_type' => 'TWG\'s Technical Evaluation Report',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'12',
          'project_type' => 'svp',
          'document_type' => 'Abstract of Bids',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'13a',
          'project_type' => 'svp',
          'document_type' => 'Notice of Meeting',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'13b',
          'project_type' => 'svp',
          'document_type' => 'Minutes of Meeting',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'14',
          'project_type' => 'svp',
          'document_type' => 'Memorandum.Invitation to pre-qualified to submit proposals',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'15',
          'project_type' => 'svp',
          'document_type' => 'Request for Quotation/Cover Sheet',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'16',
          'project_type' => 'svp',
          'document_type' => 'Detailed Estimates',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17a',
          'project_type' => 'svp',
          'document_type' => 'Business Permit',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17b',
          'project_type' => 'svp',
          'document_type' => 'PhilGEPS Registration',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17c',
          'project_type' => 'svp',
          'document_type' => 'PCAB Lincense',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17d',
          'project_type' => 'svp',
          'document_type' => 'Income or Business Tax Clearance',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17e',
          'project_type' => 'svp',
          'document_type' => 'Omnibus Sworn Statement',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17f',
          'project_type' => 'svp',
          'document_type' => 'Provincial Permit',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'18',
          'project_type' => 'svp',
          'document_type' => 'Latest Income and Business Tax Returns',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'19',
          'project_type' => 'svp',
          'document_type' => 'Bid Notice Abtract',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'20',
          'project_type' => 'svp',
          'document_type' => 'Construction Schedule and S-Curve',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'21',
          'project_type' => 'svp',
          'document_type' => 'Cash Flow and Payment Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'22',
          'project_type' => 'svp',
          'document_type' => 'List of Key Personnel',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'23',
          'project_type' => 'svp',
          'document_type' => 'Manpower Utilization Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'24',
          'project_type' => 'svp',
          'document_type' => 'Equipment Utilization Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'25',
          'project_type' => 'svp',
          'document_type' => 'Construction Methods',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'26',
          'project_type' => 'svp',
          'document_type' => 'Construction Safety and Health Program Proposal',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'27',
          'project_type' => 'svp',
          'document_type' => 'Program of Work',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'28',
          'project_type' => 'svp',
          'document_type' => 'Detailed Estimates',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'29',
          'project_type' => 'svp',
          'document_type' => 'Time Spot Schedules',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'30',
          'project_type' => 'svp',
          'document_type' => 'Quality Control Program',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'31',
          'project_type' => 'svp',
          'document_type' => 'Quantity Take-off',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'32',
          'project_type' => 'svp',
          'document_type' => 'Work Program Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'33',
          'project_type' => 'svp',
          'document_type' => 'Approved Plans and Specification',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'34',
          'project_type' => 'svp',
          'document_type' => 'Authorization by Sangguinan to enter into contract',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'35a',
          'project_type' => 'svp',
          'document_type' => 'Memorandum of Agreement(MOA)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'35b',
          'project_type' => 'svp',
          'document_type' => 'Implementation Management Agreement(IMA)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'35b',
          'project_type' => 'svp',
          'document_type' => 'Agenda and/or Supplemental Bulletins',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'1',
          'project_type' => 'negotiated',
          'document_type' => 'Notice to Proceed(NTP)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'2',
          'project_type' => 'negotiated',
          'document_type' => 'Approved Construction Safety and Health Program',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'3',
          'project_type' => 'negotiated',
          'document_type' => 'Contract Agreement',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'4',
          'project_type' => 'negotiated',
          'document_type' => 'Notice of Award',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'5',
          'project_type' => 'negotiated',
          'document_type' => 'Performance Security',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'6',
          'project_type' => 'negotiated',
          'document_type' => 'BAC Resolution of Award',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'7',
          'project_type' => 'negotiated',
          'document_type' => 'Oblication Request(OBR)/Trust Fund Utilization Slip(TFUS)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'8',
          'project_type' => 'negotiated',
          'document_type' => 'Annual Procurement Plan(APP)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'9',
          'project_type' => 'negotiated',
          'document_type' => 'Notice of Post Qualification',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'10',
          'project_type' => 'negotiated',
          'document_type' => 'Notice of Post Disqualification',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'11',
          'project_type' => 'negotiated',
          'document_type' => 'TWG\'s Technical Evaluation Report',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'12',
          'project_type' => 'negotiated',
          'document_type' => 'Abstract of Bids',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'13a',
          'project_type' => 'negotiated',
          'document_type' => 'Notice of Meeting',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'13b',
          'project_type' => 'negotiated',
          'document_type' => 'Minutes of Meeting',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'14',
          'project_type' => 'svp',
          'document_type' => 'Memorandum.Invitation to pre-qualified to submit proposals',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'15',
          'project_type' => 'negotiated',
          'document_type' => 'Request for Quotation/Cover Sheet',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'16',
          'project_type' => 'negotiated',
          'document_type' => 'Detailed Estimates',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17a',
          'project_type' => 'negotiated',
          'document_type' => 'Business Permit',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17b',
          'project_type' => 'negotiated',
          'document_type' => 'PhilGEPS Registration',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17c',
          'project_type' => 'negotiated',
          'document_type' => 'PCAB Lincense',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17d',
          'project_type' => 'negotiated',
          'document_type' => 'Income or Business Tax Clearance',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17e',
          'project_type' => 'negotiated',
          'document_type' => 'Omnibus Sworn Statement',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'17f',
          'project_type' => 'negotiated',
          'document_type' => 'Provincial Permit',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'18',
          'project_type' => 'negotiated',
          'document_type' => 'Latest Income and Business Tax Returns',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'19',
          'project_type' => 'negotiated',
          'document_type' => 'Bid Notice Abtract',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'20',
          'project_type' => 'negotiated',
          'document_type' => 'Construction Schedule and S-Curve',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'21',
          'project_type' => 'negotiated',
          'document_type' => 'Cash Flow and Payment Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'22',
          'project_type' => 'negotiated',
          'document_type' => 'List of Key Personnel',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'23',
          'project_type' => 'negotiated',
          'document_type' => 'Manpower Utilization Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'24',
          'project_type' => 'negotiated',
          'document_type' => 'Equipment Utilization Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'25',
          'project_type' => 'negotiated',
          'document_type' => 'Construction Methods',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'26',
          'project_type' => 'negotiated',
          'document_type' => 'Construction Safety and Health Program Proposal',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'27',
          'project_type' => 'negotiated',
          'document_type' => 'Program of Work',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'28',
          'project_type' => 'negotiated',
          'document_type' => 'Detailed Estimates',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'29',
          'project_type' => 'negotiated',
          'document_type' => 'Time Spot Schedules',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'30',
          'project_type' => 'negotiated',
          'document_type' => 'Quality Control Program',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'31',
          'project_type' => 'negotiated',
          'document_type' => 'Quantity Take-off',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'32',
          'project_type' => 'negotiated',
          'document_type' => 'Work Program Schedule',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'33',
          'project_type' => 'negotiated',
          'document_type' => 'Approved Plans and Specification',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'34',
          'project_type' => 'negotiated',
          'document_type' => 'Authorization by Sangguinan to enter into contract',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'35a',
          'project_type' => 'negotiated',
          'document_type' => 'Memorandum of Agreement(MOA)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'35b',
          'project_type' => 'negotiated',
          'document_type' => 'Implementation Management Agreement(IMA)',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'document_number'=>'35b',
          'project_type' => 'negotiated',
          'document_type' => 'Agenda and/or Supplemental Bulletins',
          'document_status' => 'active',
          'created_at' => now(),
          'updated_at' => now()
        ]
      ]);
    }
}
