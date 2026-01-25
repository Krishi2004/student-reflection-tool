<!-- Reflection Page -->
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
                        <input type="text" name="title" required class="w-full rounded border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Skill</label>
                        <select name="skill_id" required class="w-full rounded border-gray-300">
                            <option value="">Select...</option>
                            @foreach($skills as $skill)
                                <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="space-y-3 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Situation</label>
                        <textarea name="situation" rows="2" required class="w-full rounded border-gray-300" placeholder="Context..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-indigo-700">Action (You)</label>
                        <textarea name="action" rows="3" required class="w-full rounded border-indigo-200 bg-indigo-50" placeholder="Your specific actions..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Result</label>
                        <textarea name="result" rows="2" required class="w-full rounded border-gray-300" placeholder="Outcome..."></textarea>
                        <input type="hidden" name="analysis" value="Included in Result"> 
                    </div>
                </div>


                <div class="grid grid-cols-2 gap-4 mb-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Self Score (1-5)</label>
                        <input type="number" name="self_score" min="1" max="5" required class="w-full rounded border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Supervisor Email</label>
                        <input type="email" name="supervisor_email" required class="w-full rounded border-gray-300">
                    </div>
                </div>


                <div class="flex justify-end space-x-3 pt-4 border-t">

                    <button type="button" id="cancelBtn" class="px-4 py-2 text-gray-600 hover:text-gray-800 border rounded">Cancel</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Save</button>
                </div>
            </form>
        </div>

    </div>


    <script>
        $(document).ready(function() {

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