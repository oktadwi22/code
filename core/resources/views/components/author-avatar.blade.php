@php
    $image = @$author->avatar ? getImage(getFilePath('authorAvatar') . '/' . @$author->avatar) : siteFavicon();
@endphp
<img src="{{ $image }}" class="author-avatar {{ !@$author->avatar ? 'avatar-from-fav' : '' }}" alt="@lang('Author Image')" >
