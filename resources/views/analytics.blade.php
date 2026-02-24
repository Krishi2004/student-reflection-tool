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
                            class="flex-col items-center justify-center text-gray-500 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            <div class="text-5xl mb-3">🤷‍♂️</div>
                            <p class="text-lg font-bold text-gray-700">No reflections logged for this skill yet.</p>
                            <p class="text-sm">Once you add a reflection for this skill, your growth graph will appear here.
                            </p>
                            <p id="debugText" class="text-[10px] text-gray-400 mt-6 font-mono"></p>
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
                const radar_data = allSkills.map(skillName => {
                    if (rawData[skillName]) {
                        const points = rawData[skillName];
                        return parseFloat(points[points.length - 1].y);
                    }
                    return 0;
                });


                const radarCtx = document.getElementById('radarChart').getContext('2d');
                new Chart(radarCtx, {
                    type: 'radar',
                    data: {
                        labels: allSkills,
                        datasets: [{
                            label: 'Current Level',
                            data: radar_data,
                            fill: true,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgb(255, 99, 132)',
                            pointBackgroundColor: 'rgb(255, 99, 132)',
                            pointBorderColor: '#fff',
                            borderDash: [3, 5],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: { min: 0, max: 5, ticks: { stepSize: 1, backdropColor: 'transparent' } }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
                const doughnutLabels = Object.keys(rawData);
                const doughnutData = doughnutLabels.map(skillName => rawData[skillName].length);

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
                const dropdown = document.getElementById('skillDropdown');
                const chartWrapper = document.getElementById('chartWrapper');
                const noDataWrapper = document.getElementById('noDataWrapper');
                const singlePointWarning = document.getElementById('singlePointWarning');
                const debugText = document.getElementById('debugText');
                let myChart = null;

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

                function aggressiveMatch(str) {
                    return String(str).replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
                }

                function updateChart() {
                    const rawSelected = dropdown.value;
                    let selectedSkill = 'all';

                    if (rawSelected !== 'all') {
                        selectedSkill = Object.keys(rawData).find(
                            key => aggressiveMatch(key) === aggressiveMatch(rawSelected)
                        );
                    }

                    if (singlePointWarning) singlePointWarning.style.display = 'none';

                    if (rawSelected !== 'all' && !selectedSkill) {
                        chartWrapper.style.display = 'none';
                        noDataWrapper.style.display = 'flex';
                        if (debugText) debugText.innerText = `DEBUG -> Searched: [${rawSelected}]. Has: [${Object.keys(rawData).join(', ')}]`;
                        if (myChart) myChart.destroy();
                        return;
                    }

                    if (selectedSkill !== 'all' && rawData[selectedSkill] && rawData[selectedSkill].length === 1) {
                        if (singlePointWarning) singlePointWarning.style.display = 'block';
                    }

                    chartWrapper.style.display = 'block';
                    noDataWrapper.style.display = 'none';

                    let dates = new Set();
                    let skillsToCheck = selectedSkill === 'all' ? Object.keys(rawData) : [selectedSkill];

                    skillsToCheck.forEach(skill => {
                        if (rawData[skill]) rawData[skill].forEach(point => dates.add(point.x));
                    });
                    let labels = Array.from(dates);

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
                                borderColor: themeColors[colorIndex % themeColors.length],
                                backgroundColor: themeColors[colorIndex % themeColors.length],
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
                                    min: 0, max: 5.5,
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