@extends('game-api::layout')
@section('body')


    <script type="application/javascript">

        function openTriviaCreatorFromApiModal(){

            let title = 'Create New Trivia From API';

            //Create the form
            let form = document.createElement('form');
            form.setAttribute('method', 'POST');
            form.setAttribute('action', '/trv/management/api-trivia');
            form.setAttribute('id', 'trivia-from-api-form');
            form.setAttribute('class', 'flex flex-row flex-wrap justify-center w-full');

            let triviaInfoDiv = document.createElement('div');
            triviaInfoDiv.setAttribute('class', 'flex flex-col w-full md:w-4/6 px-2');

            //need a field for trivia title
            let titleField = document.createElement('input');
            titleField.setAttribute('type', 'text');
            titleField.setAttribute('name', 'title');
            titleField.setAttribute('id', 'title');
            titleField.setAttribute('placeholder', 'Title');
            titleField.setAttribute('class', 'border border-slate-400 shadow shadow-slate-400 rounded py-1 px-2 mb-2');

            //need a field for trivia description
            let descriptionField = document.createElement('input');
            descriptionField.setAttribute('type', 'text');
            descriptionField.setAttribute('name', 'description');
            descriptionField.setAttribute('id', 'description');
            descriptionField.setAttribute('placeholder', 'Description');
            descriptionField.setAttribute('class', 'border border-slate-400 shadow shadow-slate-400 rounded py-1 px-2 mb-2');

            let categoryField = document.createElement('select');
            categoryField.setAttribute('name', 'category');
            categoryField.setAttribute('id', 'category');
            categoryField.setAttribute('class', 'border border-slate-400 shadow shadow-slate-400 rounded py-1 px-2 mb-2');

            //need category options
            @foreach($categories as $cat)
            let categoryOption{{$cat['id']}} = document.createElement('option');
            categoryOption{{$cat['id']}}.setAttribute('value', "{{ $cat['id'] }}");
            categoryOption{{$cat['id']}}.innerHTML = "{{ $cat['name'] }}";
            categoryField.appendChild(categoryOption{{$cat['id']}});
            @endforeach

            //need a field for trivia difficulty
            let difficultyField = document.createElement('select');
            difficultyField.setAttribute('name', 'difficulty');
            difficultyField.setAttribute('id', 'difficulty');
            difficultyField.setAttribute('class', 'border border-slate-400 shadow shadow-slate-400 rounded py-1 px-2 mb-2');

            //need difficulty options
            let easyOption = document.createElement('option');
            easyOption.setAttribute('value', 'easy');
            easyOption.innerHTML = 'Easy';
            difficultyField.appendChild(easyOption);

            let mediumOption = document.createElement('option');
            mediumOption.setAttribute('value', 'medium');
            mediumOption.innerHTML = 'Medium';
            difficultyField.appendChild(mediumOption);

            let hardOption = document.createElement('option');
            hardOption.setAttribute('value', 'hard');
            hardOption.innerHTML = 'Hard';
            difficultyField.appendChild(hardOption);


            //need a submit button
            let submitButton = document.createElement('button');
            submitButton.setAttribute('type', 'submit');
            submitButton.setAttribute('class', 'py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold');
            submitButton.innerHTML = 'Create Trivia From API';


            //append all the fields to the form

            triviaInfoDiv.appendChild(titleField);
            triviaInfoDiv.appendChild(descriptionField);
            triviaInfoDiv.appendChild(categoryField);
            triviaInfoDiv.appendChild(difficultyField);
            triviaInfoDiv.appendChild(submitButton);

            let csrfInput = document.createElement('input');
            csrfInput.setAttribute('type', 'hidden');
            csrfInput.setAttribute('name', '_token');
            csrfInput.setAttribute('id', '_token');
            csrfInput.setAttribute('value', '{{ csrf_token() }}');


            form.appendChild(triviaInfoDiv);
            form.appendChild(csrfInput);
            //form.appendChild(descriptionField);
            //form.appendChild(categoryField);
            //form.appendChild(difficultyField);
            //form.appendChild(submitButton);

            GameApi.openModal('game-modal', title, form);
        }


    </script>


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
                                <div class="flex flex-row flex-wrap">
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
                        <div class="px-4 py-4">
                            <button onclick="openTriviaCreatorFromApiModal()" class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold">Create Trivia From API</button>
                        </div>
                        <div class="px-4 py-4 shadow">
                            <form action="/trv/api-single-upload/trivia" method="POST" enctype="multipart/form-data" class="flex flex-col px-2 py-4 ">
                                {{ csrf_field() }}
                                <div class="flex flex-row">
                                    <div class="flex flex-col px-2 py-2">
                                        <div class="">
                                            <label class="raleway font-semibold text-md" for="category">Category:</label>
                                            <select name="category" class="bg-slate-100 border border-zinc-400 rounded py-1">
                                                <option value="arts_and_literature">Art and Literature</option>
                                                <option value="entertainment">Entertainment</option>
                                                <option value="food_and_drink">Food And Drink</option>
                                                <option value="geography">geography</option>
                                                <option value="history">history</option>
                                                <option value="language">language</option>
                                                <option value="mathematics">mathematics</option>
                                                <option value="music">music</option>
                                                <option value="people_and_places">people_and_places</option>
                                                <option value="religion_and_mythology">religion_and_mythology</option>
                                                <option value="science_and_nature">science_and_nature</option>
                                                <option value="sport_and_leisure">sport_and_leisure</option>
                                                <option value="tech_an_video_games">tech_an_video_games</option>
                                                <option value="toys_and_games">toys_and_games</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-row px-2">
                                    <button type="submit" class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold">Load Single Answer Questions</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="flex flex-col px-2 mt-4">
                        <h2 class="raleway">Trivia's</h2>
                        <table class="my-4">
                            <tr class="border-b-slate-300 border-b">
                                <th class="px-2 py-2">Title</th>
                                <th class="px-2 py-2">Difficulty</th>
                                <th class="px-2 py-2">Category</th>
                                <th class="px-2 py-2">Active</th>
                                <th class="px-2 py-2">Private</th>
                                <th class="px-2 py-2">Premium</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                            @foreach($trivias as $trv)
                                <tr>
                                    <td class="text-center">{{ $trv['title'] }}</td>
                                    <td class="text-center">{{ Str::limit($trv['difficulty'], 50, '...') }}</td>
                                    <td class="text-center">{{ $trv->category['name'] }}</td>
                                    <td class="text-center">{{ $trv['is_active'] }}</td>
                                    <td class="text-center">{{ $trv['private'] }}</td>
                                    <td class="text-center">{{ $trv['is_premium'] }}</td>
                                    <td class="flex flex-row justify-around">
                                        <div class="px-1">
                                            <a class="inline-block py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" href="/trv/management/trivia/{{ $trv['id'] }}">Edit</a>
                                        </div>
                                        <div class="px-1">
                                            <button class="py-2 px-4 shadow-md bg-red-500 text-slate-100 font-semibold">Delete</button>
                                        </div>
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