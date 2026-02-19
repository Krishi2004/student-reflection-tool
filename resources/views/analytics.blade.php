<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @include('layouts.public-nav')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-12">
    
    <h1 class="text-2xl font-bold mb-6">Performance Analytics</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-gray-500 text-xs uppercase font-semibold tracking-wider">Total Goals</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $stats['total_goals'] }}</p>
        </div>
        
        </div>

    </div>
</html>