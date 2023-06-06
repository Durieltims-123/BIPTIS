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
          @foreach($links as $link)
          @if($link->parent_name===null||$link->parent_name==="")
          <li class="nav-item">
            <a class="nav-link" href="/{{$link->link_route }}">
              <i class="{{$link->link_icon}} text-primary"></i> {{$link->link_name}}
            </a>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link " href="#{{$link->parent_id}}Link" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="reports">
              <i class="{{$link->link_icon}} text-primary"></i>
              <span class="nav-link-text">{{$link->parent_name}}</span>
            </a>

            <div class="collapse" id="{{$link->parent_id}}Link">
              <ul class="nav nav-sm flex-column">
                @foreach($link->sublinks as $sublink)
                <li class="nav-item">
                  <a class="nav-link"  href="/{{$sublink->link_route }}">
                    {{$sublink->link_name}}
                  </a>
                </li>
                @endforeach
              </ul>
            </div>
          </li>
          @endif
          @endforeach

        </ul>
      </div>
    </div>
  </div>
</nav>
