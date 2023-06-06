@extends('layouts.app')

@section('content')
@include('layouts.headers.cards2')
<div class="container-fluid mt-1">
  <div id="app">
    <div class="card shadow">
      <div class="card shadow border-0">
        <div class="card-header">
          <h2 id="title"></h2>
        </div>
        <div class="card-body">
          <!-- The toolbar will be rendered in this container. -->
          <div id="toolbar-container"></div>

          <!-- This container will become the editable. -->
          <div id="editor">
            <p>This is the initial editor content.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



@endsection

@push('custom-scripts')
<script>
DecoupledEditor
.create( document.querySelector( '#editor' ) )
.then( editor => {
  const toolbarContainer = document.querySelector( '#toolbar-container' );

  toolbarContainer.appendChild( editor.ui.view.toolbar.element );
} )
.catch( error => {
  console.error( error );
} );
</script>
@endpush
