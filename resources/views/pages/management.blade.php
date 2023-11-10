@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <div class="flex flex-col mt-2 px-12  w-2/3">
            <div>
                <h1 class="fira-sans font-semibold text-2xl">Management</h1>
                <hr>
                <div class="bg-slate-100">
                    <div class="flex flex-col bg-slate-200 shadow">
                        <div>
                            <form action="/trv/management/trivia" method="POST" class="flex flex-col px-2 py-4 ">
                                {{ csrf_field() }}
                                <div class="flex flex-row">
                                    <div class="flex flex-col px-2 py-2">
                                        <label class="raleway font-semibold text-md" for="title">Title:</label>
                                        <input class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" type="text" name="title" id="title">
                                    </div>
                                    <div class="flex flex-col px-2 py-2">
                                        <label class="raleway font-semibold text-md" for="description">Description</label>
                                        <input class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" type="text" name="description" id="description">
                                    </div>
                                    <div class="flex flex-col px-2 py-2">
                                        <label class="raleway font-semibold text-md" for="difficulty">Difficulty</label>
                                        <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" name="difficulty" id="difficulty">
                                            <option value="easy">Easy</option>
                                            <option value="medium">Medium</option>
                                            <option value="hard">Hard</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col px-2 py-2">
                                        <label class="raleway font-semibold text-md" for="category">Category</label>
                                        <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" name="category" id="category">
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="flex flex-row px-2">
                                    <button type="submit" class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold">Create New Trivia</button>
                                </div>
                            </form>
                        </div>
                        <div>
                            <form action="/trv/csv-upload/trivia" method="POST" enctype="multipart/form-data" class="flex flex-col px-2 py-4 shadow">
                                {{ csrf_field() }}
                                <div class="flex flex-row">
                                    <div class="flex flex-col px-2 py-2">
                                        <label class="raleway font-semibold text-md" for="title">CSV File:</label>
                                        <input class="bg-slate-100 border border-zinc-400 rounded py-1"  type="file" name="trivia-csv" id="trivia-csv">
                                    </div>
                                </div>
                                <div class="flex flex-row px-2">
                                    <button type="submit" class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold">Create Trivia From CSV</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="flex flex-col px-2 mt-4">
                        <h2 class="raleway">Trivia's</h2>
                        <table class="my-4">
                            <tr class="border-b-slate-300 border-b">
                                <th class="px-2 py-2">Title</th>
                                <th class="px-2 py-2">Description</th>
                                <th class="px-2 py-2">Difficulty</th>
                                <th class="px-2 py-2">Category</th>
                                <th class="px-2 py-2">Active</th>
                                <th class="px-2 py-2">Private</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                            @foreach($trivias as $trv)
                                <tr>
                                    <td class="text-center">{{ $trv['title'] }}</td>
                                    <td class="text-center">{{ $trv['description'] }}</td>
                                    <td class="text-center">{{ Str::limit($trv['difficulty'], 50, '...') }}</td>
                                    <td class="text-center">{{ $trv->category['name'] }}</td>
                                    <td class="text-center">{{ $trv['is_active'] }}</td>
                                    <td class="text-center">{{ $trv['private'] }}</td>
                                    <td class="flex flex-row justify-around">
                                        <a class="inline-block py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" href="/trv/management/trivia/{{ $trv['id'] }}">Edit</a>
                                        <button class="py-2 px-4 shadow-md bg-red-500 text-slate-100 font-semibold">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection