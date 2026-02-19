<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @include('layouts.public-nav')
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
    </style>
</head>

<body>
    <div class="center">
        <div style="overflow: auto; margin-bottom: 20px;">
            <h2 class="float-left text-lg font-bold">Edit Goal</h2>
            <button id="close" style="float: right;" class="text-gray-500 hover:text-red-500 font-bold">X</button>
        </div>


        <form action="{{ route('goals.update', $goal->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Goal Title</label>
                    <input type="text" name="title" value="{{ old('title', $goal->title ?? '') }}"
                        class="w-full rounded border-gray-300 @error('title') border-red-500 @enderror">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Target Skill</label>
                    <select name="skill_id"
                        class="w-full rounded border-gray-300 @error('skill_id') border-red-500 @enderror">
                        <option value="">Select...</option>
                        @foreach($skills as $skill)
                            <option value="{{ $skill->id }}" {{ old('skill_id', $goal->skill_id ?? '') == $skill->id ? 'selected' : ''}}>
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
                    <input value="{{ old('target_score', $goal->target_score) }}" type="number" name="target_score"
                        min="1" max="5" required class="w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Deadline</label>
                    <input value="{{ old('deadline', $goal->deadline) }}" type="date" name="deadline" required
                        class="w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select class="w-full rounded border-gray-300" name="status">
                        @foreach (\App\Models\Goal::getStatus() as $statuses)
                            <option selected="Selected" value="{{ $statuses }}" {{ old('status', $goal->status) == $statuses ? 'selected' : '' }}>{{ $statuses }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-3 mb-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700">How (Action Plan/ Description)</label>
                    <textarea name="description" rows="2" required class="w-full rounded border-gray-300"
                        placeholder="I will practice in front of a mirror twice a week and join a debate club">{{ old('description', $goal->description) }}</textarea>
                    @error('analysis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>



            <div class="flex justify-end space-x-3 pt-4 border-t">

                <a href='{{ route('goals') }}'
                    class="px-4 py-2 text-gray-600 hover:text-gray-800 border rounded">Cancel</a>
                <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Save</button>
            </div>
        </form>

    </div>

    <script>
        $(document).ready(function () {
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


            $('#cancelBtn2').on('click', function () {
                $('.center').fadeOut();
                $('#show').fadeIn();
            });
        });
    </script>

</body>

</html>