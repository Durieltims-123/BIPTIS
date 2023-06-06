<template>
  <div class="card shadow">
    <div class="card shadow border-0">
      <div class="card-header">
        <h2 id="title">{{title}}</h2>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <a v-if=" project_type=='supplemental'" class="btn btn-danger text-white float-right mb-2" href="/add_supplemental_app"><i class="ni ni-fat-add">Supplemental APP</i> </a>
          <a v-if=" project_type=='regular'" class="btn btn-danger text-white float-right mb-2" href="/add_regular_app"><i class="ni ni-fat-add">Regular APP</i> </a>
        </div>
        <div class="table-responsive">
          <table class="table" id="app_table">
            <thead class="bg-primary text-white">
              <tr>
                <th class="text-center"></th>
                <th class="text-center">ID</th>
                <th class="text-center">Project No.</th>
                <th class="text-center">Project Title</th>
                <th class="text-center">Location</th>
                <th class="text-center">Mode of Procurement</th>
                <th class="text-center">Posting</th>
                <th class="text-center">Sub/Open of Bids</th>
                <th class="text-center">Notice of Award</th>
                <th class="text-center">Contract Signing</th>
                <th class="text-center">Source of Fund</th>
                <th class="text-center">Approved Budget Cost</th>
                <th class="text-center">Project Year</th>
                <th class="text-center">Status</th>

              </tr>
            </thead>
            <tbody>

              <tr v-for="project_plan in project_plans">
                <td v-if=" project_plan.project_status!='pending'"><button class="btn-sm shadow-0 border-0 btn-primary">View</button></td>
                <td v-else><button class="btn-sm shadow-0 border-0 btn-success">Edit</button><button class="btn-sm shadow-0 border-0 btn-danger">delete</button></td>
                <td>{{ project_plan.plan_id }}</td>
                <td>{{ project_plan.project_no }}</td>
                <td>{{ project_plan.project_title }}</td>
                <td>{{ project_plan.barangay }},{{ project_plan.municipality }}</td>
                <td>{{ project_plan.mode }}</td>
                <td>{{ project_plan.abc_post_date }}</td>
                <td>{{ project_plan.sub_open_date }}</td>
                <td>{{ project_plan.award_notice_date }}</td>
                <td>{{ project_plan.contract_signing_date }}</td>
                <td>{{ project_plan.source }}</td>
                <td>{{ project_plan.abc }}</td>
                <td>{{ project_plan.project_year }}</td>
                <td>{{ project_plan.project_status }}</td>
              </tr>

              <tr class="text-center" v-if="project_plans.length == 0">
                <td colspan="10">
                  No Data
                </td>
              </tr>
            </tbody>

          </table>
        </div>
      </div>
    </div>
  </div>

</template>

<script>


export default {
  props:['project_plans','title','project_type','year'],
  mounted() {
    // console.log(this.project_plans);
  }
}




</script>
