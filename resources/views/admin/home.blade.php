@extends('admin.layout')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Home Management</h5>
                <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#addModal">Add Home</button>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Subtitle</th>
                                <th>Description</th>
                                <th>Button Text</th>
                                <th>Hero Image</th>
                                <th>About Title</th>
                                <th>About Description</th>
                                <th>News Title</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($home as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ Str::limit($item->title ?? 'N/A', 30) }}</td>
                                <td>{{ Str::limit($item->subtitle ?? 'N/A', 30) }}</td>
                                <td>{{ Str::limit($item->description ?? 'N/A', 40) }}</td>
                                <td>{{ $item->button_text ?? 'N/A' }}</td>
                                <td>{{ $item->hero_image ?? 'N/A' }}</td>
                                <td>{{ Str::limit($item->about_title ?? 'N/A', 30) }}</td>
                                <td>{{ Str::limit($item->about_description ?? 'N/A', 40) }}</td>
                                <td>{{ $item->news_title ?? 'N/A' }}</td>
                                <td>{{ $item->status ?? 'N/A' }}</td>
                                <td>{{ $item->created_at ? $item->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>{{ $item->updated_at ? $item->updated_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editItem({{ $item->id }})">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteItem({{ $item->id }})">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="13" class="text-center">No data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editItem(id) {
    alert('Edit home ' + id);
}

function deleteItem(id) {
    if(confirm('Delete this item?')) {
        alert('Delete home ' + id);
    }
}
</script>
@endsection