<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reflections</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    @include('layouts.public-nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="relative bg-white rounded-3xl p-8 shadow-sm border border-gray-200 mb-8 overflow-hidden transition hover:shadow-md duration-300">
            <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-70 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -mb-16 -ml-16 w-48 h-48 bg-purple-50 rounded-full blur-3xl opacity-70 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight mb-2">
                        Welcome back, <span class="text-indigo-600">{{  auth()->user()->name }}</span> 👋
                    </h1>
                    <p class="text-gray-500 text-lg">Ready to log today's progress and grow your skills?</p>
                </div>
                <a href="{{ route('reflection') }}" class="shrink-0 group inline-flex items-center justify-center px-8 py-4 text-base font-black rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-1">
                    <span class="mr-3 text-2xl group-hover:scale-110 transition-transform">✨</span>
                    Log New Reflection
                </a>
            </div>
        </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                    <h2 class="text-lg font-black text-gray-900">Recent Activity</h2>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Latest entries</span>
                </div>

                <div class="p-6">
                    @if($recentReflections->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-5xl mb-4 text-gray-300">📝</div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">No reflections yet</h3>
                            <p class="text-gray-500 text-sm mb-4">Start tracking your skill development by logging your
                                first entry.</p>
                            <a href="{{ route('reflection') }}"
                                class="text-indigo-600 font-bold hover:text-indigo-800 transition">Log your first reflection
                                &rarr;</a>
                        </div>
                    @else

                        <div class="space-y-6">
                            @foreach($recentReflections as $reflection)
                                @php

                                    $assessment = $reflection->skillAssessments->first();
                                    $score = $assessment->self_score ?? 0;
                                    $skillName = $assessment->skill->name ?? 'Unspecified Skill';
                                @endphp
                                <div
                                    class="flex items-start gap-4 p-4 rounded-xl border border-gray-100 hover:border-indigo-100 hover:bg-indigo-50/30 transition">
                                    <div
                                        class="flex-shrink-0 flex flex-col items-center justify-center w-12 h-12 rounded-full @if($score >= 4) bg-emerald-100 text-emerald-700 @elseif($score == 3) bg-amber-100 text-amber-700 @else bg-red-100 text-red-700 @endif border-2 border-white shadow-sm font-black text-lg">
                                        {{ $score }}
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex justify-between items-start mb-1">
                                            <h4 class="font-bold text-gray-900">{{ $skillName }}</h4>
                                            <span
                                                class="text-xs font-bold text-gray-400">{{ $reflection->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 line-clamp-2">{{ $reflection->title }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 text-center border-t border-gray-100 pt-4">
                            <a href="{{ route('reflection') }}"
                                class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition">View all
                                reflections &rarr;</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-6">


            <div
                class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-6 shadow-md text-white flex flex-col h-full justify-between relative overflow-hidden group">

                <div
                    class="absolute -top-10 -right-10 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl transition-transform group-hover:scale-150 duration-700">
                </div>

                <div class="relative z-10">
                    <div class="text-3xl mb-4">📈</div>
                    <h2 class="text-xl font-black mb-2">Track Your Growth</h2>
                    <p class="text-indigo-100 text-sm mb-6">Dive into your personal analytics dashboard to see your
                        progress mapped out over time.</p>
                </div>

                <a href="{{ route('analytics') }}"
                    class="relative z-10 w-full bg-white text-indigo-700 font-black py-3 rounded-xl text-center shadow-sm hover:shadow-md hover:bg-gray-50 transition">
                    View Analytics Dashboard
                </a>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-900 mb-2 flex items-center"><span class="mr-2">💡</span> Pro Tip</h3>
                <p class="text-sm text-gray-600">Students who log reflections at least 3 times a week show a 40% higher
                    retention of new skills. Make it a habit!</p>
            </div>

        </div>
    </div>
    </div>

    </div>
    </div>

</body>

</html>