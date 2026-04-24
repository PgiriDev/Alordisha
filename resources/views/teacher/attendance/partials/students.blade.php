<form method="POST" action="{{ route('attendance.save') }}">
    @csrf

    <input type="hidden" name="branch_id" value="{{ $r->branch_id }}">
    <input type="hidden" name="subject_id" value="{{ $r->subject_id }}">
    <input type="hidden" name="date" value="{{ $r->date }}">

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Present</th>
                <th>Absent</th>
            </tr>
        </thead>

        <tbody>
        @foreach ($students as $st)
            <tr>
                <td>{{ $st->name }}</td>

                <td>
                    <input type="radio"
                           name="attendance[{{ $st->id }}]"
                           value="present"
                           checked>
                </td>

                <td>
                    <input type="radio"
                           name="attendance[{{ $st->id }}]"
                           value="absent">
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <button class="btn btn-success">Save Attendance</button>
</form>
