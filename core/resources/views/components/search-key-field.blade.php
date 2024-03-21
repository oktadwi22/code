
@props(['placeholder' => 'Search...', 'inputClass' => 'form-control', 'btn' => 'btn--primary'])
<div class="input-group w-auto flex-fill">
    <input type="search" name="search" class="{{ $inputClass }} bg--white form-control" placeholder="{{ __($placeholder) }}" value="{{ request()->search }}">
    <button class="btn {{ $btn }}" type="submit"><i class="la la-search"></i></button>
</div>
