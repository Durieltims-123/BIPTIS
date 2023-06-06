<div class="header bg-gradient-primary pb-8 pt-5 pt-md-3">
  <div class="container-fluid">
    <div class="header-body">
      <!-- Card stats -->
      <div class="row col-sm-10">
        <div class="col-xl-3 col-lg-6">
          <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row">
                <div class="col pr-0">
                  <a href="{{route('ongoing_projects')}}" target="_blank">
                    <h5 class="card-title text-uppercase text-muted mb-0">Ongoing Projects</h5>
                    <span class="h2 font-weight-bold mb-0">{{$ongoing}}</span>
                  </a>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-danger text-white rounded-circle shadow">
                    <i class="fas fa-chart-bar"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-6">
          <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row">
                <div class="col pr-0">
                  <a href="{{route('completed_projects')}}" target="_blank">
                    <h5 class="card-title text-uppercase text-muted mb-0">Completed (@php echo date('Y'); @endphp)</h5>
                    <span class="h2 font-weight-bold mb-0">{{$completed}}</span>
                  </a>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                    <i class="fas fa-chart-pie"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-6">
          <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row">
                <div class="col pr-0">
                  <a href="{{route('projects_for_rebid')}}" target="_blank">
                    <h5 class="card-title text-uppercase text-muted mb-0">Projects for Rebid</h5>
                    <span class="h2 font-weight-bold mb-0">{{$for_rebid}}</span>
                  </a>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-primary text-white rounded-circle shadow">
                    <i class="ni ni-calendar-grid-58"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-6">
          <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row">
                <div class="col pr-0">
                  <a href="{{route('projects_for_review')}}" target="_blank">
                    <h5 class="card-title text-uppercase text-muted mb-0">Projects for Review</h5>
                    <span class="h2 font-weight-bold mb-0">{{$for_review}}</span>
                  </a>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-yellow text-white rounded-circle shadow">
                    <i class="fas fa-users"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-6 mt-2">
          <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row">
                <div class="col pr-0">
                  <a href="{{route('unprocured_projects')}}" target="_blank">
                    <h5 class="card-title text-uppercase text-muted mb-0">Unprocured Projects</h5>
                    <span class="h2 font-weight-bold mb-0">{{$unprocured_projects}}</span>
                  </a>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                    <i class="fas fa-percent"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-6 mt-2">
          <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row">
                <div class="col pr-0">
                  <a href="{{route('reverted_projects')}}" target="_blank">
                    <h5 class="card-title text-uppercase text-muted mb-0">Reverted Projects({{date('Y')}})</h5>
                    <span class="h2 font-weight-bold mb-0">{{$reverted_projects}}</span>
                  </a>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-danger text-white rounded-circle shadow">
                    <i class="ni ni-bold-left"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-lg-6 mt-2">
          <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row">
                <div class="col pr-0">
                  <a href="{{route('terminated_projects')}}" target="_blank">
                    <h5 class="card-title text-uppercase text-muted mb-0">Terminated Contracts</h5>
                    <span class="h2 font-weight-bold mb-0">{{$terminated_projects}}</span>
                  </a>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-purple text-white rounded-circle shadow">
                    <i class="ni ni-active-40"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-lg-6 mt-2">
          <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row">
                <div class="col pr-0">
                  <a href="{{route('insufficient_performance_bond')}}" target="_blank">
                    <h5 class="card-title text-uppercase text-muted mb-0">Insufficient Performancebond</h5>
                    <span class="h2 font-weight-bold mb-0">{{$insufficient_performance_bond}}</span>
                  </a>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                    <i class="ni ni-bullet-list-67"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-6 mt-2">
          <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row">
                <div class="col pr-0">
                  <a href="{{route('get_with_pow')}}" target="_blank">
                    <h5 class="card-title text-uppercase text-muted mb-0">POW Received(@php echo date('Y'); @endphp)</h5>
                    <span class="h2 font-weight-bold mb-0">{{$pow_this_year}}</span>
                  </a>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                    <i class="ni ni-bullet-list-67"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-6 mt-2">
          <div class="card card-stats mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row">
                <div class="col pr-0">
                  <a href="/post_qualification_to_verify" target="_blank">
                    <h5 class="card-title text-uppercase text-muted mb-0">Post Qual (Unverified)</h5>
                    <span class="h2 font-weight-bold mb-0">{{$post_qual_to_verify}}</span>
                  </a>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-danger text-white rounded-circle shadow">
                    <i class="fa fa-check-circle"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
         <div class="col-xl-3 col-lg-6 mt-2">
             <div class="card card-stats mb-4 mb-xl-0">
                 <div class="card-body">
                     <div class="row">
                         <div class="col pr-0">
                             <a href="/pending_resolution_declaring_failure" target="_blank">


                                 <h5 class="card-title text-uppercase text-muted mb-0">Projects Pending for RDF</h5>
                                 <span class="h2 font-weight-bold mb-0">{{$pending_rdf}}</span>

                             </a>
                         </div>
                         <div class="col-auto">
                             <div class="icon icon-shape bg-gradient-danger text-white rounded-circle shadow">
                                 <i class="fa fa-check-circle"></i>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>

      </div>
    </div>
  </div>
</div>
