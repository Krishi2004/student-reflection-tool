<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goals</title>
    @include('layouts.public-nav')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>


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

<body bg-gray-100>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">My Goals</h1>


            <button id="show" type="button"
                class="bg-indigo-600 text-white hover:bg-indigo-700 font-bold py-2 px-4 rounded-full shadow transition">
                + Add New Goal
            </button>
        </div>

        <div id='createmodal' class="center hideform">
            <div style="overflow: auto; margin-bottom: 20px;">
                <h2 class="float-left text-lg font-bold">New Goal</h2>
                <button id="close" style="float: right;" class="text-gray-500 hover:text-red-500 font-bold">X</button>
            </div>

            <form action="{{ route('goals.store') }}" method="POST">
                @csrf


                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Goal Title</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            class="w-full rounded border-gray-300 @error('title') border-red-500 @enderror">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Target Skill</label>
                        <select name="skill_id"
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

                <div class="grid grid-cols-2 gap-4 mb-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Target Score (1-5)</label>
                        <input value="{{ old('target_score') }}" type="number" name="target_score" min="1" max="5"
                            required class="w-full rounded border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deadline</label>
                        <input value="" type="date" name="deadline" required class="w-full rounded border-gray-300">
                    </div>
                </div>
                <div class="space-y-3 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">How (Action Plan/ Description)</label>
                        <textarea value="{{ old('analysis') }}" name="description" rows="2" required
                            class="w-full rounded border-gray-300"
                            placeholder="I will practice in front of a mirror twice a week and join a debate club">{{ old('analysis') }}</textarea>
                        @error('analysis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
        <div class="mt-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">My Goals</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($goals as $goal)
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition duration-200">

                        <div class="flex justify-between items-start mb-4">

                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $goal->skill->name ?? 'Unspecified Skill' }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $goal->created_at->format('M d, Y') }}
                            </span>

                        </div>

                        <h4 class="text-lg font-bold text-gray-900 mb-2 truncate" title="{{ $goal->title }}">
                            {{ $goal->title }}
                        </h4>

                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                            <div class="text-sm text-gray-600">
                                <span class="text-gray-900">Target Score:</span>
                                <span class="font-bold text-gray-900">
                                    {{ $goal->target_score }}
                                </span>
                            </div>
                            <span class="text-xs text-orange-500 font-semibold">{{ $goal->status }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="openViewModal({{ $goal->id }})"
                                class="text-indigo-600 hover:text-indigo-900 text-sm font-bold bg-indigo-50 px-3 py-1 rounded">
                                View Details
                            </button>

                        </div>

                    </div>
                @empty

                    <div class="col-span-full bg-white rounded-lg p-10 text-center border-2 border-dashed border-gray-300">
                        <p class="text-gray-500 italic mb-2">You haven't submitted any goals yet.</p>
                        <p class="text-sm text-gray-400">Click the button above to add your first entry.</p>
                    </div>
                @endforelse
            </div>
        </div>
        <div id="viewModal" class="center hideform">
            <div style="overflow: auto; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                <h2 id="view-title" class="float-left text-xl font-bold text-gray-900">Goal Title</h2>
                <button id="closeView" style="float: right;"
                    class="text-gray-500 hover:text-red-500 font-bold">X</button>
            </div>

            <div class="mb-4">
                <span id="view-skill"
                    class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded uppercase">Skill
                    Name</span>
                <span id="view-status" class="ml-2 border px-2 py-0.5 rounded text-xs font-bold uppercase">Status</span>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Action
                    Plan</label>
                <div id="view-desc" class="text-gray-700 bg-gray-50 p-4 rounded border border-gray-200 leading-relaxed">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 p-3 rounded border">
                    <span class="block text-xs text-gray-500">Target Score</span>
                    <span id="view-target" class="text-lg font-bold text-gray-900">5/5</span>
                </div>
                <div class="bg-gray-50 p-3 rounded border">
                    <span class="block text-xs text-gray-500">Deadline</span>
                    <span id="view-deadline" class="text-lg font-bold text-gray-900">Jan 1, 2026</span>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" id="closeViewBtn"
                    class="px-4 py-2 text-gray-600 hover:text-gray-800 border rounded">Close</button>

                <!-- We can dynamically update these links later if needed -->
                <!-- <a id="view-edit-btn" href="#" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded font-bold">Edit</a> -->
            </div>
        </div>

    </div>
    </div>


    <script>

        const goalsData = @json($goals);

        $(document).ready(function () {
            @if ($errors->any())
                $('#createmodal').fadeIn();
                $('#show').hide();
            @endif


            $('#show').on('click', function () {
                $('#createmodal').fadeIn();
                $(this).hide();
            });

            $('#close, #cancelBtn').on('click', function (e) {
                e.preventDefault();
                $('#createmodal').fadeOut();
                $('#show').fadeIn();
            });


            $('#closeView, #closeViewBtn').on('click', function (e) {
                e.preventDefault();
                $('#viewModal').fadeOut();
                $('#show').fadeIn();
            });
        });


        function openViewModal(id) {
            const goal = goalsData.find(g => g.id === id);
            if (goal) {
                $('#view-title').text(goal.title);
                $('#view-desc').text(goal.description);
                $('#view-target').text(goal.target_score + '/5');
                $('#view-deadline').text(goal.deadline);
                $('#view-skill').text(goal.skill ? goal.skill.name : 'Skill');
                $('#view-status').text(goal.status);

                $('#viewModal').fadeIn();
                $('#show').hide();
            }
        }
    </script>



</body>

</html>