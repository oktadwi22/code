@props([
    'placeholder' => 'Search...',
    'btn' => 'btn--primary',
    'dateSearch' => 'no',
    'keySearch' => 'yes',
    'inputClass' => 'form-control',
])

<form action="" method="GET" class="d-flex flex-wrap gap-2">
    @if ($keySearch == 'yes')
        <x-search-key-field inputClass="{{ $inputClass }}" placeholder="{{ __($placeholder) }}" btn="{{ $btn }}" />
    @endif
    @if ($dateSearch == 'yes')
        <x-search-date-field />
    @endif
</form>
