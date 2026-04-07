@extends('admin.layout')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Users Management</h5>
                <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#addModal">Add User</button>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-striped" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Email Verified</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i') : 'Not Verified' }}</td>
                                <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $user->updated_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editUser({{ $user->id }})">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }})">Delete</button>
                                </td>
                            </tr>
                            @endforeach
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
function editUser(id) {
    // Edit functionality
    alert('Edit user ' + id);
}

function deleteUser(id) {
    if(confirm('Delete this user?')) {
        // Delete functionality
        alert('Delete user ' + id);
    }
}
</script>
@endsection