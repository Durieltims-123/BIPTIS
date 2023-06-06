<template>

  <div class="card shadow mt-4 mb-5">
    <div class="card shadow border-0">
      <div class="card-header">
        <h2 id="title">{{title}}</h2>
      </div>
      <div class="card-body">
        <form class="row" name="app_form" type="POST" action="/submit_plan">

          <!-- date added -->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="date_added">Date Added <span class="text-red">*</span></label>
            <input type="date" id="date_added" name="date_added" class="form-control input-danger" v-bind:value="old['date_added']" >
            <label class="error-msg text-red" v-if="errors.date_added">{{errors['date_added'][0]}}</label>
          </div>

          <!-- project year -->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="project_year">Project Year <span class="text-red">*</span></label>
            <date-picker  input-class="form-control bg-white" id="project_year" name="project_year" format="yyyy" minimum-view="year" v-bind:value="old['project_year']" ></date-picker>
            <!-- <input type="" id="project_year" name="project_year" class="form-control input-sm" v-bind:value="old['project_year']" > -->
            <label class="error-msg text-red" v-if="errors.project_year">{{errors['project_year'][0]}}</label>
          </div>


          <!-- Year Funded -->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="year_funded">Year Funded <span class="text-red">*</span></label>
            <date-picker  input-class="form-control bg-white" id="year_funded" name="year_funded" format="yyyy" minimum-view="year" v-bind:value="old['year_funded']" ></date-picker>
            <label class="error-msg text-red" v-if="errors.year_funded">{{errors['year_funded'][0]}}</label>
          </div>

          <!-- Project Number -->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="project_number">Project No.<span class="text-red">*</span></label>
            <input type="" id="project_number" name="project_number" class="form-control" v-bind:value="old['project_number']" >
            <label class="error-msg text-red" v-if="errors.project_number">{{errors['project_number'][0]}}</label>
          </div>

          <!-- Project Title -->
          <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0">
            <label for="project_title">Project Title.<span class="text-red">*</span></label>
            <input type="" id="project_title" name="project_title" class="form-control" v-bind:value="old['project_title']" >
            <label class="error-msg text-red" v-if="errors.project_title">{{errors['project_title'][0]}}</label>
          </div>

          <!-- Sector -->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="sector">Sector Type<span class="text-red">*</span></label>

            <div class="container-fluid">
              <div class="custom-control-inline custom-radio ml-3">
                <input type="radio" id="barangay_sector" v-on:click="fillSector" value="barangay" name="sector_type" class="custom-control-input">
                <label class="custom-control-label" for="barangay_sector">Barangay Development</label>
              </div>
              <div class="custom-control-inline custom-radio ml-3">
                <input type="radio" id="office_sector" name="sector_type" class="custom-control-input">
                <label class="custom-control-label" for="office_sector">Office</label>
              </div>
            </div>
            <label class="error-msg text-red" v-if="errors.sector_type">{{errors['sector_type'][0]}}</label>
          </div>

          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="sector">Sector<span class="text-red">*</span></label>
            <select type="" id="sector" name="sector" class="form-control" v-bind:value="old['sector']" >
            </select>
            <label class="error-msg text-red" v-if="errors.sector">{{errors['sector'][0]}}</label>
          </div>


          <!-- Municipality -->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="municipality">Municipality<span class="text-red">*</span></label>
            <select type="" id="municipality" name="municipality" class="form-control" v-bind:value="old['municipality']" >
            </select>
            <label class="error-msg text-red" v-if="errors.municipality">{{errors['municipality'][0]}}</label>
          </div>

          <!-- Barangay -->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="barangay">Barangay<span class="text-red">*</span></label>
            <select type="" id="barangay" name="barangay" class="form-control" v-bind:value="old['barangay']" >
            </select>
            <label class="error-msg text-red" v-if="errors.barangay">{{errors['barangay'][0]}}</label>
          </div>

          <!-- Type of Project  -->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="type_of_project">Type of Project<span class="text-red">*</span></label>
            <select type="" id="type_of_project" name="type_of_project" class="form-control" v-bind:value="old['type_of_project']" >
            </select>
            <label class="error-msg text-red" v-if="errors.type_of_project">{{errors['type_of_project'][0]}}</label>
          </div>

          <!-- mode of procurement -->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="mode_of_procurement">Mode of Procurement<span class="text-red">*</span></label>
            <select type="" id="mode_of_procurement" name="mode_of_procurement" class="form-control" v-bind:value="old['mode_of_procurement']" >
            </select>
            <label class="error-msg text-red" v-if="errors.mode_of_procurement">{{errors['mode_of_procurement'][0]}}</label>
          </div>

          <!-- Approved Budget Cost -->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="approved_budget_cost">Approved Budget Cost<span class="text-red">*</span></label>
            <input type="" id="approved_budget_cost" name="approved_budget_cost" class="form-control" v-bind:value="old['approved_budget_cost']" >
            <label class="error-msg text-red" v-if="errors.approved_budget_cost">{{errors['approved_budget_cost'][0]}}</label>
          </div>

          <!--Source of Fund-->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="source_of_fund">Source of Fund<span class="text-red">*</span></label>
            <select type="" id="source_of_fund" name="source_of_fund" class="form-control" v-bind:value="old['source_of_fund']" >
            </select>
            <label class="error-msg text-red" v-if="errors.source_of_fund">{{errors['source_of_fund'][0]}}</label>
          </div>


          <!-- Account account_classification -->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="account_classification">Account Classication<span class="text-red">*</span></label>
            <select type="" id="account_classification" name="account_classification" class="form-control" v-bind:value="old['account_classification']" >
            </select>
            <label class="error-msg text-red" v-if="errors.account_classification">{{errors['account_classification'][0]}}</label>
          </div>


          <!--ABC POST Date-->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="ABC_post_date">ABC/Post of IB/REI<span class="text-red">*</span></label>
            <select type="" id="ABC_post_date" name="ABC_post_date" class="form-control" v-bind:value="old['ABC_post_date']" >
            </select>
            <label class="error-msg text-red" v-if="errors.ABC_post_date">{{errors['ABC_post_date'][0]}}</label>
          </div>

          <!--Opening of Bid-->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="opening_of_bid">Opening of Bid<span class="text-red">*</span></label>
            <select type="" id="opening_of_bid" name="opening_of_bid" class="form-control" v-bind:value="old['opening_of_bid']" >
            </select>
            <label class="error-msg text-red" v-if="errors.opening_of_bid">{{errors['opening_of_bid'][0]}}</label>
          </div>

          <!--Notice of Award-->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="notice_of_award">Notice of Award<span class="text-red">*</span></label>
            <select type="" id="notice_of_award" name="notice_of_award" class="form-control" v-bind:value="old['notice_of_award']" >
            </select>
            <label class="error-msg text-red" v-if="errors.notice_of_award">{{errors['notice_of_award'][0]}}</label>
          </div>

          <!--Contract Signing-->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="contract_signing">Contract Signing<span class="text-red">*</span></label>
            <select type="" id="contract_signing" name="notice_of_award" class="form-control" v-bind:value="old['contract_signing']" >
            </select>
            <label class="error-msg text-red" v-if="errors.contract_signing">{{errors['contract_signing'][0]}}</label>
          </div>


          <!--Remarks-->
          <div class="form-group col-xs-12 col-sm-6 col-lg-6 mb-0">
            <label for="remarks">Remarks<span class="text-red">*</span></label>
            <select type="" id="remarks" name="remarks" class="form-control" v-bind:value="old['remarks']" >
            </select>
            <label class="error-msg text-red" v-if="errors.remarks">{{errors['remarks'][0]}}</label>
          </div>


          <div class="form-group col-xs-12 col-sm-12 col-lg-12 mb-0 text-center">
            <button type="submit" class="btn btn-primary mt-5">Submit</button>
          </div>

        </form>
      </div>
    </div>
  </div>

</template>

<script>

export default {
  props:['title','project_type','year','old','data','errors'],
  mounted() {
  },
  methods:{
    fillSector(){

      alert(this.$names.sector_type.value);

    }
  }
}

// events


</script>
