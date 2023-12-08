@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <div class="flex flex-col mt-2 py-4  w-5/6">
            <div>
                <h1 class="josefin-sans text-yellow-500 font-semibold text-4xl">Question Reports</h1>
                <div class="bg-slate-100">
                    <table class="table-auto w-full">
                        <thead>
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Created</th>
                            <th class="px-4 py-2">Updated</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td class="border px-4 py-2">{{ $report->id }}</td>
                                <td class="border px-4 py-2">{{ $report->type }}</td>
                                <td class="border px-4 py-2">{{ $report->status }}</td>
                                <td class="border px-4 py-2">{{ $report->created_at }}</td>
                                <td class="border px-4 py-2">{{ $report->updated_at }}</td>
                                <td class="border px-4 py-2">
                                    <a href="" class="text-blue-500 hover:text-blue-800">View</a>
                                    <a href="" class="text-blue-500 hover:text-blue-800">Edit</a>
                                    <form class="inline-block" action="" method="POST" onsubmit="return confirm('Are you sure?');">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                        <button type="submit" class="text-red-500 hover:text-red-800 mb-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection