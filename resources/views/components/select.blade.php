<?php
    $id = \App\Models\Helper::generateRandomInteger();
?>
<label for="{!! $name !!}_{!! $id !!}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $slot }}</label>
<select id="{!! $name !!}_{!! $id !!}" name="{!! $name !!}" @if($required) required @endif class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
    <option selected>{{ $slot }}</option>
    @foreach($items as $item)
        <option value="{!! $item['key'] !!}">{!! $item['value'] !!}</option>
    @endforeach
</select>
