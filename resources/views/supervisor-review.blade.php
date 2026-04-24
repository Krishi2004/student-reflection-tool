<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Reflection</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 p-8">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Student Reflection Review</h1>

        <div class="space-y-6">
            <div class="flex justify-between items-start border-b border-gray-100 pb-4">

                <div>
                    <h2 class="text-sm font-semibold text-gray-500 uppercase">Title</h2>
                    <p class="text-gray-900 text-lg font-medium">{{ $reflection->title ?? 'Untitled Reflection' }}</p>
                </div>

                <div class="text-right">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase">Student Name</h2>
                    <p class="text-gray-900 text-lg font-medium">{{ $reflection->user->name ?? 'Unknown Student' }}</p>
                </div>

            </div>

            <div>
                <h2 class="text-sm font-semibold text-gray-500 uppercase">Situation</h2>
                <p class="text-gray-700 bg-gray-50 p-3 rounded border whitespace-pre-wrap">
                    {{ $reflection->narrative['situation'] ?? 'No data provided.' }}
                </p>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-gray-500 uppercase">Action</h2>
                <p class="text-gray-700 bg-gray-50 p-3 rounded border whitespace-pre-wrap">
                    {{ $reflection->narrative['action'] ?? 'No data provided.' }}
                </p>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-gray-500 uppercase">Result</h2>
                <p class="text-gray-700 bg-gray-50 p-3 rounded border whitespace-pre-wrap">
                    {{ $reflection->narrative['result'] ?? 'No data provided.' }}
                </p>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-500 uppercase">Analysis</h2>
                <p class="text-gray-700 bg-gray-50 p-3 rounded border whitespace-pre-wrap">
                    {{ $reflection->narrative['analysis'] ?? 'No data provided.' }}
                </p>
            </div>
        </div>
        <div class="mt-8 pt-6 border-t border-gray-200">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 p-4 rounded-lg border border-red-200">
                    <h3 class="text-sm font-bold text-red-800 uppercase mb-2">Wait, something went wrong:</h3>
                    <ul class="text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ request()->fullUrl() }}" method="POST">
                @csrf

                <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <label for="verifier_score" class="block text-sm font-semibold text-blue-900 uppercase mb-2">
                        Evaluate this Skill (1-5)
                    </label>
                    <select name="verifier_score" id="verifier_score" required
                        class="w-full p-2 border border-blue-200 rounded text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled selected>Select a score...</option>
                        <option value="1">1 - Needs Significant Improvement</option>
                        <option value="2">2 - Below Expectations</option>
                        <option value="3">3 - Meets Expectations</option>
                        <option value="4">4 - Exceeds Expectations</option>
                        <option value="5">5 - Exceptional</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded hover:bg-blue-700 transition">
                    Approve & Verify Reflection
                </button>

            </form>


        </div>
</body>

</html>