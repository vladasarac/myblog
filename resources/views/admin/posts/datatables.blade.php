@extends('layouts.dashboard')

@section('content')
  <h2 class="sub-header">Simple DataTables in laravel 5.2</h2>
  <div class="row">
    <div class="col-md-9">
      <a href="{{ url('admin/posts/new-post')}}" class="btn btn-primary btn-sm">Add New Post</a><br><br>
    </div>
    <br>
  </div>
  <div class="table-responsive">
    <table class="table table-striped" id="allposts">
      <thead>
      <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Description</th>
        <th>Created</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
    </table>
  </div>
@endsection


@push('scripts')
<script type="text/javascript">
  $(function(){
    $('#allposts').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{!! URL::asset('admin/posts/postsdata') !!}',
      columns : [
        { data: 'id', name: 'id' },
        { data: 'title', name: 'title' },
        { data: 'description', name: 'description' },
        { data: 'updated_at', name: 'updated_at' }
      ]
    });
  });
</script>
@endpush

