<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Progress Analytics</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50">

    @include('layouts.public-nav')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-black text-gray-900 mb-2">Skill Growth Analytics</h1>
        <p class="text-gray-600 mb-8">Track your proficiency improvements over time based on your reflections.</p>

        @if(empty($chartData))
            <div class="bg-white rounded-2xl p-12 text-center border-2 border-dashed border-gray-300">
                <div class="text-5xl mb-4">📊</div>
                <p class="text-gray-700 font-bold text-lg mb-2">No data to display yet.</p>
                <p class="text-sm text-gray-500">Go write some reflections to start tracking your growth!</p>
            </div>
        @else

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2x1 p-6 shadow-sm border-gray-200 flex items-center transition hover:shadow-md">
                    <div class="bg-indigo-100 p-4 rounded-x1 text-indigo-600 text-2x1 mr-4">📝</div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Total Reflections</p>
                        <p class="text-3x1 font-black text-gray-900">{{ $totalReflections }}</p>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex items-center transition hover:shadow-md">
                    <div class="bg-emerald-100 p-4 rounded-xl text-emerald-600 text-2xl mr-4">🏆</div>
                    <div class="overflow-hidden">
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Top Skill (Avg)</p>
                        <p class="text-xl font-black text-gray-900 truncate">{{ $topSkill }}</p>
                    </div>
                </div>


                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex items-center transition hover:shadow-md">
                    <div class="bg-amber-100 p-4 rounded-xl text-amber-600 text-2xl mr-4">🔥</div>
                    <div class="overflow-hidden">
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Most Practiced</p>
                        <p class="text-xl font-black text-gray-900 truncate">{{ $mostPracticedSkill }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">

                <!-- THE SKILL DROPDOWN FILTER -->
                <div class="mb-6 border-b pb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Select a Skill to View:</label>
                    <select id="skillDropdown"
                        class="w-full md:w-1/3 p-2 rounded-lg border-gray-300 border shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white cursor-pointer">
                        <option value="all">Compare All Skills</option>
                        @foreach($skills as $skill)
                            <option value="{{ $skill->name }}">{{ $skill->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- GAMIFIED BANNER (Shows if they only have 1 reflection) -->
                <div id="singlePointWarning" style="display: none;"
                    class="mb-6 bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="text-indigo-500 text-2xl mr-3">💡</div>
                        <div>
                            <p class="text-sm text-indigo-800 font-bold">Tracking Started!</p>
                            <p class="text-xs text-indigo-600">You have logged your first reflection for this skill (shown
                                as a dot). <strong>Log another reflection to unlock your growth line!</strong></p>
                        </div>
                    </div>
                </div>

                <!-- THE GRAPH CONTAINER -->
                <div id="chartWrapper" style="position: relative; height: 400px; width: 100%;">
                    <canvas id="progressChart"></canvas>
                </div>

                <!-- THE "NO DATA" MESSAGE -->
                <div id="noDataWrapper" style="display: none; height: 400px; width: 100%;"
                    class="flex-col items-center justify-center text-gray-500 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                    <div class="text-5xl mb-3">🤷‍♂️</div>
                    <p class="text-lg font-bold text-gray-700">No reflections logged for this skill yet.</p>
                    <p class="text-sm">Once you add a reflection for this skill, your growth graph will appear here.</p>

                    <!-- DEBUGGER TOOL: This will tell us exactly why it failed if it fails -->
                    <p id="debugText" class="text-[10px] text-gray-400 mt-6 font-mono"></p>
                </div>

            </div>
        @endif
    </div>

    <!-- The JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if(!empty($chartData))
                const rawData = {!! json_encode((object) $chartData) !!};
                const dropdown = document.getElementById('skillDropdown');
                const chartWrapper = document.getElementById('chartWrapper');
                const noDataWrapper = document.getElementById('noDataWrapper');
                const singlePointWarning = document.getElementById('singlePointWarning');
                const debugText = document.getElementById('debugText');
                let myChart = null;

                // Handle "Same Day" reflections
                for (const skill in rawData) {
                    if (rawData.hasOwnProperty(skill)) {
                        const dateCounts = {};
                        rawData[skill].forEach(point => {
                            let originalDate = point.x;
                            if (dateCounts[originalDate]) {
                                dateCounts[originalDate]++;
                                point.x = `${originalDate} (Ref ${dateCounts[originalDate]})`;
                            } else {
                                dateCounts[originalDate] = 1;
                            }
                        });
                    }
                }

                // THE FIX: AGGRESSIVE MATCHER
                // Strips all spaces, hyphens, and weird characters so "Leadership " exactly matches "leadership"
                function aggressiveMatch(str) {
                    return String(str).replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
                }

                function updateChart() {
                    const rawSelected = dropdown.value;
                    let selectedSkill = 'all';

                    // Use the Aggressive Matcher
                    if (rawSelected !== 'all') {
                        selectedSkill = Object.keys(rawData).find(
                            key => aggressiveMatch(key) === aggressiveMatch(rawSelected)
                        );
                    }

                    // Reset warnings
                    if (singlePointWarning) singlePointWarning.style.display = 'none';

                    // NO DATA FALLBACK CHECK
                    if (rawSelected !== 'all' && !selectedSkill) {
                        chartWrapper.style.display = 'none';
                        noDataWrapper.style.display = 'flex';

                        // Print Debug Info to the screen so you can see the glitch
                        if (debugText) {
                            debugText.innerText = `DEBUG -> You searched for: [${rawSelected}]. Database actually has: [${Object.keys(rawData).join(', ')}]`;
                        }

                        if (myChart) myChart.destroy();
                        return;
                    }

                    // Gamification Check (1 Reflection)
                    if (selectedSkill !== 'all' && rawData[selectedSkill] && rawData[selectedSkill].length === 1) {
                        if (singlePointWarning) singlePointWarning.style.display = 'block';
                    }

                    // We have data! Show chart.
                    chartWrapper.style.display = 'block';
                    noDataWrapper.style.display = 'none';

                    // Setup Dates (X-Axis)
                    let dates = new Set();
                    let skillsToCheck = selectedSkill === 'all' ? Object.keys(rawData) : [selectedSkill];

                    skillsToCheck.forEach(skill => {
                        if (rawData[skill]) {
                            rawData[skill].forEach(point => dates.add(point.x));
                        }
                    });
                    let labels = Array.from(dates);

                    // Setup Datasets (Lines)
                    const colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
                    let datasets = [];
                    let colorIndex = 0;

                    skillsToCheck.forEach(skill => {
                        if (rawData[skill]) {
                            let dataPoints = labels.map(label => {
                                let foundPoint = rawData[skill].find(p => p.x == label);
                                return foundPoint ? parseFloat(foundPoint.y) : null;
                            });

                            datasets.push({
                                label: skill,
                                data: dataPoints,
                                borderColor: colors[colorIndex % colors.length],
                                backgroundColor: colors[colorIndex % colors.length],
                                tension: 0.3,
                                borderWidth: 4,
                                pointRadius: 8,
                                pointHoverRadius: 12,
                                spanGaps: true
                            });
                            colorIndex++;
                        }
                    });

                    if (myChart) myChart.destroy();

                    const ctx = document.getElementById('progressChart').getContext('2d');
                    myChart = new Chart(ctx, {
                        type: 'line',
                        data: { labels: labels, datasets: datasets },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    min: 0,
                                    max: 5.5,
                                    title: { display: true, text: 'Proficiency Level (1-5)', font: { weight: 'bold' } },
                                    ticks: {
                                        stepSize: 1,
                                        callback: function (value) {
                                            if (value === 1) return '1 (Starter)';
                                            if (value === 3) return '3 (Intermediate)';
                                            if (value === 5) return '5 (Expert)';
                                            return value;
                                        }
                                    }
                                },
                                x: { title: { display: true, text: 'Date', font: { weight: 'bold' } } }
                            }
                        }
                    });
                }

                if (dropdown) {
                    dropdown.addEventListener('change', updateChart);
                    updateChart();
                }
            @endif
        });
    </script>
</body>

</html>