<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Goal</title>
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
            <a href="{{ route('goals') }}" style="float: right;" class="text-gray-500 hover:text-red-500 font-bold">X</a>
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

            <div class="grid grid-cols-2 gap-4 mb-4 items-start">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Target Score (1-5)</label>
                    <input type="number" name="target_score" value="{{ old('target_score', $goal->target_score) }}"
                        min="1" max="5" required class="w-full rounded border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Deadline</label>
                    <input type="date" name="deadline" value="{{ old('deadline', $goal->deadline) }}" required
                        class="w-full rounded border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="w-full rounded border-gray-300">
                        @foreach (\App\Models\Goal::getStatus() as $status)
                            <option value="{{ $status }}" {{ old('status', $goal->status) == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" class="w-full rounded border-gray-300">
                </div>

                <div class="col-span-2 pt-4 border-t">
                    <h3 class="block text-sm font-medium text-gray-700">Break it down</h3>
                    <p class="mb-4 text-sm text-gray-500">Add smaller, manageable steps to help you reach this goal.</p>

                    <div id="steps-container">
                        
                        {{-- Load existing steps from the database --}}
                        @if($goal->actionSteps->count() > 0)
                            @foreach ($goal->actionSteps as $step)
                                <div class="flex gap-2 mb-3 step-row">
                                    <input type="text" name="steps[]" value="{{ $step->description }}" required 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="button" class="px-3 py-2 font-bold text-red-500 hover:text-red-700 remove-step-btn" title="Remove this step">✕</button>
                                </div>
                            @endforeach
                        @else
                            {{-- Fallback if no steps exist yet --}}
                            <div class="flex gap-2 mb-3 step-row">
                                <input type="text" name="steps[]" placeholder="Next step..." required 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="button" class="px-3 py-2 font-bold text-red-500 hover:text-red-700 remove-step-btn" title="Remove this step">✕</button>
                            </div>
                        @endif

                    </div>

                    <button type="button" id="add-step-btn"
                        class="flex items-center mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        + Add another step
                    </button>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('goals') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 border rounded">Cancel</a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Save</button>
            </div>
        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('steps-container');
            const addButton = document.getElementById('add-step-btn');

            // 1. Add new step
            if(addButton && container) {
                addButton.addEventListener('click', function () {
                    const newRow = document.createElement('div');
                    newRow.className = 'flex gap-2 mb-3 step-row';
                    newRow.innerHTML = `
                        <input type="text" name="steps[]" placeholder="Next step..." required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="button" class="px-3 py-2 font-bold text-red-500 hover:text-red-700 remove-step-btn" title="Remove this step">✕</button>
                    `;
                    container.appendChild(newRow);
                });
            }

            // 2. Remove step (works for both old and new steps)
            if(container) {
                container.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-step-btn')) {
                        e.target.closest('.step-row').remove();
                    }
                });
            }
        });
    </script>

</body>
</html>