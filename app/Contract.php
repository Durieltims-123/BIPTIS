<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
  protected $table = 'contracts';
  protected $primaryKey = 'contract_id';
  protected $fillable = [
    "contract_date_generated",
    "contract_release_date",
    "contract_date_received_contractor",
    "contract_date_of_notarization",
    "contract_receive_date",
    "performance_bond_receive_date",
    "performance_bond_posted",
    "performance_bond_issuance",
    "performance_bond_expiration",
    "performance_bond_duration",
    "performance_bond_remarks",
    "contract_remarks",
    "project_bid_id",
    "contractor_id",
    "posting_status",
    "posting_date",
    "with_attachment"
  ];
}
