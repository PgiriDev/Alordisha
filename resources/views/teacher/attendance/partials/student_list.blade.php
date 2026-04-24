<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Status</th>
            <th>Mark</th>
        </tr>
    </thead>

    <tbody>
        @forelse($students as $s)
            <tr>
                <td>
                    <img src="{{ asset('storage/' . $s->photo_path) }}" width="50" height="50" style="border-radius:5px;">
                </td>
                <td>{{ $s->name }}</td>
                <td>
                    <span class="badge bg-info">Not Marked</span>
                </td>
                <td>
                    <input type="checkbox"
                           name="attendance[{{ $s->id }}]"
                           value="present">
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-danger">
                    No students found for this branch & subject.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
