<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reflections</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .center {
            margin: auto;
            width: 60%;
            padding: 30px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            background-color: white;
            border-radius: 8px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .hideform {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-100">

    @include('layouts.public-nav')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">My Reflections</h1>

            <button id="show" type="button"
                class="bg-indigo-600 text-white hover:bg-indigo-700 font-bold py-2 px-4 rounded-full shadow transition">
                + Add New Reflection
            </button>
        </div>

        <div class="center hideform">
            <div style="overflow: auto; margin-bottom: 20px;">
                <h2 class="float-left text-lg font-bold">New Structured Reflection</h2>
                <button id="close" style="float: right;" class="text-gray-500 hover:text-red-500 font-bold">X</button>
            </div>

            <form id="reflectionForm" action="{{ route('reflections.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            class="w-full rounded border-gray-300 @error('title') border-red-500 @enderror">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Skill</label>
                        <select name="skill_id" id="skill_id_select"
                            class="w-full rounded border-gray-300 @error('skill_id') border-red-500 @enderror">
                            <option value="">Select...</option>
                            @foreach($skills as $skill)
                                <option value="{{ $skill->id }}" {{ old('skill_id') == $skill->id ? 'selected' : '' }}>
                                    {{ $skill->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('skill_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-3 mb-4">

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Situation</label>
                        <textarea id='situationInput' name="situation" rows="2" required
                            class="w-full rounded border-gray-300 gibberish-check"
                            placeholder="Context...">{{ old('situation') }}</textarea>
                        @error('situation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                        <div id="error-situation" style="display: none;"
                            class="text-red-500 text-sm font-bold mt-2 items-center">
                            <span class="mr-2">⚠️</span> <span class="error-text"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Task</label>
                        <textarea id='taskInput' name="task" rows="2" required
                            class="w-full rounded border-gray-300 gibberish-check"
                            placeholder="What was the task?">{{ old('task') }}</textarea>
                        @error('task') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                        <div id="error-situation" style="display: none;"
                            class="text-red-500 text-sm font-bold mt-2 items-center">
                            <span class="mr-2">⚠️</span> <span class="error-text"></span>
                        </div>
                    </div>



                    <div>
                        <label class="block text-sm font-bold text-gray-700">Action (You)</label>
                        <textarea id='actionInput' name="action" rows="3" required
                            class="w-full rounded border-gray-300 gibberish-check"
                            placeholder="Your specific actions...">{{ old('action') }}</textarea>
                        @error('action') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                        <div id="error-action" style="display: none;"
                            class="text-red-500 text-sm font-bold mt-2 items-center">
                            <span class="mr-2">⚠️</span> <span class="error-text"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Result</label>
                        <textarea id='resultInput' name="result" rows="2" required
                            class="w-full rounded border-gray-300 gibberish-check"
                            placeholder="Outcome...">{{ old('result') }}</textarea>
                        @error('result') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                        <div id="error-result" style="display: none;"
                            class="text-red-500 text-sm font-bold mt-2 items-center">
                            <span class="mr-2">⚠️</span> <span class="error-text"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Analysis (What did you learn?)</label>
                        <textarea id="analysisInput" name="analysis" rows="2" required
                            class="w-full rounded border-gray-300 gibberish-check"
                            placeholder="If you did this again, what would you do differently?">{{ old('analysis') }}</textarea>
                        @error('analysis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <div id="error-analysis" style="display: none;"
                            class="text-red-500 text-sm font-bold mt-2 items-center">
                            <span class="mr-2">⚠️</span> <span class="error-text"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4 items-end">
                        <div class="bg-gray-50 p-3 rounded border">
                            <label class="block text-sm font-medium text-gray-700">Self Score</label>
                            <input id='scoreRange' type="range" value="{{ old('self_score', 3) }}" name="self_score"
                                min="1" max="5" required
                                class="w-full h-2 bg-gray-300 rounded-1g appearance-none cursor-pointer accent-indigo-600">

                            <div class="flex justify-between items-center mt-2">
                                <span id="scoreValue" class="text-2x1 font-bold text-indigo-600">3</span>
                                <span id="scoreLabel" class="text-2x1 font-bold text-indigo-600">Competent</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supervisor Email</label>
                            <input value="{{ old('supervisor_email') }}" type="email" name="supervisor_email" required
                                class="w-full rounded border-gray-300">
                        </div>
                    </div>

                    <div id="actionPlanSection"
                        class="bg-indigo-50 rounded-2xl shadow-sm border border-indigo-100 overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0 mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-black text-indigo-900 mb-2">📈 Growth Mindset</h3>
                            <p class="text-sm text-indigo-700 mb-4">Since you scored below a 4, what are 3 actionable
                                steps you can take to improve?</p>

                            <div class="space-y-3">
                                <input type="text" name="action_plan[]" id="action1" placeholder="Step 1..."
                                    class="w-full p-3 rounded-xl border border-indigo-200 focus:border-indigo-500">
                                <input type="text" name="action_plan[]" id="action2" placeholder="Step 2..."
                                    class="w-full p-3 rounded-xl border border-indigo-200 focus:border-indigo-500">
                                <input type="text" name="action_plan[]" id="action3" placeholder="Step 3..."
                                    class="w-full p-3 rounded-xl border border-indigo-200 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" id="cancelBtn"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 border rounded">Cancel</button>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Save</button>
                    </div>

            </form>
        </div>

    </div>

    <div class="mt-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Past Entries</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($reflections as $reflection)
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition duration-200">

                    <div class="flex justify-between items-start mb-4">

                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ $reflection->skillAssessments->first()?->skill->name ?? 'Unspecified Skill' }}
                        </span>
                        <span class="text-xs text-gray-500">
                            {{ $reflection->created_at->format('M d, Y') }}
                        </span>

                    </div>

                    <h4 class="text-lg font-bold text-gray-900 mb-2 truncate" title="{{ $reflection->title }}">
                        {{ $reflection->title }}
                    </h4>
                    <div class="flex flex-col mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Self Score</span>
                            <span
                                class="text-xs font-bold text-indigo-600">{{ $reflection->skillAssessments->first()?->self_score ?? 0 }}/5</span>
                        </div>
                        <div class="flex gap-1 h-1.5 w-full">
                            @php $score = $reflection->skillAssessments->first()?->self_score ?? 0; @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <div class="flex-1 rounded-full {{ $score >= $i ? 'bg-indigo-500' : 'bg-gray-200' }}"></div>
                            @endfor
                        </div>
                    </div>
                    <div class="mt-2">
                        <span
                            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            Quality Score: {{ number_format($reflection->r_quality_score, 1) }} / 5.0
                        </span>
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                        <div class="text-sm text-gray-600">
                        </div>
                        <span class="text-xs text-orange-500 font-semibold">
                            @if ($reflection->verified_at)
                                <span class="text-xs text-green-600 font-bold">✅ Verified On
                                    {{ \Carbon\Carbon::parse($reflection->verified_at)->format('d M Y') }}</span>
                            @else
                                <span class="text-xs text-orange-500 font-semibold">Pending Verification</span>

                            @endif
                        </span>
                    </div>
                    <div class="flex gap-2">

                    </div><br>
                    <div class="flex items-center gap-2">
                        <a href="/reflection_edit/{{ $reflection->id }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm transition">Edit</a>

                        <form action="{{ route('reflection.delete', $reflection->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm transition">
                                Delete
                            </button>
                        </form>
                        <button type="button"
                            onclick="LevelUp({{ $reflection->skillAssessments->first()?->skill_id ?? '' }})"
                            class="text-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 font-bold py-1 px-2 rounded transition">
                            + Level Up
                        </button>
                    </div>

                </div>
            @empty

                <div class="col-span-full bg-white rounded-lg p-10 text-center border-2 border-dashed border-gray-300">
                    <p class="text-gray-500 italic mb-2">You haven't submitted any reflections yet.</p>
                    <p class="text-sm text-gray-400">Click the button above to add your first entry.</p>
                </div>
            @endforelse
        </div>
    </div>

    </div>

    <script>
        // used for the 'Level UP' button
        $(document).ready(function () {
            @if ($errors->any())
                $('.center').show();
                $('#show').hide();
            @endif

            $('#show').on('click', function () { // show the reflection form
                $('.center').fadeIn();
                $(this).hide();
            });

            $('#close').on('click', function () { // close the reflection form by 'x' or 'cancel'
                $('.center').fadeOut();
                $('#show').fadeIn();
            });

            $('#cancelBtn').on('click', function () { // close the reflection form by 'x' or 'cancel'
                $('.center').fadeOut();
                $('#show').fadeIn();
            });



            const labels = { 1: 'Starter', 2: 'Beginner', 3: 'Intermediate', 4: 'Advanced', 5: 'Expert' }; // Self score slider labels
            const actionPlanSection = document.getElementById('actionPlanSection');
            const actionInputs = [
                document.getElementById('action1'),
                document.getElementById('action2'),
                document.getElementById('action3')
            ];

            $('#scoreRange').on('input', function () { // updates the UI when the slider is changed
                const val = parseInt($(this).val());
                $('#scoreValue').text(val);
                $('#scoreLabel').text(labels[val]);
                const colorClass = val <= 2 ? 'text-red-500' : (val == 3 ? 'text-orange-500' : 'text-green-600'); // changes the colour when number is higher than 3
                $('#scoreValue').removeClass('text-red-500 text-orange-500 text-green-600 text-indigo-600').addClass(colorClass);

                if (val < 4) { // shows the action plan when the slider is less than 4
                    actionPlanSection.style.maxHeight = "500px";
                    actionPlanSection.style.opacity = "1";
                    actionInputs.forEach(input => input.setAttribute('required', 'true'));
                } else {
                    actionPlanSection.style.maxHeight = "0px";
                    actionPlanSection.style.opacity = "0";
                    actionInputs.forEach(input => {
                        input.removeAttribute('required');
                        input.value = "";
                    });
                }
            });
            $('#scoreRange').trigger('input');

            const form = document.getElementById('reflectionForm');
            const textareas = document.querySelectorAll('.gibberish-check');

            function isGibberish(text) { // checks if the user has inputted actual sentences rather than trying to bypass the 20 character limit
                if (text.trim().length === 0) return null;
                if (text.trim().length < 20) return "Response is too short. Please elaborate!"; // minimum 20 character 
                if (!/^[a-zA-Z0-9\s.,!?'"()\-]+$/.test(text)) return "Please use standard English characters only."; // uses regex to detect standard english
                if (!/[aeiouy]/i.test(text)) return "Please use real words (missing vowels detected)."; // Vowel check 
                if (/[^aeiouy\s]{5,}/i.test(text)) return "This looks like a keyboard smash. Please write clearly."; //detects consectutive constants
                if (/(.)\1{4,}/.test(text)) return "Please avoid repeating the same character over and over."; // detects repeated characters
                return null;
            }

            if (form && textareas.length > 0) {

                form.addEventListener('submit', function (event) { // waits until the user has clicked submit
                    let hasError = false;
                    let firstErrorElement = null;

                    textareas.forEach(textarea => { // runs the isGibberish function on all fields
                        const errorMessage = isGibberish(textarea.value);
                        const errorContainer = document.getElementById('error-' + textarea.name);

                        if (errorContainer) {
                            const errorText = errorContainer.querySelector('.error-text');

                            if (errorMessage) {
                                hasError = true;
                                if (errorText) errorText.innerText = errorMessage;
                                errorContainer.style.display = 'flex';
                                textarea.classList.add('border-red-500', 'ring-red-200', 'ring-2');

                                if (!firstErrorElement) firstErrorElement = textarea;
                            }
                        }
                    });

                    if (hasError) {
                        event.preventDefault();
                        if (firstErrorElement) firstErrorElement.focus();
                    }
                });

                textareas.forEach(textarea => { // validation before they move on to the next box
                    const errorContainer = document.getElementById('error-' + textarea.name);

                    if (errorContainer) {
                        const errorText = errorContainer.querySelector('.error-text');

                        textarea.addEventListener('blur', function () { // error message appears when they click off the box rather than waiting to hit submit
                            const errorMessage = isGibberish(textarea.value);
                            if (errorMessage) {
                                if (errorText) errorText.innerText = errorMessage;
                                errorContainer.style.display = 'flex';
                                textarea.classList.add('border-red-500', 'ring-red-200', 'ring-2');
                            }
                        });

                        textarea.addEventListener('input', function () {
                            errorContainer.style.display = 'none';
                            textarea.classList.remove('border-red-500', 'ring-red-200', 'ring-2');
                        });
                    }
                });
            }

        });

        function LevelUp(skillId) { // form for the Level up button
            if (skillId) {
                $('#skill_id_select').val(skillId);
            }
            $('.center').fadeIn();
            $('#show').hide();
            $('input[name="title"]').focus();
        }
    </script>
</body>

</html>