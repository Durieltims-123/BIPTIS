<!-- Sidenav -->
<nav class="sidenav navbar navbar-vertical  fixed-left  navbar-expand-xs navbar-light bg-white" id="sidenav-main">
  <div class="scrollbar-inner">
    <!-- Brand -->
    <div class="sidenav-header  align-items-center">
      <a class="navbar-brand" href="javascript:void(0)">
        BIPTIS
      </a>
    </div>
    <div class="navbar-inner">
      <!-- Collapse -->
      <div class="collapse navbar-collapse" id="sidenav-collapse-main">
        <!-- Nav items -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}">
              <i class="ni ni-tv-2 text-primary"></i> {{ __('Dashboard') }}
            </a>
          </li>

          <!--  APP -->
          <li class="nav-item">
            <a class="nav-link " href="#app_project" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="app_project">
              <i class="ni ni-app text-primary"></i>
              <span class="nav-link-text">{{ __('APP') }}</span>
            </a>

            <div class="collapse" id="app_project">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('limited_regular_app')}}">
                    {{ __('Regular APP') }}
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{route('limited_supplemental_app')}}">
                    {{ __('Supplemental APP') }}
                  </a>
                </li>
              </ul>
            </div>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="/twg_projects_with_bidders">
              <i class="ni ni-chart-bar-32 text-blue"></i> {{ __('Project (Bidders and Price Quotations)') }}
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{route('project_bidders_additional_documents')}}">
              <i class="ni ni-check-bold text-primary"></i> {{ __('Requirements Checklist') }}
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{route('request_for_extension')}}">
              <i class="ni ni-chat-round text-primary"></i> {{ __('Request for Extension') }}
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link " href="#procurement_activity" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="procurement_activity">
              <i class="ni ni-send" style="color: #f4645f;"></i>
              <span class="nav-link-text">{{ __('Procurement Activity') }}</span>
            </a>


            <div class="collapse" id="procurement_activity">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a class="nav-link"  href="{{route('twg_post_qualification')}}">
                    {{ __('Post Qualification') }}
                  </a>
                </li>

              </ul>
            </div>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{ route('generate_bid_evaluation') }}">
              <i class="ni ni-chart-pie-35 text-primary"></i> {{ __('Bid Evaluation Report') }}
            </a>
          </li>



          <!-- Document Tracking -->
          <li class="nav-item">
            <a class="nav-link " href="#documenttracking" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="twg">
              <i class="ni ni-folder-17" style="color: #1708eb;"></i>
              <span class="nav-link-text">{{ __('Document Tracking') }}</span>
            </a>

            <div class="collapse" id="documenttracking">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('documenttracking.index')}}">
                    {{ __('Documents') }}
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('documenttracking.checklist') }}">
                    {{ __('Project Document Check List') }}
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('documenttracking.create') }}">
                    {{ __('Add Documents to be Tracked') }}
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('documenttracking.trackingsettings') }}">
                    {{ __('Tracking Settings') }}
                  </a>
                </li>
              </ul>
            </div>
          </li>

        </ul>
      </div>
    </div>
  </div>
</nav>
