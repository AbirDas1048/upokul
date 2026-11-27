{{--
    One form control from a field definition (config/site_settings.php, config/content_types.php).
    Needs: $field, $value (current value). Optional: $resettable (image fields get a "use default" box).
--}}
@php
    $name = $field['name'];
    $type = $field['type'];
    $id = 'f_'.$name;
    $current = old($name, $value ?? null);
    $required = is_string($field['rules'] ?? null) ? str_contains($field['rules'], 'required') : in_array('required', (array) ($field['rules'] ?? []), true);
    $invalid = $errors->has($name) ? ' is-invalid' : '';
@endphp
<div class="col-12 col-md-{{ $field['col'] ?? 12 }}">
    @if ($type === 'toggle')
        <div class="form-check form-switch">
            <input type="hidden" name="{{ $name }}" value="0">
            <input class="form-check-input" type="checkbox" role="switch" id="{{ $id }}" name="{{ $name }}" value="1" @checked((bool) $current)>
            <label class="form-check-label fw-semibold" for="{{ $id }}">{{ $field['label'] }}</label>
        </div>
    @else
        <label class="form-label" for="{{ $id }}">{{ $field['label'] }}@if ($required) <span class="text-danger">*</span>@endif</label>

        @switch($type)
            @case('textarea')
                <textarea class="form-control{{ $invalid }}" id="{{ $id }}" name="{{ $name }}" rows="{{ $field['rows'] ?? 3 }}" @required($required)>{{ $current }}</textarea>
                @break

            @case('lines')
                <textarea class="form-control{{ $invalid }}" id="{{ $id }}" name="{{ $name }}" rows="{{ $field['rows'] ?? 4 }}" spellcheck="false" @required($required)>{{ $current }}</textarea>
                @break

            @case('select')
                <select class="form-select{{ $invalid }}" id="{{ $id }}" name="{{ $name }}" @required($required)>
                    @foreach ($field['options'] as $optValue => $optLabel)
                        <option value="{{ $optValue }}" @selected((string) $current === (string) $optValue)>{{ $optLabel }}</option>
                    @endforeach
                </select>
                @break

            @case('image')
                <div class="ad-img-field">
                    <img id="{{ $id }}_img" src="{{ $site->url($current) ?: 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' }}" alt="">
                    <div class="flex-grow-1 min-w-0">
                        <input class="form-control form-control-sm{{ $invalid }}" type="file" id="{{ $id }}" name="{{ $name }}" accept="image/png,image/jpeg,image/webp,.ico" data-preview="#{{ $id }}_img">
                        @if (! empty($resettable))
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="reset_{{ $name }}" name="reset_{{ $name }}" value="1">
                                <label class="form-check-label small" for="reset_{{ $name }}">Go back to the default image</label>
                            </div>
                        @endif
                    </div>
                </div>
                @break

            @case('password')
                <div class="input-group">
                    <input class="form-control{{ $invalid }}" type="password" id="{{ $id }}" name="{{ $name }}" value="" autocomplete="new-password" placeholder="••••••••">
                    <button class="btn btn-outline-secondary" type="button" data-toggle-password="#{{ $id }}" aria-label="Show or hide"><i class="fa-solid fa-eye"></i></button>
                </div>
                @break

            @case('icon')
                <div class="input-group">
                    <span class="input-group-text"><i id="{{ $id }}_ico" class="fa-solid fa-question"></i></span>
                    <input class="form-control{{ $invalid }}" type="text" id="{{ $id }}" name="{{ $name }}" value="{{ $current }}" placeholder="fa-plane-departure" data-icon-preview="#{{ $id }}_ico" autocomplete="off" @required($required)>
                </div>
                @break

            @default
                <input class="form-control{{ $invalid }}" type="{{ in_array($type, ['email', 'url', 'number']) ? $type : 'text' }}" id="{{ $id }}" name="{{ $name }}" value="{{ $current }}" @if (! empty($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif @required($required)>
        @endswitch

        @error($name) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    @endif

    @if (! empty($field['help']))
        <div class="form-text">{{ $field['help'] }}</div>
    @endif
</div>
