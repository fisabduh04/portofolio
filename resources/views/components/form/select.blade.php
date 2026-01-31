@props(['options' => [], 'id' => null, 'name' => null, 'label' => null, 'placeholder' => null, 'selected' => null])

<div>
    @if($label)
    <label for="{{ $id ?? $name }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
    </label>
    @endif
    
    <select id="{{ $id ?? $name }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ' . ($errors->has($name) ? 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500' : 'border-gray-300')]) }}>
        @if($placeholder)
            <option value="" {{ is_null($selected) ? 'selected' : '' }}>{{ $placeholder }}</option>
        @endif
        
        @foreach ($options as $val => $label)
            <option value="{{ $val }}" {{ (string)$val === (string)$selected ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>