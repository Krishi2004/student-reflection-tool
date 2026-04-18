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
            <div
                class="bg-gradient-to-r from-indigo-50 to purple-50 rounded-2xl p-6 shadow-sm border border-indigo-100 mb-6 flex items-start transition hover:shadow-md">
                <div
                    class="bg-white p-3 rounded-full shadow-sm text-indigo-600 text-xl mr-4 border border-indigo-50 flex-shrink-0">
                    ✨
                </div>
                <div>
                    <h2 class="text-xs font-black text-indigo-900 uppercase tracking-wider mb-2"> Auto Generated Insights
                    </h2>
                    <ul class="text-indigo-800 text-sm space-y-2">
                        <li>
                            <strong>Consistency:</strong>You have logged {{ $totalReflections }} reflections so far.
                            @if ($totalReflections < 3)
                                You are just getting started—keep logging to unlock deeper trend data!
                            @elseif($totalReflections >= 3 && $totalReflections < 10)
                                You are building excellent momentum.
                            @else
                                You have built a fantastic habit of self-reflection!
                            @endif
                        </li>
                        <li>
                            <strong>Standout Strength:</strong> Your highest recorded proficiency is currently in
                            <strong>{{ $topSkill }}</strong>.
                        </li>
                        <li>
                            <strong>Current Focus:</strong> You are dedicating the majority of your reflection time to
                            <strong>{{ $mostPracticedSkill }}</strong>.
                            @if($topSkill !== $mostPracticedSkill)
                                This focused effort should help pull its score up soon!
                            @else
                                You are actively maintaining your strongest asset.
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border-gray-200 flex items-center transition hover:shadow-md">
                    <div class="bg-indigo-100 p-4 rounded-xl text-indigo-600 text-2xl mr-4">📝</div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Total Reflections</p>
                        <p class="text-3xl font-black text-gray-900">{{ $totalReflections }}</p>
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

                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex items-center transition hover:shadow-md">
                    <div class="bg-amber-100 p-4 rounded-xl text-amber-600 text-2xl mr-4">🔥</div>
                    <div class="overflow-hidden">
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Most Practiced</p>
                        <p class="text-xl font-black text-gray-900 truncate">{{ $mostPracticedSkill }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <div>
                            <h2 class="text-lg font-black text-gray-900">
                                Progress History
                            </h2>
                            <p class="text-xs text-gray-500">Select a skill to view your graph timeline</p>
                        </div>
                        <select id="skillDropdown"
                            class="w-full md:w-1/3 p-2 rounded-lg border-gray-300 border shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white cursor-pointer">
                            <option value="all">Compare All Skills</option>
                            @foreach($skills as $skill)
                                <option value="{{ $skill->name }}">{{ $skill->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div id="singlePointWarning" style="display: none;"
                        class="mb-6 bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-r-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="text-indigo-500 text-2xl mr-3">💡</div>
                            <div>
                                <p class="text-sm text-indigo-800 font-bold">Tracking Started!</p>
                                <p class="text-xs text-indigo-600">You have logged your first reflection for this skill
                                    (shown
                                    as a dot). <strong>Log another reflection to unlock your growth line!</strong></p>
                            </div>
                        </div>
                    </div>
                    <div id="chartWrapper" style="position: relative; height: 400px; width: 100%;">
                        <canvas id="progressChart"></canvas>
                    </div>

                    <div id="noDataWrapper" style="display: none; height: 400px; width: 100%;"
                        class="flex-col items-center justify-center text-gray-500 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                        <div id="noDataWrapper" style="display: none; height: 400px; width: 100%;"
                            class="flex-col items-center justify-center text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 p-8">
                            <div class="text-5xl mb-3">📊</div>
                            <p class="text-lg font-bold text-gray-700">No reflections logged for this skill yet.</p>
                            <p class="text-sm text-gray-500">Once you add a reflection for this skill, your growth graph
                                will appear here.</p>
                            <p id="debugText" class="text-[10px] text-gray-400 mt-6 font-mono italic"></p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-6">

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                        <h2 class="text-lg font-black text-gray-900 mb-1">Current Skill Profile</h2>
                        <p class="text-xs text-gray-500 mb-6">Your most recent proficiency levels.</p>
                        <div style="position: relative; height: 220px; width: 100%;">
                            <canvas id="radarChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                        <h2 class="text-lg font-black text-gray-900 mb-1">Focus Areas</h2>
                        <p class="text-xs text-gray-500 mb-6">Distribution of where you spend your reflection time.</p>
                        <div style="position: relative; height: 220px; width: 100%;">
                            <canvas id="doughnutChart"></canvas>
                        </div>
                    </div>

                </div>

            </div>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if(!empty($chartData))
                const rawData = {!! json_encode((object) $chartData) !!};
                const themeColors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
                const allSkills = {!! json_encode($skills->pluck('name')) !!};

                // --- 1. RADAR CHART ---
                const radar_self_data = allSkills.map(skillName => {
                    // Added safety net: (rawData[skillName]?.self || [])
                    const points = rawData[skillName]?.self || [];
                    if (points.length > 0) {
                        return parseFloat(points[points.length - 1].y);
                    }
                    return 0;
                });

                const radar_verifier_data = allSkills.map(skillName => {
                    // Added safety net: (rawData[skillName]?.verifier || [])
                    const points = rawData[skillName]?.verifier || [];
                    if (points.length > 0) {
                        const lastPoint = points[points.length - 1];
                        return lastPoint && lastPoint.y !== null ? parseFloat(lastPoint.y) : 0;
                    }
                    return 0;
                });

                const radarCtx = document.getElementById('radarChart').getContext('2d');
                new Chart(radarCtx, {
                    type: 'radar',
                    data: {
                        labels: allSkills,
                        datasets: [
                            {
                                label: 'My Current Level',
                                data: radar_self_data,
                                fill: true,
                                backgroundColor: 'rgba(79, 70, 229, 0.2)',
                                borderColor: 'rgb(79, 70, 229)',
                                pointBackgroundColor: 'rgb(79, 70, 229)',
                                pointBorderColor: '#fff',
                            },
                            {
                                label: 'Supervisor Level',
                                data: radar_verifier_data,
                                fill: true,
                                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                borderColor: 'rgb(16, 185, 129)',
                                pointBackgroundColor: 'rgb(16, 185, 129)',
                                pointBorderColor: '#fff',
                                borderDash: [5, 5],
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: { min: 0, max: 5, ticks: { stepSize: 1, backdropColor: 'transparent' } }
                        },
                        plugins: { legend: { display: true, position: 'bottom' } }
                    }
                });

                // --- 2. DOUGHNUT CHART ---
                const doughnutLabels = Object.keys(rawData);
                const doughnutData = doughnutLabels.map(skillName => {
                    // Added safety net
                    return (rawData[skillName]?.self || []).length;
                });

                const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
                new Chart(doughnutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: doughnutLabels,
                        datasets: [{
                            data: doughnutData,
                            backgroundColor: themeColors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 20, usePointStyle: true }
                            }
                        }
                    }
                });

                // --- 3. LINE CHART ---
                const dropdown = document.getElementById('skillDropdown');
                const chartWrapper = document.getElementById('chartWrapper');
                const noDataWrapper = document.getElementById('noDataWrapper');
                const singlePointWarning = document.getElementById('singlePointWarning');
                const debugText = document.getElementById('debugText');
                let myChart = null;

                // Handle duplicate dates with safety nets
                for (const skill in rawData) {
                    if (rawData.hasOwnProperty(skill)) {
                        const dateCounts = {};
                        const selfData = rawData[skill]?.self || [];
                        const verifierData = rawData[skill]?.verifier || [];

                        selfData.forEach((point, index) => {
                            let originalDate = point.x;
                            if (dateCounts[originalDate]) {
                                dateCounts[originalDate]++;
                                let newLabel = `${originalDate} (Ref ${dateCounts[originalDate]})`;
                                point.x = newLabel;
                                if (verifierData[index]) {
                                    verifierData[index].x = newLabel;
                                }
                            } else {
                                dateCounts[originalDate] = 1;
                            }
                        });
                    }
                }

                function aggressiveMatch(str) {
                    return String(str).replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
                }

                function updateChart() {
                    const rawSelected = dropdown ? dropdown.value : 'all';
                    let selectedSkill = 'all';

                    if (rawSelected !== 'all') {
                        selectedSkill = Object.keys(rawData).find(
                            key => aggressiveMatch(key) === aggressiveMatch(rawSelected)
                        );
                    }

                    // Reset UI states
                    if (singlePointWarning) singlePointWarning.style.display = 'none';
                    chartWrapper.style.display = 'block';
                    noDataWrapper.style.display = 'none';

                    // 1. Collect ALL unique dates from the selected data
                    let datesSet = new Set();
                    let skillsToCheck = (selectedSkill === 'all') ? Object.keys(rawData) : [selectedSkill];

                    skillsToCheck.forEach(skill => {
                        if (rawData[skill]) {
                            (rawData[skill].self || []).forEach(p => datesSet.add(p.x));
                            (rawData[skill].verifier || []).forEach(p => datesSet.add(p.x));
                        }
                    });

                    // Sort labels chronologically (Optional, but looks better)
                    let labels = Array.from(datesSet);

                    // 2. Build Datasets
                    let datasets = [];
                    let colorIndex = 0;

                    skillsToCheck.forEach(skill => {
                        if (rawData[skill]) {
                            let selfColor = themeColors[colorIndex % themeColors.length];

                            // MAP SELF SCORES: Look for a match for every label
                            let selfDataPoints = labels.map(label => {
                                let match = rawData[skill].self.find(p => String(p.x) === String(label));
                                return match ? parseFloat(match.y) : null;
                            });

                            datasets.push({
                                label: selectedSkill === 'all' ? `${skill} (Self)` : 'My Self Score',
                                data: selfDataPoints,
                                borderColor: selfColor,
                                backgroundColor: selfColor,
                                tension: 0.3,
                                borderWidth: 4,
                                pointRadius: 6,
                                spanGaps: true // This connects the dots even if there are nulls in between
                            });

                            // MAP VERIFIER SCORES
                            let verifierDataPoints = labels.map(label => {
                                let match = (rawData[skill].verifier || []).find(p => String(p.x) === String(label));
                                return (match && match.y !== null) ? parseFloat(match.y) : null;
                            });

                            let verifierColor = selectedSkill === 'all' ? selfColor : '#10B981';

                            datasets.push({
                                label: selectedSkill === 'all' ? `${skill} (Supervisor)` : 'Supervisor Score',
                                data: verifierDataPoints,
                                borderColor: verifierColor,
                                backgroundColor: verifierColor,
                                borderDash: [8, 5],
                                tension: 0.3,
                                borderWidth: 4,
                                pointRadius: 6,
                                spanGaps: true
                            });

                            colorIndex++;
                        }
                    });

                    // 3. DEBUG: Check if we actually have numbers now
                    console.log("Labels generated:", labels);
                    console.log("Datasets generated:", datasets);

                    if (myChart) myChart.destroy();

                    const ctx = document.getElementById('progressChart').getContext('2d');
                    myChart = new Chart(ctx, {
                        type: 'line',
                        data: { labels: labels, datasets: datasets },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: { mode: 'index', intersect: false }
                            },
                            scales: {
                                y: {
                                    min: 0, max: 5.5,
                                    ticks: { stepSize: 1 }
                                }
                            }
                        }
                    });
                }

                if (dropdown) {
                    dropdown.addEventListener('change', updateChart);
                }
                updateChart();

            @endif
        });
    </script>
</body>

</html>