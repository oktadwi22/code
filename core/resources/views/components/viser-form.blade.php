@props(['frontend' => false])

@foreach ($formData as $data)
    @php
        $editItem = collect($editData)
            ->where('name', $data->name)
            ->first();
    @endphp


    <div @class([
        'col-xl-6' => !$frontend,
        'col-xl-12' => $frontend,
    ])>
        <div class="form-group">
            <label class="form--label">{{ __($data->name) }}</label>
            @if ($data->type == 'text')
                <input type="text" class="form--control form--control--sm" name="{{ $data->label }}"
                    value="{{ @$editItem->value ?? old($data->label) }}"
                    @if ($data->is_required == 'required') required @endif>
            @elseif($data->type == 'textarea')
                <textarea class="form--control form--control--sm" name="{{ $data->label }}"
                    @if ($data->is_required == 'required') required @endif>{{ @$editItem->value ?? old($data->label) }}</textarea>
            @elseif($data->type == 'select')
                <select class="select form--control--sm form--control simple"
                    name="{{ $data->label }}@if (@$data->multi_select == 1)[]@endif"
                    @if ($data->is_required == 'required') required @endif {{ @$data->multi_select == 1 ? 'multiple' : '' }}>
                    <option value="">@lang('Select One')</option>
                    @foreach ($data->options as $item)
                        <option value="{{ $item }}"
                            {{ is_array(@$editItem->value) ? (in_array($item, @$editItem->value) ? 'selected' : '') : (@$editItem->value == $item || $item == old($data->label) ? 'selected' : '') }}>
                            {{ __($item) }}
                        </option>
                    @endforeach
                </select>
            @elseif($data->type == 'checkbox')
                @foreach ($data->options as $option)
                    <div class="form-check">
                        <input class="form-check-input" name="{{ $data->label }}[]" type="checkbox"
                            value="{{ $option }}" id="{{ $data->label }}_{{ titleToKey($option) }}">
                        <label class="form-check-label"
                            for="{{ $data->label }}_{{ titleToKey($option) }}">{{ $option }}</label>
                    </div>
                @endforeach
            @elseif($data->type == 'radio')
                @foreach ($data->options as $option)
                    <div class="form-check">
                        <input class="form-check-input" name="{{ $data->label }}" type="radio" value="{{ $option }}" id="{{ $data->label }}_{{ titleToKey($option) }}"
                            @checked($option == (@$editItem->value ?? old($data->label)))>
                        <label class="form-check-label"
                            for="{{ $data->label }}_{{ titleToKey($option) }}">{{ $option }}</label>
                    </div>
                @endforeach
            @elseif($data->type == 'file')
                <input type="file" class="form--control form--control--sm" name="{{ $data->label }}"
                    @if ($data->is_required == 'required') required @endif
                    accept="@foreach (explode(',', $data->extensions) as $ext) .{{ $ext }}, @endforeach">
                <pre class="text--base mt-1">@lang('Supported mimes'): {{ $data->extensions }}</pre>
            @endif
        </div>
    </div>
@endforeach
