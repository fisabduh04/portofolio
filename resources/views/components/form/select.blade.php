<div>
    @props(['options', 'id', 'name', 'label' => null, 'placeholder' => null, 'selected' => null,'value'=>null])
    @if($label)
    <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
    </label>
    @endif
    <select id="{{ $id }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'bg-gray-50 border border-gray-300
        text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700
        dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
        dark:focus:border-blue-500']) }}>
        @if($placeholder)
        <option value="" disabled>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $optionLabel)
        <option value="{{ $value }}" {{ (string)$value===(string)$selected ? 'selected' : '' }}>
            {{ $value }}
        </option>
        @endforeach


    </select>
</div>