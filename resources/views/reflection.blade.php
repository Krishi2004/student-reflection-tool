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
            

            <button id="show" type="button" class="bg-indigo-600 text-white hover:bg-indigo-700 font-bold py-2 px-4 rounded-full shadow transition">
                + Add New Reflection
            </button>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif


        <div class="center hideform">
            <div style="overflow: auto; margin-bottom: 20px;">
                <h2 class="float-left text-lg font-bold">New Structured Reflection</h2>
                <button id="close" style="float: right;" class="text-gray-500 hover:text-red-500 font-bold">X</button>
            </div>

            <form action="{{ route('reflections.store') }}" method="POST">
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
                        <select name="skill_id" class="w-full rounded border-gray-300 @error('skill_id') border-red-500 @enderror">
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
                        <textarea  name="situation" rows="2" required class="w-full rounded border-gray-300" placeholder="Context...">{{ old('situation') }}</textarea>
                        @error('situation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-indigo-700">Action (You)</label>
                        <textarea name="action" rows="3" required class="w-full rounded border-indigo-200 bg-indigo-50" placeholder="Your specific actions...">{{ old('action') }}</textarea>
                        @error('action') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Result</label>
                        <textarea value="{{ old('result') }}" name="result" rows="2" required class="w-full rounded border-gray-300" placeholder="Outcome...">{{ old('result') }}</textarea>
                        @error('result') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Analysis (What did you learn?)</label>
                        <textarea value="{{ old('analysis') }}" name="analysis" rows="2" required class="w-full rounded border-gray-300" placeholder="If you did this again, what would you do differently?">{{ old('analysis') }}</textarea>
                        @error('analysis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>


                <div class="grid grid-cols-2 gap-4 mb-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Self Score (1-5)</label>
                        <input value="{{ old('self_score') }}" type="number" name="self_score" min="1" max="5" required class="w-full rounded border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Supervisor Email</label>
                        <input value="{{ old('supervisor_email') }}"type="email" name="supervisor_email" required class="w-full rounded border-gray-300">
                    </div>
                </div>


                <div class="flex justify-end space-x-3 pt-4 border-t">

                    <button type="button" id="cancelBtn" class="px-4 py-2 text-gray-600 hover:text-gray-800 border rounded">Cancel</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Save</button>
                </div>
            </form>

        </div>
            <div class="mt-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Past Entries</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($reflections as $reflection)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition duration-200">

                        <div class="flex justify-between items-start mb-4">

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $reflection->skillAssessments->first()->skill->name ?? 'Unspecified Skill' }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $reflection->created_at->format('M d, Y') }}
                            </span>

                        </div>
                        
                        <h4 class="text-lg font-bold text-gray-900 mb-2 truncate" title="{{ $reflection->title }}">
                            {{ $reflection->title }}
                        </h4>
                        
                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                            <div class="text-sm text-gray-600">
                                <span class="font-bold text-gray-900">{{ $reflection->skillAssessments->first()->self_score ?? '-' }}/5</span> Self Score
                            </div>
                            <span class="text-xs text-orange-500 font-semibold">Pending Verification</span>
                        </div>
                            <!-- DELETE FORM START -->
                            <form action="{{ route('reflection.delete', $reflection->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf                <!-- Security Token -->
                                @method('DELETE')    <!-- This tells Laravel: "Treat this POST as a DELETE" -->

                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm transition">
                                    Delete
                                </button>
                            </form>
                            <!-- DELETE FORM END -->
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
        $(document).ready(function() {
            @if ($errors->any())
                $('.center').show();
                $('#show').hide();
            @endif
        

            $('#show').on('click', function () {
                $('.center').fadeIn(); 
                $(this).hide();
            });


            $('#close').on('click', function (e) {
                e.preventDefault();
                $('.center').fadeOut();
                $('#show').fadeIn();
            });


            $('#cancelBtn').on('click', function () {
                $('.center').fadeOut();
                $('#show').fadeIn();
            });
        });
    </script>

</body>
</html>